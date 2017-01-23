<?php

$functionmode = (isset($_GET['f']) && !empty($_GET['f'])) ? $_GET['f'] : false;

function get_contentdata()
{
    global $moduleinfo;
    global $core_tpl;
    global $gameid;
    if (file_exists('modules/' . $moduleinfo['modname'] . '/' . $moduleinfo['modname'] . ".php")) {
        require_once('modules/' . $moduleinfo['modname'] . '/' . $moduleinfo['modname'] . ".php");
    } elseif (file_exists('modules/' . $moduleinfo['modname'] . '/' . $moduleinfo['modname'] . ".tpl"))
        $core_tpl->template_array('file', 'modules/' . $moduleinfo['modname'] . '/' . $moduleinfo['modname'] . ".tpl");
    unset($moduleinfo);
}

if ($functionmode)
{
    $sqlselect = $core_db->select('modules', '*', 'module_active=1 AND module_position = "content" AND module_name = "'.$functionmode.'"');
    if(isset($sqlselect[0]['module_id']) && (int)$sqlselect[0]['module_id'] > 0 )
    {
        $moduleinfo['position'] = $sqlselect[0]['module_position'];
        $moduleinfo['modname'] = $sqlselect[0]['module_name'];
        get_contentdata();
    }
}
elseif (!$functionmode)
{
    $sqlselect = $core_db->select('modules', '*', 'module_active=1 AND module_position = "contentbox"', 'module_sortid ASC');

    foreach ($sqlselect as $new_array) {
        //echo "<pre>";print_r($new_array);echo "</pre>";
        $moduleinfo['position'] = $new_array['module_position'];
        $moduleinfo['modname'] = $new_array['module_name'];
        get_contentdata();
    }
}
else
    die('Ungültiger Aufruf - Access denied');
?>