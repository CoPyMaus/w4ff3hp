<?php
class MAILCHECKER
{
	const MX_OK 					= 250;
	const MX_READY 					= 220;
	const MX_NOK					= 550;

	const MX_DEFAULT_PORT 			= 25;

	const MX_CMD_CONNECT			= 'CONNECT';
	const MX_CMD_HELO				= 'HELO HI';
	const MX_CMD_MAIL_FROM			= 'MAIL FROM';
	const MX_CMD_RCPT_TO			= 'RCPT TO';

	const MSG_EMAIL_INVALID			= 'Email address not valid.';
	const MSG_NO_HOST_RECORDS		= 'No host records found.';
	const MSG_NO_MX_FOUND			= 'No mailserver found';
	const MSG_NO_STATUS_CODE		= 'Could not get status code from nameserver.';

	private $_email					= null;

	public $last_mx_command		 	= null;
	public $last_mx_status_code 	= null;
	public $last_error_message		= null;
	public $substance				= 0; 	// defines how reliable the result is

	/**
	 * Set email address
	 *
	 * @param $email email address
	 * @return void
	 */

	public function setEmail( $email )
	{
		$this->_email 				= $email;

		$this->last_mx_command 		= null;
		$this->last_mx_status_code 	= null;
		$this->last_error_message	= null;
		$this->substance 			= 0;
	}

	/**
	 * Check if given email is valid and exists
	 * If mailserver responses 250 to "RCTP TP <$email>"
	 *
	 * @return bool true if valid and exists else false (check $this->substance afterwards)
	 */

	public function checkIfExists()
	{
		// check if $this->email is valid (syntax)

		if ( !$this->_checkIfValid() )
		{
			$this->last_error_message	= self::MSG_EMAIL_INVALID;
			$this->substance			= 1.0;
			return false;
		}

		// get hostname from email

		list( $address, $hostname ) = explode( '@', $this->_email );

		// check if domain is registered (no host records mean not registered)

		if ( count( dns_get_record( $hostname ) ) === 0 )
		{
			$this->last_error_message 	= self::MSG_NO_HOST_RECORDS;
			$this->substance			= 1.0;
			return false;
		}

		// get mailserver for hostname

		$mailserver = self::_getMailserverForHostname( $hostname );

		if ( !$mailserver )
		{
			$this->last_error_message 	= self::MSG_NO_MX_FOUND;
			$this->substance			= 0.5;
			return false;
		}

		// "CONNECT" to mailserver

		if ( $fsocket = fsockopen( $mailserver[0], self::MX_DEFAULT_PORT ) )
		{
			$matches	= array();
			$buffer 	= trim( fread( $fsocket, 4096 ) );

			$this->last_mx_command = self::MX_CMD_CONNECT;

			if ( preg_match('/^([0-9]+).+/i', $buffer, $matches) )
			{
				$this->last_mx_status_code 	= $matches[1];

				if ( (int)$matches[1] !== self::MX_READY )
				{
					$this->substance = 0.25;
					return false;
				}

				// send self::MX_CMD_HELO command to mailserver

				$status_code 				= self::_sendCommand( self::MX_CMD_HELO, $fsocket );
				$this->last_mx_command		= self::MX_CMD_HELO;
				$this->last_mx_status_code 	= $status_code;

				if ( $status_code !== self::MX_OK )
				{
					$this->substance = 0.5;
					return false;
				} 

				// send self::MX_CMD_MAIL_FROM command to mailserver

				$status_code 				= self::_sendCommand( self::MX_CMD_MAIL_FROM . ': <test@example.org>', $fsocket );
				$this->last_mx_command		= self::MX_CMD_MAIL_FROM;
				$this->last_mx_status_code 	= $status_code;

				if ( $status_code !== self::MX_OK )
				{
					$this->substance = 0.5;
					return false;
				}

				// send self::MX_CMD_RCPT_TO command to mailserver

				$status_code 				= self::_sendCommand( self::MX_CMD_RCPT_TO . ': <' . $this->_email . '>', $fsocket );
				$this->last_mx_command		= self::MX_CMD_RCPT_TO;
				$this->last_mx_status_code 	= $status_code;

				switch ( $status_code )
				{
					case self::MX_OK:

						$this->substance = 1.0;
						return true;

					case self::MX_NOK:

						$this->substance = 1.0;
						return false;

					default:

						$this->substance = 0.5;
						return false;
				}	
			}
			else
			{
				$this->substance 			= 0.1;
				$this->last_error_message 	= self::MSG_NO_STATUS_CODE;
			}
		}

		return false;
	}

	/**
	 * Check if syntax of given email address is valid
	 *
	 * @return bool true if valid else false
	 */

	private function _checkIfValid()
	{
		return ( filter_var( $this->_email, FILTER_VALIDATE_EMAIL ) !== false )? true : false;
	}

	/**
	 * Get mailserver for a hostname (like nslookup -q=mx $hostname)
	 *
	 * @param $hostname the hostname to get nameserver for
	 * @return (mixed|bool) Array of nameserver or false
	 */

	private static function _getMailserverForHostname( $hostname )
	{
		$mailserver = array();

		if ( getmxrr($hostname, $mailserver) )
		{
			return $mailserver;
		}

		return false;
	}

	/**
	 * Send command to open fsocket
	 *
	 * @param $command the command to execute ("\r\n" will be appended)
	 * @param $fsocket the socket on which you want to execute $command
	 * @return (int|bool) status code or false on error
	 */

	private static function _sendCommand( $command, $fsocket )
	{
		$matches = array();

		if ( !$fsocket )
			return false;

		fwrite( $fsocket, $command . "\r\n" );

		$buffer = trim( fread( $fsocket, 4096 ) );

		if ( preg_match('/^([0-9]+).+/i', $buffer, $matches ) )
			return (int) $matches[1];

		return false;
	}

}
?>