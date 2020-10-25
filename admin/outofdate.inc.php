<?php

// ------------------------------------------------------------------------- //
//                           Agendax-X for Xoops                             //
//                              Version:  2.0                                //
// ------------------------------------------------------------------------- //
// Author: Wang Jue (alias wjue)                                             //
// Purpose: Very Flexible Calendar and Event Module                          //
// email: wjue@wjue.org                                                      //
// URLs: http://www.wjue.org,  http://www.guanxiCRM.com                      //
//---------------------------------------------------------------------------//
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//---------------------------------------------------------------------------//
// Part of this software is based on EXTcalendar                             //
// of Kristof De Jaeger sweaty@urgent.ugent.be                          //

// outofdate.inc.php
# ----------------------------------------------------------------------
# out-of-date events
# ----------------------------------------------------------------------
$userTimestamp = xoops_getUserTimestamp(time());
$date = date('Ymd', $userTimestamp);
// expired events
$query = 'SELECT id,title,cat_name,date,type FROM ' . XOOPS_DB_PREFIX . '_agendax_events LEFT JOIN ' . XOOPS_DB_PREFIX . '_agendax_cat ON ' . XOOPS_DB_PREFIX . '_agendax_events.cat=' . XOOPS_DB_PREFIX . '_agendax_cat.cat_id ';

$query .= "WHERE type='E' AND (date<'$date') ORDER BY date ASC";
$result = $GLOBALS['xoopsDB']->queryF($query);
$rows = $GLOBALS['xoopsDB']->getRowsNum($result);

$output_str = '<a href=index.php?op=delalloodev>' . _('Delete all out of date events') . " !</a><br><br>\n";
$foo = '';
while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
    $foo++ % 2 ? $color = 'BBBBBB' : $color = 'EEEEEE';

    if ($row->date < $date) {
        $output_str .= "<table border=1 bgcolor=$color cellspacing=0 cellpadding=4 width=\"100%\">\n";

        $output_str .= "<tr><td>\n<li><b>" . stripslashes($row->title) . '</b> ' . _('on') . ' ' . agendax_showdate($row->date, 'short') . "\n";

        $output_str .= ' - ' . _('category') . ' : ' . $row->cat_name . "\n";

        $output_str .= ' - <a href=index.php?op=view&id=' . $row->id . '>' . _('view') . "</a>\n";

        $output_str .= ' - <a href=index.php?op=edit&id=' . $row->id . '>' . _('edit') . "</a>\n";

        $output_str .= ' - <a href=index.php?op=delev&id=' . $row->id . '>' . _('delete') . "</a>\n";

        $output_str .= "</td></tr>\n";

        $output_str .= "</table>\n";
    }
}

$query = 'SELECT id,title,cat_name,date,event_end FROM '
          . XOOPS_DB_PREFIX
          . '_agendax_events LEFT JOIN '
          . XOOPS_DB_PREFIX
          . '_agendax_cat ON '
          . XOOPS_DB_PREFIX
          . '_agendax_events.cat='
          . XOOPS_DB_PREFIX
          . '_agendax_cat.cat_id LEFT JOIN '
          . XOOPS_DB_PREFIX
          . '_agendax_event_repeats ON id=event_id ';
$query .= "WHERE type='R' AND (event_end<'$date') AND (event_end != 0) ORDER BY title ASC";
$result = $GLOBALS['xoopsDB']->queryF($query);
$rows = $GLOBALS['xoopsDB']->getRowsNum($result);

$output_str .= '<br><br>' . _('Out of date repeat events') . " :<br><br>\n";
while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
    $foo++ % 2 ? $color = 'BBBBBB' : $color = 'EEEEEE';

    $output_str .= "<table border=1 bgcolor=$color cellspacing=0 cellpadding=4 width=\"100%\">\n";

    $output_str .= "<tr><td>\n<li><b>" . stripslashes($row->title) . '</b> ' . _('terminated on') . ' ' . agendax_showdate($row->event_end, 'short') . "\n";

    $output_str .= ' - ' . _('category') . ' : ' . $row->cat_name . "\n";

    $output_str .= ' - <a href=index.php?op=view&id=' . $row->id . '&on=' . $row->event_end . '>' . _('view') . "</a>\n";

    $output_str .= ' - <a href=index.php?op=edit&id=' . $row->id . '>' . _('edit') . "</a>\n";

    $output_str .= ' - <a href=index.php?op=delev&id=' . $row->id . '>' . _('delete') . "</a>\n";

    $output_str .= "</td></tr>\n";

    $output_str .= "</table>\n";
}

// all active events
$query = 'SELECT id,title,cat_name,date,type,event_end FROM '
          . XOOPS_DB_PREFIX
          . '_agendax_events LEFT JOIN '
          . XOOPS_DB_PREFIX
          . '_agendax_cat ON '
          . XOOPS_DB_PREFIX
          . '_agendax_events.cat='
          . XOOPS_DB_PREFIX
          . '_agendax_cat.cat_id LEFT JOIN '
          . XOOPS_DB_PREFIX
          . '_agendax_event_repeats ON id=event_id ';
$query .= "WHERE (date>='$date') OR (event_end>='$date') OR (event_end=NULL) OR (event_end=0) ORDER BY date ASC";
$result = $GLOBALS['xoopsDB']->queryF($query);
$rows = $GLOBALS['xoopsDB']->getRowsNum($result);

$output_str .= '<br><br>' . _('Current active events') . " :<br><br>\n";
$foo = '';
while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
    $foo++ % 2 ? $color = 'BBBBBB' : $color = 'EEEEEE';

    {
        $output_str .= "<table border=1 bgcolor=$color cellspacing=0 cellpadding=4 width=\"100%\">\n";
        $output_str .= "<tr><td>\n<li><b>" . stripslashes($row->title) . '</b> ';
        if ('E' == $row->type) {
            $output_str .= _('on') . ' ' . agendax_showdate($row->date, 'short') . "\n";
        }
        if ('R' == $row->type) {
            $output_str .= '(' . _('recurring event') . ')' . "\n";
        }
        $output_str .= ' - ' . _('cat') . ' : ' . $row->cat_name . "\n";
        if ('E' == $row->type) {
            $output_str .= ' - <a href=index.php?op=view&id=' . $row->id . '>' . _('view') . "</a>\n";
        }
        $output_str .= ' - <a href=index.php?op=edit&id=' . $row->id . '>' . _('edit') . "</a>\n";
        $output_str .= ' - <a href=index.php?op=delev&id=' . $row->id . '>' . _('delev') . "</a>\n";
        $output_str .= "</td></tr>\n";
        $output_str .= "</table>\n";
    }
}
$agendax['content'] = $output_str;
