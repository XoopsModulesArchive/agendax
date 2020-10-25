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

if (!preg_match('/index.php/', $HTTP_SERVER_VARS['PHP_SELF'])) {
    exit('Access Denied');
}

if (1 == $email_should_be_validated) {
    include './checkemail.inc.php';
}
# ----------------------------------------------------------------------
# update event
# ----------------------------------------------------------------------

$title       = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$contact     = $_POST['contact'] ?? '';
$email       = $_POST['email'] ?? '';
$cat         = $_POST['cat'] ?? 1;
$foto        = $_POST['foto'] ?? '';
if (isset($_POST['startDate'])) {
    $startDate = str_replace('-', '', $_POST['startDate']);
} else {
    $startDate = date('Ymd');
}
$rpt_end_use = $_POST['rpt_end_use'] ?? 'n';
if (('y' == $rpt_end_use) && !empty($_POST['endDate'])) {
    $event_end = $_POST['endDate'];
} else {
    $event_end = null;
}
$rpt_freq_daily         = $_POST['rpt_freq_daily'] ?? '0';
$rpt_freq_weekly        = $_POST['rpt_freq_weekly'] ?? '0';
$rpt_freq_yearly        = $_POST['rpt_freq_yearly'] ?? '0';
$rpt_freq_monthlyByDay  = $_POST['rpt_freq_monthlyByDay'] ?? '0';
$rpt_freq_monthlyByDayR = $_POST['rpt_freq_monthlyByDayR'] ?? '0';
$rpt_freq_monthlyByDate = $_POST['rpt_freq_monthlyByDate'] ?? '0';
$rpt_type               = $_POST['rpt_type'] ?? 'none';
$url                    = $_POST['url'] ?? 'none';

$rptfreqname = 'rpt_freq_' . $rpt_type;
if ('none' != $rpt_type) {
    $rpt_freq = $$rptfreqname;
} else {
    $rpt_freq = '0';
}

$rpt_sun = $_POST['rpt_sun'] ?? 'n';
$rpt_mon = $_POST['rpt_mon'] ?? 'n';
$rpt_tue = $_POST['rpt_tue'] ?? 'n';
$rpt_wed = $_POST['rpt_wed'] ?? 'n';
$rpt_thu = $_POST['rpt_thu'] ?? 'n';
$rpt_fri = $_POST['rpt_fri'] ?? 'n';
$rpt_sat = $_POST['rpt_sat'] ?? 'n';

$event_repeaton_days = $rpt_sun . $rpt_mon . $rpt_tue . $rpt_wed . $rpt_thu . $rpt_fri . $rpt_sat;

global $myts; //text sanitisor

if (empty($title)) {
    $output_str .= _('Please provide a title') . '<br>' . back();
} elseif (empty($description)) {
    $output_str .= _('Please give a description') . '<br>' . back();
} elseif (empty($contact)) {
    $output_str .= _('Please provide a contact person') . '<br>' . back();
} elseif ((1 == $email_should_be_validated) && ('valid' != validate_email($email))) {
    $output_str .= _('Please provide a valid email address') . '<br>' . back();
} elseif (empty($startDate)) {
    $output_str .= _('Please specify event date') . '<br>' . back();
} else {
    $url = str_replace('http://', '', $url);

    $modif_date = date('Ymd');

    $modif_time = date('His');

    // 'E'= non_repeating event, 'R'= repeating event, 'B'= ressource booking event

    ('none' == $rpt_type) ? $type = 'E' : $type = 'R';

    $access = 'P';   // 'P' public, 'R' restricted, will be extended to restricted/private access type

    // try to see the original type of the event

    $sql = 'SELECT type FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE id='$id'";

    $result = $GLOBALS['xoopsDB']->queryF($sql) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

    if ($row = $GLOBALS['xoopsDB']->fetchObject($result)) {
        $old_type = $row->type;
    }

    $title = $myts->addSlashes($title);

    $description = $myts->addSlashes($description);

    $contact = $myts->addSlashes($contact);

    $query = 'UPDATE ' . XOOPS_DB_PREFIX . "_agendax_events SET picture='$foto',title='$title',description='$description',contact='$contact',url='$url',email='$email',cat='$cat',date='$startDate',time='0',modif_date='$modif_date',modif_time='$modif_time',type='$type'";

    $query .= " WHERE id='$id'";

    $result = $GLOBALS['xoopsDB']->queryF($query) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

    //now update the agendax_event_repeats table

    if ('R' == $type) {
        $event_end = str_replace('-', '', $event_end);

        if ('E' == $old_type) {
            // an original non repeating event becomes repeating ->insert

            $sql = 'INSERT INTO ' . XOOPS_DB_PREFIX . "_agendax_event_repeats VALUES('$id', '$rpt_type', '$event_end', '$rpt_freq', '$event_repeaton_days')";

            $result = $GLOBALS['xoopsDB']->queryF($sql) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());
        } elseif ('R' == $old_type) {
            // an original repeating event remains repeating ->update

            $sql = 'UPDATE ' . XOOPS_DB_PREFIX . "_agendax_event_repeats SET event_rpt_type='$rpt_type', event_end='$event_end', frequency='$rpt_freq', event_repeaton_days='$event_repeaton_days' WHERE event_id='$id'";

            $result = $GLOBALS['xoopsDB']->queryF($sql) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());
        }
    } else {
        if ('R' == $old_type) {
            // an original repeating event becomes non repeating - delete

            $sql = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_event_repeats WHERE event_id='$id'";

            $result = $GLOBALS['xoopsDB']->queryF($sql) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

            $sql = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_event_repeats_not WHERE event_id='$id'";

            $result = $GLOBALS['xoopsDB']->queryF($sql) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());
        }
    }

    redirect_header("index.php?op=view&id=$id", 1, _('data base updated'));
}
