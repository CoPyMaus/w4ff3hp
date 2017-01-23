<?php
/*
 * sitebox.php
 * Abfrage der Datenbank auf module in der linken Seitenbox
 * Überprüfen ob das jeweilige Modul existiert
 * Einbinden der Module und vorbereiten für die Ausgabe
 */
//$core_tpl->template_array('string', '<table><tr><td><table><tr><td>');

$sqlselect = $core_db->select('modules', '*', 'module_active=1 AND module_position = "left"', 'module_sortid ASC');

foreach ($sqlselect as $new_array)
{
//echo "<pre>";print_r($new_array);echo "</pre>";
    $moduleinfo['position'] = $new_array['module_position'];
    $moduleinfo['modname'] = $new_array['module_name'];
    if (file_exists('modules/'.$moduleinfo['modname'].'/'.$moduleinfo['modname'].".php")) {
        require_once('modules/' . $moduleinfo['modname'] . '/' . $moduleinfo['modname'] . ".php");
    }
    elseif (file_exists('modules/'.$moduleinfo['modname'].'/'.$moduleinfo['modname'].".tpl"))
        $core_tpl->template_array('file', 'modules/'.$moduleinfo['modname'].'/'.$moduleinfo['modname'].".tpl");
    unset($moduleinfo);
}

//echo serialize($test = array('1' => 1, '2' => 1, '3' => 1));

//$core_tpl->template_array('string', '</td></tr></table></tr></td></table>')
?>