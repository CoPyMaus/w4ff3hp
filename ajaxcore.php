<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


require_once ('includes/autoload.php');
$core = new CORE;
$core_db = new COREDB;
$core_tpl = new CORETPL;

//Remove all old or inactive Guest Sessions
$core->delete_sessiontimeouts();


$userinfo = array('id' => 0);

//Load config from database
$config_from_db = $core_db->select('config', '*');
for ($i=0; $i < count ($config_from_db); $i++)
    define($config_from_db[$i]['config_name'], $config_from_db[$i]['value']);

//Check if given gid and set to variable $gameid. Is not set it, $gameid becomes false
$gameid = (isset($_GET['gid']) && $_GET['gid'] > 0) ? $_GET['gid'] : false;
if(!$gameid && isset($_POST['gid']) && $_POST['gid'] > 0) $gameid = $_POST['gid'];

//generate session from cookie
if (isset($_COOKIE[COOKIE_PREFIX.'_session']) && !empty($_COOKIE[COOKIE_PREFIX.'_session']))
    session_id($_COOKIE[COOKIE_PREFIX.'_session']);

//start session (old session from cookie or new session
session_start();

//set cookie with session
setcookie(COOKIE_PREFIX."_session", session_id());

//check if session in database
$dbsession = $core_db->select('sessions', 'sslastaction', "sskey='".session_id()."'");
if (isset($dbsession[0]['sslastaction']))
{
    //Session exist in database. Update timestamp and get userinfo
    $core_db->update('sessions', $fields = array('sslastaction'), $values = array(time()), "sskey='" . session_id() . "'");
    $core->get_userinfo_from_session(session_id());
}
else
    $core_db->insert('sessions', $fields = array('uid', 'sskey', 'sslastaction'), $values = array($userinfo['id'], session_id(), time())); //Session is not in database. Write id into sessions



if (!$gameid)
    require_once ('gameselect.php');
else {
    if (isset($_GET['m']) && !empty($_GET['m'])) {
        $site = $_GET['m'] . '.php';
        //$check = $core->check_quested_site($site);
        if ($site = $core->check_quested_site($site)) {
//            $moduleinfo = $_GET['m'];
            require_once($site);
            /////////////////////////////////////////////////////////////////////////////////////////
            //$core->get_modules();
            /////////////////////////////////////////////////////////////////////////////////////////
        } else {
            $core_tpl->template = "<h2 style='text-align: center; color: #FF6666'>ERROR: Es wurde keine gültige Seite aufgerufen!</h2>";
        }
    }
}

echo $core_tpl->template;

?>