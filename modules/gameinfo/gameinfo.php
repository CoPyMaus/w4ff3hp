<?php
if (!isset($moduleinfo)) die('Access denied');

if($moduleinfo['position'] == 'left')
{
    $moduleinfo['content'] = $core_tpl->getcontent_box('modules/'.$moduleinfo['modname'].'/'.$moduleinfo['modname'].'_box', $mode='content', true);
    $dbgamequest = $core_db->select('games', '*', 'gid='.$gameid);
    $gameinfo = array(  'gid' => $dbgamequest[0]['gid'],
                        '{gname}' => $dbgamequest[0]['gname'],
                        '{glogo}' => TEMPLATE_PATH.TEMPLATE.'images/gamelogos/'.$dbgamequest[0]['glogo'],
                        '{glink}' => $dbgamequest[0]['glink']
    );
    $moduleinfo['content'] = $core_tpl->simple_replace($moduleinfo['content'], $gameinfo);
    $core_tpl->template_array('string', $moduleinfo['content']);
}
?>