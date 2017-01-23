<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($moduleinfo)) die('Access denied');

//echo '<pre>'; print_r($_POST); echo '</pre>';

if (!isset($return_array['message']) || !is_array($return_array['message']))
	$return_array['message'] = array();

function set_errormessage($message)
{
	global $return_array;		
	$return_array['message'][] = $message;
}

function get_errormessage()
{
	global $return_array;
	if (isset($return_array['message']) && is_array($return_array['message']))
	{
		$message = (count($return_array['message'] > 1)) ? implode("<hr>\r\n", $return_array['message']) : $return_array['message'][0];
		return($message);
	}
	return false;
}

$core_db = new COREDB;

if (isset($_GET['token']))
{
	$token = "'".$_GET['token']."'";
	$token_query = $core_db->select('user', 'uid, acc_active', 'regkey='.$token);
	if ($token_query)
	{
		$uid = $token_query[0]['uid'];
		if ($token_query[0]['acc_active'] == 0)
		{
			$fields[] = 'acc_active';
			$values[] = 1;
			$query = $core_db->update('user', $fields, $values, 'uid='.$uid);
			if ($query)
				$registerinfo = array(	'{registertitle}' => 'Freischaltung erfolgreich!',
										'{registerinfo}' => 'Ihr Benutzerkonto konnte erfolgreich aktiviert werden. Sie können sich nun mit Benutzername und das von Ihnen gesetze Passwort einloggen!');
			else
				$registerinfo = array(	'{registertitle}' => 'Freischaltung nicht erforderlich',
										'{registerinfo}' => 'Ihr Benutzerkonto wurde bereits aktiviert. Bitte loggen Sie sich mit Ihrem Benutzernamen und Passwort ein!');
		}
	}
	else
		$registerinfo = array(	'{registertitle}' => 'Registrierung fehlgeschlagen',
								'{registerinfo}' => 'Der Authentifiezierungstoken konnte nicht gefunden werden. Womöglich wurde Ihr Account bereits entfernt!');	
	
	$register_infobox = $core_tpl->getcontent_box('modules/register/register_infobox', true);
	$register_infobox = $core_tpl->simple_replace($register_infobox, $registerinfo);
	echo $register_infobox;
		
	exit();		
}

$username = (isset($_POST['reg_username']) && !empty($_POST['reg_username'])) ? $_POST['reg_username'] : false;
if ($username)
{
	$dbusername_check = $core_db->select('user', 'username', "username = '".$username."'");
	if ($dbusername_check[0]['username'] == $username)
		set_errormessage("Username steht nicht zur Verfügung!");
}
elseif (isset($_POST['reg_username']))
	set_errormessage("Es muss ein Username angegeben werden");

if (isset($_POST['reg_password']) && !empty($_POST['reg_password']))
{
	if (isset($_POST['reg_password_verification']) && !empty($_POST['reg_password_verification']))
	{
		if ($_POST['reg_password'] == $_POST['reg_password_verification'])
		{
			if (isset($_POST['reg_hashpw']) && !empty($_POST['reg_hashpw']))
			{
				$hashpw = $_POST['reg_hashpw'];
			}
			else
				set_errormessage("UUps, Ihr Browser hat das Passwort nicht verschlüsselt übertragen. Bitte versuchen Sie es noch einmal!");
		}
		else
			set_errormessage("Fehler bei der Passwortprüfung. Bitte erenut eingeben");
	}
	else
		set_errormessage("Das Passwort muss 2x angegeben werden, um Schreibfehler vorzubeugen!");
}
elseif (isset($_POST['reg_password']))
	set_errormessage("Prima, wenn sie jedem den Zugang erlauben möchten. Zu Ihrer Sicherheit geben Sie bitte 2x ein Password an!");
		
$reg_email = (isset($_POST['reg_email']) && !empty($_POST['reg_email'])) ? $_POST['reg_email'] : false;
if ($reg_email)
{
	$mc = new MAILCHECKER;
	$mc->setEmail($reg_email);
	$mc->checkIfExists();
	$maildata = get_object_vars($mc);
//	echo "<pre>"; print_r($maildata); echo "</pre>";
	if (!empty($maildata['last_error_message']))
		set_errormessage("Email ist <b>nicht</b> gültig");
	else
	{
		$dbemail_check = $core_db->select('user', 'email', "email = '".$reg_email."'");
		if ($dbemail_check[0]['email'] == $reg_email)
			set_errormessage("eMail- Adresse ist bereits in Verwendung!");			
	}
}

$rerror = get_errormessage();

if (!$rerror && $username && $hashpw && $reg_email)
{
	$mailkey = md5(uniqid());
    $fields = array('username', 'password', 'email', 'game_accessgroup', 'regkey', 'user_regdate');
    $values = array($username, md5($hashpw), $reg_email, 'a:3:{i:1;i:5;i:2;i:5;i:3;i:5;}', $mailkey, time());
    if ($core_db->insert('user', $fields, $values))
	{
		if (file_exists('modules/register/register_email.html'))
		{
			$mailertext = file_get_contents("modules/register/register_email.html");
			$mailertext = str_replace('$username', $username, $mailertext);
			$mailertext = str_replace('$mailkey', $mailkey, $mailertext);
			$mail = new MAILER;

			//$mail->isMail();
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'service.raumwaffe@gmail.com';
			$mail->Password = 'W0rk1ng5';
			$mail->Port = 587;
			$mail->SMTPDebug = 0;
			
			$mail->setFrom('service.raumwaffe@gmail.com', 'Raumwaffen Netzwerk');
			$mail->addReplyTo('service.raumwaffe@gmail.com', 'Raumwaffen Netzwerk');
			
			$mail->addAddress($reg_email);

			$mail->Subject = "Freischaltung Ihres Accounts (Raumwaffen Netzwerk)";
			$mail->isHTML(true);
			$mail->msgHTML($mailertext);
			//$mail->Body    = nl2br($mailertext);
			$mail->AltBody = strip_tags($mailertext);
			if(!$mail->send())
			{
     			//$mail->Send() liefert FALSE zurück: Es ist ein Fehler aufgetreten
				$registerinfo = array(	'{registertitle}' => 'Registrierung fehlgeschlagen',
										'{registerinfo}' => 'Uups, die Bestätigungsemail konnte Ihnen nicht zugesand werden. Bitte informieren Sie einen Administrator, um die Freischaltung zu veranlassen!<br /><br />'."Fehler: " . $mail->ErrorInfo);
  			}
  			else
  			{
     			//$mail->Send() liefert TRUE zurück: Die Email ist unterwegs
				$registerinfo = array(	'{registertitle}' => 'Registrierung erfolgreich',
										'{registerinfo}' => '<h2>Es wurde eine eMail mit einem Aktivierungslink versendet. Um weiter machen zu können, überprüfen Sie ihre Nachrichten.<br />In Einzelfällen und je nach Anbieter, kann es passieren, das die eMail im Spam Postfach landet. Bitte prüfen sie auch dort!</h2>');
  			}
		}
	}
	//else
    
	
	$register_infobox = $core_tpl->getcontent_box('modules/register/register_infobox', true);
	$register_infobox = $core_tpl->simple_replace($register_infobox, $registerinfo);
	echo $register_infobox;
	unset($registerinfo);
	exit();
}

$moduleinfo['content'] = $core_tpl->getcontent_box('modules/register/register', true);


$registerinfo = array(  '{gid}' => $gameid,
                        '{registererror}' => ($rerror) ? '<div style="background-color: red; color: #000000;">'.$rerror.'</div>' : '&nbsp;',
                        '{reg_username}' => $username,
                        '{reg_email}' => $reg_email);
$moduleinfo['content'] = $core_tpl->simple_replace($moduleinfo['content'], $registerinfo);
$core_tpl->template_array('string', $moduleinfo['content']);

?>