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
// searchresults.inc.php file

$agendax['searchTitle'] = _('Search results');

$search = $_POST['search'] ?? '';

$agendax['resultsFound'] = 'no';

if (!$search) {
    $agendax['noresults'] = _('no result found');
} # nothing in search
elseif (mb_strlen($search) < 3) {
    $agendax['noresults'] = _('no result found');
} # must be longer then 3 chars !
else {
    $query = 'SELECT id,title,description,contact,url,email,cat,cat_name,date FROM ' . XOOPS_DB_PREFIX . '_agendax_events LEFT JOIN ' . XOOPS_DB_PREFIX . '_agendax_cat ON ';

    $query .= XOOPS_DB_PREFIX . '_agendax_events.cat=' . XOOPS_DB_PREFIX . "_agendax_cat.cat_id WHERE title LIKE '%$search%' OR description LIKE '%$search%' OR url like '%$search%' AND approved = '1' ";

    $query .= 'ORDER BY year ASC, month ASC, day ASC';

    $result = $GLOBALS['xoopsDB']->queryF($query);

    $rows = $GLOBALS['xoopsDB']->getRowsNum($result);

    if (0 == $rows) {
        $agendax['noresults'] = _('no result found');
    } else {
        $agendax['resultsFound'] = 'yes';

        $agendax['resultsNb'] = "($rows " . _('results') . ')';

        $agendax['results'] = [];

        $k = 0;

        while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
            $agendax['results'][$k]['title'] = "<a href=\"$agendax_url/index.php?op=view&id=$row->id\">$row->title</a>";

            $agendax['results'][$k]['date'] = agendax_showdate($row->date);

            $agendax['results'][$k]['contact'] = _('Contact') . " : <a href=\"mailto:$row->email\">$row->contact</a>";

            $agendax['results'][$k]['category'] = _('Category') . " : <a href=\"$agendax_url/index.php?op=cat&id=$row->cat\">$row->cat_name</a>";

            $de = str_replace('<br>', '', $row->description);

            $de = str_replace('<br>', '', $row->description);

            $agendax['results'][$k]['description'] = mb_substr(stripslashes($de), 0, 100) . ' ...  ';

            $agendax['results'][$k]['readmore'] = "<a href=\"$agendax_url/index.php?op=view&id=$row->id\">" . _('Read more') . '</a>';

            $agendax['results'][$k]['url'] = _('URL') . " : <a href=\"http://$row->url\">$row->url</a>";

            $k++;
        }
    }
}

$agendax['searchform'] = search();

$GLOBALS['xoopsOption']['template_main'] = 'agendax_searchresults.html';
