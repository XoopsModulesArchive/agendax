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

// functions.inc.php file

# ----------------
# search function
# ----------------

function search()
{
    global $agendax_url;

    $output_str = '';

    $output_str .= "<form action=$agendax_url/index.php?op=res method=post>\n";

    $output_str .= "<input type=text name=search>\n";

    $output_str .= '<input type=submit value="' . _('search') . "\">\n";

    $output_str .= "</form>\n";

    return $output_str;
}

# ---------------
# back function
# ---------------

function back()
{
    $out = '<br><a href="javascript:history.back()">' . _('back to previous page') . "</a>\n";

    return $out;
}

/*
** $date is in the form of nnnnmmdd (ex: 20040524)
*/
function agendax_showdate($date = '', $type = 'long')
{
    global $week, $maand, $dateSuffix;

    if ('' == $date) {
        $userTimestamp = xoops_getUserTimestamp(time());

        $da = date('j', $userTimestamp);

        $mo = date('n', $userTimestamp);

        $ye = date('Y', $userTimestamp);
    } else {
        $ye = (int)mb_substr($date, 0, 4);

        $mo = (int)mb_substr($date, 4, 2);

        $da = (int)mb_substr($date, 6, 2);
    }

    $we = mktime(0, 0, 0, $mo, $da, $ye);

    $we = strftime('%w', $we);

    $we++;

    if ('long' == $type) {
        if (_SHORTDATESTRING == 'n/j/Y') {
            $ret = _($week[$we]) . ' ' . _($maand[$mo]) . ' ' . $da . $dateSuffix['ri'] . ' ' . $ye . $dateSuffix['nian'];
        } elseif (_SHORTDATESTRING == 'j/n/Y') {
            $ret = _($week[$we]) . ' ' . $da . $dateSuffix['ri'] . ' ' . _($maand[$mo]) . ' ' . $ye . $dateSuffix['nian'];
        } else {          //default to "Y/n/j"
            $ret = $ye . $dateSuffix['nian'] . ' ' . _($maand[$mo]) . ' ' . $da . $dateSuffix['ri'] . ' ' . _($week[$we]);
        }
    } elseif ('middle' == $type) {
        if (_SHORTDATESTRING == 'n/j/Y') {
            $ret = _($maand[$mo]) . ' ' . $da . $dateSuffix['ri'] . ' ' . $ye . $dateSuffix['nian'];
        } elseif (_SHORTDATESTRING == 'j/n/Y') {
            $ret = $da . $dateSuffix['ri'] . ' ' . _($maand[$mo]) . ' ' . $ye . $dateSuffix['nian'];
        } else {          //default to "Y/n/j"
            $ret = $ye . $dateSuffix['nian'] . ' ' . _($maand[$mo]) . ' ' . $da . $dateSuffix['ri'];
        }
    } else {
        if (_SHORTDATESTRING == 'n/j/Y') {
            $ret = $mo . '-' . $da . '-' . $ye;
        } elseif (_SHORTDATESTRING == 'j/n/Y') {
            $ret = $da . '-' . $mo . '-' . $ye;
        } else {          //default to "Y/n/j"
            $ret = $ye . '-' . $mo . '-' . $da;
        }
    }

    return $ret;
}
