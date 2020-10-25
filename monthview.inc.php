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
// monthview.inc.php file

if (isset($_POST['cid'])) {
    $cid = $_POST['cid'];
} elseif (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    $cid = '';
}
if ($cid == 0) {
    $cid = '';
}

$year  = (int)substr($kdate, 0, 4);
$month = (int)substr($kdate, 4, 2);
$day   = (int)substr($kdate, 6, 2);

# previous month
$pm = $month;
if ($month == '1') {
    $pm = '12';
} else {
    $pm--;
}

# previous year
$py = $year;
if ($pm == '12') {
    $py--;
}

# next month
$nm = $month;
if ($month == '12') {
    $nm = '1';
} else {
    $nm++;
}

# next year
$ny = $year;
if ($nm == 1) {
    $ny++;
}

$pdate = date('Ymd', mktime(3, 0, 0, $pm, $day, $py));
$ndate = date('Ymd', mktime(3, 0, 0, $nm, $day, $ny));

# get month we want to see + calculate first day
$askedmonth = $maand[$month];
$askedyear  = $year;

$firstday = date('w', mktime(12, 0, 0, $month, 1, $year));
$firstday++;

# nr of days in askedmonth
$nr = date('t', mktime(12, 0, 0, $month, 1, $year));

$events          = [];
$repeated_events = [];
$TZ_OFFSET       = 0;
$repeated_events = read_repeated_events($cid, 0);
global $ONE_DAY;

$agendax['edate']           = [];
$agendax['adminmenu']       = [];
$agendax['title']           = [];
$agendax['description']     = [];
$agendax['contact_caption'] = [];
$agendax['contact']         = [];
$agendax['email_caption']   = [];
$agendax['email']           = [];
$agendax['url_caption']     = [];
$agendax['url']             = [];
$agendax['picture']         = [];

$firstdate = mktime(3, 0, 0, $month, 1, $year);

$date = date('Ymd', $firstdate);

$output_str .= "<table><tr>\n";
$output_str .= "<td><form action=$agendax_url/index.php?op=cal&kdate=" . $kdate . " method=post enctype=multipart/form-data name=monthview>\n";

# get the categories
$output_str .= _('Filter by category') . " : <select class=textBox2 name=cid>\n";
$output_str .= '<option value=0>' . _('ALL') . "</option>\n";
$query      = 'SELECT cat_id,cat_name FROM ' . XOOPS_DB_PREFIX . '_agendax_cat';
$result     = $GLOBALS['xoopsDB']->queryF($query);
while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
    $output_str .= "\t<option value=" . $row->cat_id;
    $output_str .= $row->cat_id == $cid ? ' selected' : '';
    $output_str .= '>' . $row->cat_name . "</option>\n";
}
$output_str .= "</select>&nbsp;\n";
$output_str .= '<input type=submit value="' . _('Show events') . "\"></form></td>\n";

$output_str .= "<td align=right>&nbsp;</td><td bgcolor=$todayclr width=15 height=15 align=right>&nbsp;</td><td align=right> = " . _('Today') . "</td></tr>\n";
$output_str .= "</table>\n";

// end category filter

# header (with links)
$output_str .= "<table border=$monthborder cellspacing=$calcells cellpadding=$calcellp width=$tablewidth>\n";
$output_str .= "<tr bgcolor=$trtopcolor>";
$output_str .= "<th align=center colspan=7 height=$tdtopheight>";
if ($month != date('n') || $year != date('Y')) {
    $output_str .= "<a href=$agendax_url/index.php?op=cal&kdate=" . $pdate . '>';
    if ($usearrows == 1) {
        $output_str .= "<img src=$arrowleft border=0 alt=\"$py _($maand[$pm])\" title=\"$py _($maand[$pm])\">";
    } else {
        $output_str .= '  <= ';
    }
    if ($displaylink == 1) {
        $output_str .= $py . $dateSuffix['nian'] . ' - ' . _($maand[$pm]);
    }
    $output_str .= '</a>';
} elseif ($archive == 1) {
    $output_str .= "<a href=$agendax_url/index.php?op=cal&kdate=" . $pdate . '>';
    if ($usearrows == 1) {
        $output_str .= "<img src=$arrowleft border=0 alt=\"$py $maand[$pm]\" title=\"$py _($maand[$pm])\">";
    } else {
        $output_str .= '  <= ';
    }
    if ($displaylink == 1) {
        $output_str .= $py . $dateSuffix['nian'] . ' - ' . _($maand[$pm]);
    }
    $output_str .= '</a>';
}
$output_str .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $askedyear . $dateSuffix['nian'] . ' ' . _($askedmonth);
$output_str .= "&nbsp;&nbsp;&nbsp;<a href=$agendax_url/index.php?op=cal&kdate=" . $ndate . '>';
if ($displaylink == 1) {
    $output_str .= $ny . $dateSuffix['nian'] . ' - ' . _($maand[$nm]);
}
if ($usearrows == 1) {
    $output_str .= "<img src=$arrowright border=0 alt=\"$ny _($maand[$nm])\" title=\"$ny _($maand[$nm])\">";
} else {
    $output_str .= ' => ';
}
$output_str .= '</a></th>';
$output_str .= "</tr>\n";
$output_str .= '<tr>';
#  make the days of week, consisting of seven <td>'s (=days)
# check if first is sunday or monday ? move sunday to end of array if day_start = 1
if ($day_start == 1) {
    $week[8] = $week[1];
    array_shift($week);
    $sunday_td = 7;
    # now let's move all keys one down :)
    for ($i = 6, $a = 7; $i >= 0; $i--, $a--) {
        $week[$a] = $week[$i];
    }
    $firstday--;
} else {
    $sunday_td = 1;
}
# print the weekdays
for ($i = 1; $i <= 7; $i++) {
    $output_str .= "<td align=center width=$tdwidth height=$tddayheight ";
    if ($i == $sunday_td) {
        $output_str .= "bgcolor=$sundaytopclr>" . _($week[$i]) . '</td>';
    } # sunday
    else {
        $output_str .= "bgcolor=$weekdaytopclr>" . _($week[$i]) . '</td>';
    } # rest of week
}
$output_str .= "</tr>\n";

# begin the days
if ($firstday == 0 && $day_start == 1) {
    $firstday = 7;
}
for ($i = 1; $i < $firstday; $i++) { { {
    $output_ut_str .= "<tadght=$tddayhht ";
}
}
    if ($i = $$sunday_td) {
$output_str .= "<tablor=$sundaytopcemptyclr   unday
    empty td else $output_str tr .= " < lor = $weekdaytopcemptyclr    elseput_str .= '</tr>p;&nbs>'; # sout
    }
;$i0;head'exng of '
for he h) {
    ;
}
$ ($i = 1; $i <= 7; $i$nr + ) { {
{
    $outpayt;
}
}
    f$i  # no$i ==
fort == 1) $firsayt++  # noput_str .= "</tr>light ";
widtht ";
    if ($i =$ $sundd$day_h);
$$sundm$day_ != d) $$y)put_str .= " < / trlor = $weekto
foclr   undhig = cehe wo
foove
fo else { { $ou$i = (day_$sun(2 - stday;$i + )) || (day_$sun(9 - stday;$i + )) || (dayn$sun(16 - stday;$i + )) || (dayn$sun(23 - stday;$i + )) || (dayn$sun(30 - stday;$i + )) || (dayn$sun(37._(stday;$i + ))  {
    put_str .= '</trlor>'; = day_td)clr    else {
$ou tput_str .= "  <=lor=$weekdaytopcclr    elseput_str .= " < / tr vn = centlr >> < b > $i < / b#  mif ( elsew let'get  <tdtual  <tdt wen $e.">" else$e."y;
f6,$ < 10) ? '0' . $$ : $i  # no$eh);
$$sonth != < 10) ? '0' . $h);
    }
}
}
$$ : $h);
$  # nope.">";
f$.$dateeh);
$.$e . "y  # no//if (if ($queiltgcoSELECT id,e=\"$,gor FROM skexoopsDB->p$agix("dax_url / < tdts"/td LEFT JOIN skexoopsDB->p$agix("dax_url / gor'/td ONegor&kdt_id WHERE 
ftepdate.' > '.' and appralld = '1' ORDER BYegor ASCoutif( else //if (if ($ oful1
if $xoopsDB->queilF($queil   $sun else { //igory filter
    #   nd ng= he parame
    # hif ($ <tdts;
    fdax_url / rr(_ < tdts(eSuff, $cid   $sunddax_url['e=\"$'$weey_shi($suner
l_ . '>'_r cries(eSuff, 0, AGENDAX_SHOW_COMPLET   $sund . '>';
}
f . '>'('Ymd', $tday == te + $i * $ONE_DAY   $sun else {
($i = 1 $k;
}
f0; $k; < count($dax_url['e=\"$'$); $k{$weekut_str .= "<tablli class=inCal#  mif (if ($ut_str .= " < tatr ipslashes(edax_url['e=\"$'$[$k < / td > "a></li#  mif (  $firsut_str .= " < tabl / s(
        $outif (elseecklosof s < n"; vo$i=cateeek
}
$oif ($a++;
    if (($i == (8-$firstday)) or ($i == (15-$firstday)) or ($i == (22-$firstday)) or ($i == (29-$firstday)) or ($i == (36 - $firstday)))
    {
        $output_str .= " < / tr > \n < tr > ";
        $a = 0;
    }
}
# ending stuff (making 'white' td's to fill table
if ($a != 0)
{
    $last = 7-$a;
    for ($i=1;$i<=$last;$i++)
    {
        $output_str .= " < td bgcolor = $weekdayemptyclr > & nbsp; < / td > ";
    }
}
$output_str .= " < / tr > \n";
$output_str .= " < / table > \n";

$agendax['calendar'] = &$output_str;

//      if ($searchdayok == 1)  $agendax['searchform'] = search();
$GLOBALS['xoopsOption']['template_main'] = 'agendax_monthview.html'
