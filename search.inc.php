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

function agendax_xoops_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;

    $ret = [];

    if (!is_array($queryarray)) {
        $queryarray[0] = $queryarray;
    }

    $sql = 'SELECT id, title, description, submit_by FROM ' . $xoopsDB->prefix('agendax_events') . ' WHERE approved=1 ';

    if (0 != $userid) {
        $sql .= ' AND submit_by=' . $userid . ' ';
    }

    if (count($queryarray) > 0) {
        $count = count($queryarray);

        $sql .= " AND ((title LIKE '%$queryarray[0]%' OR description LIKE '%$queryarray[0]%'
		                     OR contact LIKE '%$queryarray[0]%')";

        for ($i = 1; $i < $count; $i++) {
            $sql .= $andor;

            $sql .= " (title LIKE '%$queryarray[$i]%' OR description LIKE '%$queryarray[$i]%'
			                OR contact LIKE '%$queryarray[0]%')";
        }

        $sql .= ') ';
    }

    $sql .= ' ORDER BY id DESC';

    $result = $xoopsDB->query($sql, $limit, $offset);

    $i = 0;

    while (false !== ($row = $xoopsDB->fetchArray($result))) {
        $ret[$i]['link'] = 'index.php?op=view&id=' . $row['id'] . '';

        $ret[$i]['title'] = $row['title'];

        $ret[$i]['description'] = $row['description'];

        $ret[$i]['time'] = '';

        $ret[$i]['uid'] = $row['submit_by'];

        $i++;
    }

    return $ret;
}
