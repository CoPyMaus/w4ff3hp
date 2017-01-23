<?php
if (!isset($moduleinfo)) die('Access denied');
//$core->show_debug($_POST);
//Check if set username and password
if (isset($_POST['username']) && isset($_POST['hashpw']) && (isset($_POST['password']) && empty($_POST['password'])))
{
    $l_username = $_POST['username'];
    $l_password = md5($_POST['hashpw']);
    $l_dbuser = $core_db->select('user', 'uid, acc_active', 'username="'.$l_username.'" AND password="'.$l_password.'"');
	if (isset($l_dbuser[0]['acc_active']) && $l_dbuser[0]['acc_active'] != 0)
	{
    	if (isset($l_dbuser[0]['uid']) && $l_dbuser[0]['uid'] > 0)
		{
        	$userinfo['id'] = $l_dbuser[0]['uid'];
        	$core->get_userinfo_from_id();
    	}
	}
	elseif (isset($l_dbuser[0]['uid']))
	{
		$logininfo = array(	'{registertitle}' => 'Login fehlgeschlagen',
								'{registerinfo}' => 'Ihre Registrierung wurde noch nicht vollständig abgeschlossen. Bitte überprüfen Sie Ihre eMails, um die Registrierung zu vervollständigen!<br />Sollten Sie keine Nachricht bekommen oder anderweitig Probleme mit der Registrierung haben, wenden Sie sich bitte an einen Admin dieser Seite.');
		$login_infobox = $core_tpl->getcontent_box('modules/login/login_infobox', true);
		$login_infobox = $core_tpl->simple_replace($login_infobox, $logininfo);
		echo $login_infobox;
		exit();
	}
}

if (!isset($userinfo) || empty($userinfo['id']) || $userinfo['id'] == 0)
{
    // Generate Login Box
    if($moduleinfo['position'] == 'left')
    {
        $moduleinfo['content'] = $core_tpl->getcontent_box('modules/'.$moduleinfo['modname'].'/'.$moduleinfo['modname'].'_box', true);
        $logininfo = array(  '{gid}' => $gameid);
        $moduleinfo['content'] = $core_tpl->simple_replace($moduleinfo['content'], $logininfo);
        $core_tpl->template_array('string', $moduleinfo['content']);
    }
}
?>