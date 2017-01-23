<?php
if ( !class_exists ( 'COREDB' ) ) {
    class COREDB
    {
        public function __construct ()
        {
            if ( !defined ( 'DBPASS' ) )
                require_once ( 'includes/dbconfig.php' );
            if ( !defined ( 'DBUSER' ) || !defined ( 'DBPASS' ) || !defined ( 'DBHOST' ) || !defined ( 'DBNAME' ) )
                die ( 'Check the database configuration' );

            $this->dbuser = DBUSER;
            $this->dbpass = DBPASS;
            $this->dbhost = DBHOST;
            $this->dbname = DBNAME;
            $this->dbport = DBPORT;
            $this->query = '';
            $this->result = '';

            if ( !isset( $this->db ) ) {
                $this->connect ();
            }
        }

        public function __destruct ()
        {
            $this->db->close ();
            unset( $this->db );
        }

        protected function connect ()
        {
            if ( $this->dbhost != 'localhost' )
                $this->dbhost = $this->dbhost . ":" . $this->dbport;
            $this->db = new mysqli( $this->dbhost, $this->dbuser, $this->dbpass, $this->dbname );
            if ( $this->db->connect_error ) {
                die( 'Connect Error (' . $this->db->connect_errno . ') ' . $this->db->connect_error );
            }
        }

        private function query ()
        {
            $result = $this->db->query ( $this->query );

            if ( $this->db->error ) {
                die( 'Connect Error (' . $this->db->errno . ') ' . $this->db->error );
            }

            $this->result = $result;
            unset( $result );
        }

        public function select ( $table, $fields, $where = '', $order = '' )
        {
            if ( empty( $table ) || empty( $fields ) || ( is_array ( $fields ) && empty( $fields[0] ) ) )
                return false;

            mb_internal_encoding ( 'UTF-8' );
            if ( !is_array ( $fields ) && ( strrpos ( $fields, "," ) !== false || strrpos ( $fields, "`" ) !== false ) ) {
                $fields = str_replace ( ", ", ",", $fields );
                $fields = str_replace ( "`", "", $fields );
                $fields = explode ( ",", $fields );
            }

            if ( is_array ( $fields ) ) {
                for ( $i = 0; $i < count ( $fields ); $i++ ) {
                    if ( $fields[$i] == '*' || ( mb_substr ( $fields[$i], 0, 1 ) == '`' && mb_substr ( $fields[$i], -1, 1 ) == '`' ) )
                        $db_fields[] = $fields[$i];
                    else
                        $db_fields[] = $this->prepare_field ( $fields[$i] );
                }

                if ( isset( $db_fields ) ) {
                    $fields = ( count ( $db_fields ) > 1 ) ? implode ( ",", $db_fields ) : $db_fields[0];
                }
            }

            if ( mb_substr ( $table, 0, 1 ) != '`' && mb_substr ( $table, -1, 1 ) != '`' )
                $table = '`' . PREFIX . $table . '`';

            $dborder = '';
            if ( !empty( $where ) )
                $dborder .= ' WHERE ' . $where;
            if ( !empty( $order ) )
                $dborder .= ' ORDER BY ' . $order;
            $this->query = "SELECT " . $fields . " FROM " . $table . $dborder;
            unset( $table, $fields, $where, $order, $dborder, $db_fields );
/////////			echo $this->query."<br />\r\n";
            $this->query ();

            if ( $this->result !== false && $this->result->num_rows > 0 ) {
                while ( $row = $this->result->fetch_assoc () )
                    $results[] = $row;
                $this->clear ();
                return $results;
            }
            $this->clear ();
            return false;
        }

        public function is_indb( $table, $fields, $where = '', $order = '')
        {
            $iidb_query = $this->select($table, $fields, $where, $order);
            if (count($iidb_query[0] > 0))
                return true;
            else
                return false;
        }

        public function insert ( $table, $fields, $values )
        {
            $this->prepare_fields ( $fields );
            $this->prepare_values ( $values );
            $this->table = $this->prepare_field ( PREFIX . $table );
            $this->query = "INSERT INTO " . $this->table . " (" . $this->insertfields . ") VALUES (" . $this->insertvalues . ")";
			$this->query();
			//die($this->db->insert_id());
			$iid = mysqli_insert_id($this->db);
            return ($iid);

        }

        public function remove ( $table, $where )
        {
            $this->table = $this->prepare_field ( PREFIX . $table );
            $this->query = "DELETE FROM ".$this->table." WHERE ".$where;
            $this->query();
        }

        public function update ( $table, $fields = array(), $values = array(), $where = false )
        {
            if ( count ( $fields ) != count ( $values ) )
                die( 'FATAL ERROR in $core_db->update: count of fields and values are not identical' );

            for ( $i = 0; $i < count ( $fields ); $i++ ) {
                $update_field = $this->prepare_field ( $fields[$i] );
                $update_value = $this->prepare_value ( $values[$i] );
                $update_array[] = $update_field . " = " . $update_value;
            }
            if ( isset( $update_array ) ) {
                $update = ( count ( $update_array ) > 1 ) ? implode ( ", ", $update_array ) : $update_array[0];
                $this->table = $this->prepare_field ( PREFIX . $table );
                $this->query = 'UPDATE ' . $this->table . ' SET ' . $update;
                if ( $where )
                    $this->query .= ' WHERE ' . $where;
				$this->query ();
                if($this->db->affected_rows)
					return true;
				else
					return false;
            }
        }

        private function prepare_values ( $values )
        {
            if ( is_array ( $values ) ) {
                foreach ( $values as $select )
                    $valuesarray[] = $this->prepare_value ( $select );
            } else
                $this->insertvalues = $this->prepare_value ( $values );

            if ( isset( $valuesarray ) )
                $this->insertvalues = ( count ( $valuesarray ) > 1 ) ? implode ( ', ', $valuesarray ) : $valuesarray[0];
        }

        private function prepare_value ( $value )
        {
            if ( $value == 'NOW()' )
                return ( "'" . $value . "'" );
            elseif ( is_numeric ( $value ) )
                return ( $value );
            else {
                $value = "'" . $this->db->real_escape_string ( $value ) . "'";
                return ( $value );
            }
        }

        private function prepare_fields ( $fields )
        {
            if ( is_array ( $fields ) ) {
                foreach ( $fields as $select )
                    $fieldsarray[] = $this->prepare_field ( $select );
            } else
                $this->insertfields = $this->prepare_field ( $fields );

            if ( isset( $fieldsarray ) )
                $this->insertfields = ( count ( $fieldsarray ) > 1 ) ? implode ( ', ', $fieldsarray ) : $fieldsarray[0];
        }

        private function prepare_field ( $field )
        {
            return ( '`' . $field . '`' );
        }

        private function clear ()
        {
            $this->query = '';
            $this->result = '';
        }
    }
}
?>