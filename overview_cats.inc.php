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
// overview_cats.inc.php file

$query = 'SELECT cat_id,cat_name FROM ' . XOOPS_DB_PREFIX . '_agendax_cat ORDER BY cat_name';
$result = $GLOBALS['xoopsDB']->queryF($query);
$rows = $GLOBALS['xoopsDB']->getRowsNum($result);

// admin menu if user can manage categories
/*
** The unique id of an item to check permissions for.
*/
$perm_itemid = 4;

# if no rows
if ('0' == $rows) {
    $agendax['empty'] = 'true';

    $agendax['noresult'] = '<br><br>' . _("There's no category yet!") . "\n";
} # show categorys
else {
    $agendax['catList'] = _('Category List');

    $agendax['categories'] = [];

    $agendax['eventNb'] = [];

    while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
        $querycount = 'SELECT COUNT(*) AS count FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE cat=$row->cat_id AND approved=1 AND group_id IS NULL";

        $result2 = $GLOBALS['xoopsDB']->queryF($querycount);

        $row2 = $GLOBALS['xoopsDB']->fetchObject($result2);

        $agendax['categories'][] = "<a href=\"$agendax_url/index.php?op=cat&id=" . $row->cat_id . '">' . stripslashes($row->cat_name) . ' </a>';

        #if admin edit category function

        /*
        ** check permission
        */

        if ($gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            /*
            ** allowed
            */

            //          if ($agendax_isadmin) {

            $agendax['edit'][] = " <a href=\"$agendax_url/index.php?op=editcat&id=" . $row->cat_id . '">' . _('Edit') . "</a>\n" . ' | ' . " <a href=\"$agendax_url/index.php?op=delcat&id=" . $row->cat_id . '">' . _('Del') . "</a>\n";
        } else {
            $agendax['edit'][] = '';
        }

        $agendax['eventNb'][] = $row2->count;
    }
}

/*
** check permission
*/
if ($gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
    /*
    ** allowed
    */

    $agendax['addcat'] = "<br><br>\n";

    $agendax['addcat'] .= _('Add new category');

    $agendax['addcat'] .= "<form action=\"$agendax_url/index.php?op=addcat\" method=\"post\">\n";

    $agendax['addcat'] .= '<input type="text" name="cat" size="30"> ';

    $agendax['addcat'] .= '<input type="submit" value="' . _('Add') . "\">\n";

    $agendax['addcat'] .= "</form>\n";
}

$GLOBALS['xoopsOption']['template_main'] = 'agendax_overview_cats.html';
