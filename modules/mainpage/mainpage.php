<?php
if (!defined('PAGENAME')) die ('access denied');

if ( (isset($_GET['gid']) && !empty($_GET['gid'])) || (isset($_POST['gid']) && !empty($_POST['gid'])) )
    $mainpage_gid = (isset($_POST['gid'])) ? $_POST['gid'] : $_GET['gid'];
else
    $mainpage_gid = false;

$mainpage_content = false;
$mainpage_snippet = array();
$snippet = false;

if (!isset($core_db))
    global $core_db;

if (!isset($core_tpl))
    global $core_tpl;


if ($mainpage_gid)
{
echo "test";
}
else
{

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
                                '[gimage]' => TEMPLATE_PATH.TEMPLATE."images/gamebanner/".$dbanswer[$i]['gimage'],
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
    $core_tpl->content_replace = str_replace('{gameselect}', $content_snippet, $mainpage_content);
    //$core_tpl->content_replace = $mainpage_content;
    //echo $mainpage_content;
}
?>