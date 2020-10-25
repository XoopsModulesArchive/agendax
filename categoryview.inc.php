<?php

// ------------------------------------------------------------------------- //
//                           Agendax-X for Xoops                             //
//                              Version:  1.0                               //
// ------------------------------------------------------------------------- //
// Author: Wang Jue (alias wjue)                                             //
// Purpose: Very Flexible Calendar and Event Module                          //
// email: wjue@wjue.org                                                      //
// URLs: http://www.wjue.org,  http://www.guanxiCRM.com                      //
// Parts of this software is based on EXTcalendar                       //
// of Kristof De Jaeger<br>sweaty@urgent.ugent.be                          //
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
// categoryview.inc.php file

$query = 'SELECT id,title,type,cat_name,date FROM ' . XOOPS_DB_PREFIX . '_agendax_events LEFT JOIN ' . XOOPS_DB_PREFIX . '_agendax_cat ON ' . XOOPS_DB_PREFIX . '_agendax_events.cat=' . XOOPS_DB_PREFIX . '_agendax_cat.cat_id ';
$query .= "WHERE group_id IS NULL AND approved='1' AND " . XOOPS_DB_PREFIX . "_agendax_events.cat='$id' ORDER BY date ASC";
$result = $GLOBALS['xoopsDB']->queryF($query);
$rowname = $GLOBALS['xoopsDB']->fetchObject($result);
$rows = $GLOBALS['xoopsDB']->getRowsNum($result);

if (!$rows) {
    $agendax['eventFound'] = 'no';

    $agendax['noevent'] = _('No event found');
} else {
    $agendax['events'] = [];

    $k = 0;

    $agendax['category'] = _('Events in Category') . ' : ' . $rowname->cat_name;

    $result = $GLOBALS['xoopsDB']->queryF($query);

    $agendax['on'] = _('on');

    while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
        $year = (int)mb_substr($row->date, 0, 4);

        $month = (int)mb_substr($row->date, 4, 2);

        $day = (int)mb_substr($row->date, 6, 2);

        $agendax['events'][$k]['title'] = "<a href=$agendax_url/index.php?op=view&id=" . $row->id . '&on=' . $row->date . '>' . stripslashes($row->title) . '</a> ';

        if (_SHORTDATESTRING == 'n/j/Y') {
            $agendax['events'][$k]['date'] = _($maand[$month]) . ' ' . $day . $dateSuffix['ri'] . ' ' . $year . $dateSuffix['nian'];
        } elseif (_SHORTDATESTRING == 'j/n/Y') {
            $agendax['events'][$k]['date'] = $day . $dateSuffix['ri'] . ' ' . _($maand[$month]) . ' ' . $year . $dateSuffix['nian'];
        } else {          //default to "Y/n/j"
            $agendax['events'][$k]['date'] = $year . $dateSuffix['nian'] . ' ' . _($maand[$month]) . ' ' . $day . $dateSuffix['ri'];
        }

        $k++;
    }
}

$agendax['backLink'] = back();

$GLOBALS['xoopsOption']['template_main'] = 'agendax_categoryview.html';
