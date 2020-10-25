<?php

# ----------------------------------------------------------------------
# out-of-date events
# ----------------------------------------------------------------------
//  case"outof":

//    $day = date("d");
//    if (substr($day,0,1) == "0") $day = substr_replace ($day,'', 0, 1);

$day = date('j');
$month = date('n');
$year = date('Y');

$query = 'select id,title,cat_name,day,month,year from ' . XOOPS_DB_PREFIX . '_agendax_events left join ' . XOOPS_DB_PREFIX . '_agendax_cat on ' . XOOPS_DB_PREFIX . '_agendax_events.cat=' . XOOPS_DB_PREFIX . '_agendax_cat.cat_id ';
$query .= "where ((month='$month' and year<='$year') || (month<'$month' and year<='$year')) order by day,month,year ASC";
$result = $GLOBALS['xoopsDB']->queryF($query);
$rows = $GLOBALS['xoopsDB']->getRowsNum($result);

$output_str .= '<a href=index.php?op=delalloodev>' . _MA_AGXDEALLEXPEVT . " !</a><br><br>\n";
$foo = '';
while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
    $foo++ % 2 ? $color = 'BBBBBB' : $color = 'EEEEEE';

    if ((($row->day < $day) && ($row->month = $month) && ($row->year = $year)) || ($row->month < $month)) {
        $output_str .= "<table border=1 bgcolor=$color cellspacing=0 cellpadding=4 width=\"100%\">\n";

        $output_str .= "<tr><td>\n<li><b>" . stripslashes($row->title) . '</b> ' . _MA_AGXON . ' ' . $row->day . ' ' . $maand[$row->month] . ' ' . $row->year . "\n";

        $output_str .= ' - ' . _MA_AGXCATEGORY . ' : ' . $row->cat_name . "\n";

        $output_str .= ' - <a href=index.php?op=view&id=' . $row->id . '>' . _MA_AGXEVTVIEW . "</a>\n";

        $output_str .= ' - <a href=index.php?op=edit&id=' . $row->id . '>' . _MA_AGXEDIT . "</a>\n";

        $output_str .= ' - <a href=index.php?op=delev&id=' . $row->id . '>' . _MA_AGXEVTDEL . "</a>\n";

        $output_str .= "</td></tr>\n";

        $output_str .= "</table>\n";
    }
}
