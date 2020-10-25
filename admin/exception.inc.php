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
# make event exception
# ----------------------------------------------------------------------

$title       = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$contact     = $_POST['contact'] ?? '';
$email       = $_POST['email'] ?? '';
$cat         = $_POST['cat'] ?? 1;
$foto        = $_POST['foto'] ?? '';
if (isset($_POST['original_date'])) {
    $original_date = $_POST['original_date'];
}
if (isset($_POST['event_date'])) {
    $event_date = str_replace('-', '', $_POST['event_date']);
} else {
    $event_date = '$original_date';
}
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
}
if (isset($_POST['submit_by'])) {
    $submit_by = $_POST['submit_by'];
}
$url = $_POST['url'] ?? 'none';

global $myts; //text sanitisor

if (empty($title)) {
    $output_str .= _('Please provide a title') . '<br>' . back();
} elseif (empty($description)) {
    $output_str .= _('Please give a description') . '<br>' . back();
} elseif (empty($contact)) {
    $output_str .= _('Please provide a contact person') . '<br>' . back();
} elseif ((1 == $email_should_be_validated) && ('valid' != validate_email($email))) {
    $output_str .= _('Please provide a valid email address') . '<br>' . back();
} elseif (empty($event_date)) {
    $output_str .= _('Please specify event date') . '<br>' . back();
} else {
    $url = str_replace('http://', '', $url);

    $modif_date = date('Ymd');

    $modif_time = date('His');

    $type = 'E';

    $access = 'P';   // 'P' public, 'R' restricted, will be extended to restricted/private access type

    $title = $myts->addSlashes($title);

    $description = $myts->addSlashes($description);

    $contact = $myts->addSlashes($contact);

    // try to see if the exception exists already

    //    $sql = "SELECT event_date FROM ".XOOPS_DB_PREFIX."_agendax_event_repeat_not WHERE event_id='$event_id'";

    //    $result = $GLOBALS['xoopsDB']->queryF($sql)

    //        || die("Invalid query: " . $GLOBALS['xoopsDB']->error());

    //    if (!$row = $GLOBALS['xoopsDB']->fetchObject($result)) {

    //create the exception

    $sql = 'INSERT INTO ' . XOOPS_DB_PREFIX . "_agendax_event_repeats_not VALUES('$event_id', '$original_date')";

    $result = $GLOBALS['xoopsDB']->queryF($sql) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

    $query = 'INSERT INTO ' . XOOPS_DB_PREFIX . "_agendax_events VALUES('', '$event_id', NULL, '$title','$description','$contact','$url','$email','$foto','$cat','$event_date','0','$modif_date','$modif_time','0','2','0','0','0','1','$submit_by','$type','$access')";

    //        $query = "UPDATE ".XOOPS_DB_PREFIX."_agendax_events SET picture='$foto',title='$title',description='$description',contact='$contact',url='$url',email='$email',cat='$cat',date='$startDate',time='0',modif_date='$modif_date',modif_time='$modif_time',type='$type'";

    //        $query .= " WHERE id='$event_id'";

    $result = $GLOBALS['xoopsDB']->queryF($query) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

    redirect_header("index.php?op=view&id=$id&on=$event_date", 1, _('data base updated'));
}
