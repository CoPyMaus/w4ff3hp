<?php
if ( !class_exists('CORE') )
{
	class CORE
	{
		public function __construct()
        {
            $this->debug_messages = array();
            global $core_db;
            $this->core_db = &$core_db;
        }

        public function __destruct()
        {
            unset($this->db);
        }

        public function delete_sessiontimeouts($minutes = 10)
        {
            $oldtime = time() - ($minutes * 60);
            $this->core_db->remove('sessions', 'uid=0 AND sslastaction < '.$oldtime);
        }

        public function show_debug($array)
        {
            echo '<pre>';
            print_r($array);
            echo '</pre>';
        }

        public function set_debug_message($message)
        {
            $this->debug_messages[] = $message;
        }

        public function get_userinfo_from_session($session)
        {
            global $userinfo;
            $userinfo['session'] = $session;
            $gufs_db = $this->core_db->select('sessions', 'uid', "sskey ='".$session."'");
            if (isset($gufs_db[0]['uid']))
            {
                $userinfo['id'] = $gufs_db[0]['uid'];
                $this->get_userinfo_from_id();
            }
        }

        public function get_userinfo_from_id()
        {
            global $userinfo;
            if (isset($userinfo['id']) && (int)$userinfo['id'] > 0)
            {
                $gufid_db = $this->core_db->select('user', '*', 'uid='.$userinfo['id']);
                if (isset($gufid_db[0]['uid']))
                {
                    foreach ($gufid_db[0] as $key => $value)
                    {
                        if ($key != 'password' && $key != 'uid' && $key != 'game_accessgroup')
                            $userinfo[$key] = $value;
                        elseif ($key == 'game_accessgroup' && $value != 'NULL') {
                            $userinfo['game_accessgroup'] = unserialize($value);
                            $this->get_access_by_groups();
                        }

//  $this->show_debug($this->usergroups);
                    }
//  $this->show_debug($userinfo);
                    $this->update_session();
                }
            }
        }

        public function check_quested_site($site)
        {
            if (file_exists($site)) {
                return($site);
            }
            else {
                list($filename, $fileext) = explode(".", $site);
                if(file_exists('modules/'.$filename.'/'.$site))
                    return ('modules/'.$filename.'/'.$site);
                else
                    return (false);
            }
            return(false);
        }

        private function get_access_by_groups()
        {
            global $userinfo;
            if (!isset($userinfo['game_accessgroup'])) return false;

            $groups = $this->core_db->select('groups', '*', '', 'gr_id ASC');
 // $this->show_debug($groups);
            $groupsaccess = $this->core_db->select('groupsaccess', '*', '', 'gra_id ASC');
 //$this->show_debug($groupsaccess);
            foreach ($userinfo['game_accessgroup'] as $gameid => $level)
            {
                if ($level)
                {
                    $userinfo['groupaccess'][$gameid]['group_title'] = $groups[$level - 1]['gr_name'];
                    $access_array = unserialize($groups[$level - 1]['gr_access']);
 //$this->show_debug($access_array);
                    foreach ($access_array as $ac_key => $ac_value)
                    {
                        $userinfo['groupaccess'][$gameid][$groupsaccess[$ac_key]['gra_name']] = ($ac_value) ? true : false;
                    }

                }
            }
            unset ($userinfo['game_accessgroup']);
        }



        public function update_session()
        {
            global $userinfo;
            if (!isset($userinfo['id']) || empty($userinfo['id']) || !isset($userinfo['session']) || empty($userinfo['session']))
                die('FATAL ERROR in $core->update_session(): Cannot modify session! No userid or sessionid given');
            if ($this->core_db->select('sessions', '*', "sskey='".$userinfo['session']."'")) {
                $this->core_db->update('sessions', $fields = array('uid', 'sslastaction'), $values = array($userinfo['id'], time()), "sskey='" . $userinfo['session'] . "'");
                $this->core_db->remove('sessions', "sskey != '".$userinfo['session']."' AND uid = ".$userinfo['id']);
            }
            else
            {
                $fields = array ('uid', 'sskey', 'sslastaction');
                $values = array($userinfo['id'], $userinfo['session'], time());
                $this->core_db->insert('sessions', $fields, $values);
            }

        }

        public function get_modules()
        {
            $core_modules_dir = 'modules/';
            $d = @dir($core_modules_dir) or die("$core->get_modules: Failed to opening ".$core_modules_dir." for reading!");
            while(false !== ($entry = $d->read()))
            {
                if($entry[0] == ".") continue;
                if(is_dir($core_modules_dir.$entry))
                {
                    $retval[] = array (
                        "name" => $core_modules_dir.$entry."/",
                        "type" => filetype($core_modules_dir.$entry),
                        "size" => 0,
                        "lastmod" => filemtime($core_modules_dir.$entry)
                    );
                    if(is_readable($core_modules_dir.$entry.'/'))
                    {
                        $d2 = @dir($core_modules_dir.$entry.'/');
                        while(false !== ($fileentry = $d2->read()))
                        {
                            if($fileentry[0] == ".") continue;
                            $retval[] = array(
                                "name" => $core_modules_dir.$entry.'/'.$fileentry,
                                "type" => mime_content_type($core_modules_dir.$entry.'/'.$fileentry),
                                "size" => filesize($core_modules_dir.$entry.'/'.$fileentry),
                                "lastmod" => filemtime($core_modules_dir.$entry.'/'.$fileentry)
                            );
                        }
                    }
                }
            }
            $d->close();
 //           $this->show_debug($retval);
            //$this->core_modules = scandir();
        }
	}
}
?>