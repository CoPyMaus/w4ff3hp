<?php
//Generate gameselect page
$dbanswer = $core_db->select('games', '*', '', 'gid ASC');
$selectid = 1;
$content_snippet = '';

for ($i = 0; $i < count($dbanswer); $i++)
{
    $mainpage_content = $core_tpl->getcontent('gameselect', 'site', true);
    $snippet = $core_tpl->getcontent('gameselect', 'snippet', true);

    if ($selectid == 1)
        $selectid_css = 'cidbox_left';
    else
        $selectid_css = 'cidbox_float_left';

    $replacearray = array(  '[gid]' => $dbanswer[$i]['gid'],
        '[gname]' => utf8_encode($dbanswer[$i]['gname']),
        '[gimage]' => TEMPLATE_PATH.TEMPLATE."images/".$dbanswer[$i]['gimage'],
        '[glink]' => utf8_encode($dbanswer[$i]['glink']),
        '[glinkname]' => utf8_encode($dbanswer[$i]['glinkname']));

    $mainpage_snippet[] = $core_tpl->simple_replace($snippet, $replacearray);

    if (defined('GAMESELECTROW') && $selectid < GAMESELECTROW)
        $selectid++;
    else
    {
        if (is_array($mainpage_snippet) && count($mainpage_snippet) > 0)
            $content_snippet .= (count($mainpage_snippet > 1) ? implode("\r\n", $mainpage_snippet) : $mainpage_snippet[0]);
        unset($mainpage_snippet);
        $mainpage_snippet = array();
        $selectid = 1;
    }
}
$core_tpl->template = str_replace('{gameselect}', $content_snippet, $mainpage_content);
?>