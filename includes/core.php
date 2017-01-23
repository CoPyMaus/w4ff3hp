<?php
require_once ('includes/autoload.php');
$core = new CORE;
$core_db = new COREDB;
$core_tpl = new CORETPL;

//Remove all old or inactive Guest Sessions
$core->delete_sessiontimeouts();

//Load config from database
$config_from_db = $core_db->select('config', '*');
for ($i=0; $i < count ($config_from_db); $i++)
    define($config_from_db[$i]['config_name'], $config_from_db[$i]['value']);

//generate session
if (isset($_COOKIE[COOKIE_PREFIX.'_session']) && !empty($_COOKIE[COOKIE_PREFIX.'_session'])) {
    session_id($_COOKIE[COOKIE_PREFIX.'_session']);
//    echo "Alte  Session wieder hergestellt!";
}
session_start();
setcookie(COOKIE_PREFIX."_session", session_id());

//Check is set token and register for authenticate registration
if (isset($_GET['f']) && $_GET['f'] == 'register' && isset($_GET['token']) && !empty($_GET['token']))
	require_once('content.php');


//echo TEMPLATE;
//echo '<pre>'; print_r($config_from_db); echo '</pre>';




//define ('TEMPLATE', 'w4ff3');
//define ('TEMPLATE_PATH', 'styles'.'/'.TEMPLATE.'/');
$core_tpl->load_template();
echo $core_tpl->template;
?>