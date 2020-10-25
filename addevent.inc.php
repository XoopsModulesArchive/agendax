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

if (1 == $email_should_be_validated) {
    include './checkemail.inc.php';
}
require_once './include/agendax.class.php';

//if (isset($_POST['title'])) $title = $_POST['title']; else $title = '';
//if (isset($_POST['description'])) $description = $_POST['description']; else $description = '';
//if (isset($_POST['contact'])) $contact = $_POST['contact']; else $contact = '';
//if (isset($_POST['email'])) $email = $_POST['email']; else $email = '';
//if (isset($_POST['cat'])) $cat = $_POST['cat']; else $cat = '';
//if (isset($_POST['startDate'])) $startDate = $_POST['startDate']; else $startDate = '';
//if (isset($_POST['endDate'])) $event_end = $_POST['endDate']; else $event_end = '';
//if (isset($_POST['rpt_end_use'])) $rpt_end_use = $_POST['rpt_end_use']; else $rpt_end_use = 'n';
//if (isset($_POST['rpt_freq_daily'])) $rpt_freq_daily = $_POST['rpt_freq_daily']; else $rpt_freq_daily = '0';
//if (isset($_POST['rpt_freq_weekly'])) $rpt_freq_weekly = $_POST['rpt_freq_weekly']; else $rpt_freq_weekly = '0';
//if (isset($_POST['rpt_freq_yearly'])) $rpt_freq_yearly = $_POST['rpt_freq_yearly']; else $rpt_freq_yearly = '0';
//if (isset($_POST['rpt_freq_monthlyByDay'])) $rpt_freq_monthlyByDay = $_POST['rpt_freq_monthlyByDay']; else $rpt_freq_monthlyByDay = '0';
//if (isset($_POST['rpt_freq_monthlyByDayR'])) $rpt_freq_monthlyByDayR = $_POST['rpt_freq_monthlyByDayR']; else $rpt_freq_monthlyByDayR = '0';
//if (isset($_POST['rpt_freq_monthlyByDate'])) $rpt_freq_monthlyByDate = $_POST['rpt_freq_monthlyByDate']; else $rpt_freq_monthlyByDate = '0';
//if (isset($_POST['rpt_type'])) $event_rpt_type = $_POST['rpt_type']; else $event_rpt_type = 'none';
//if (isset($_POST['url'])) $url = $_POST['url']; else $url = 'none';
//
//$rptfreqname= 'rpt_freq_'.$event_rpt_type;
//if ($event_rpt_type != 'none') $rpt_freq = $$rptfreqname; else $rpt_freq = '0';
//
//if (isset($_POST['rpt_sun'])) $rpt_sun = $_POST['rpt_sun']; else $rpt_sun = 'n';
//if (isset($_POST['rpt_mon'])) $rpt_mon = $_POST['rpt_mon']; else $rpt_mon = 'n';
//if (isset($_POST['rpt_tue'])) $rpt_tue = $_POST['rpt_tue']; else $rpt_tue = 'n';
//if (isset($_POST['rpt_wed'])) $rpt_wed = $_POST['rpt_wed']; else $rpt_wed = 'n';
//if (isset($_POST['rpt_thu'])) $rpt_thu = $_POST['rpt_thu']; else $rpt_thu = 'n';
//if (isset($_POST['rpt_fri'])) $rpt_fri = $_POST['rpt_fri']; else $rpt_fri = 'n';
//if (isset($_POST['rpt_sat'])) $rpt_sat = $_POST['rpt_sat']; else $rpt_sat = 'n';

$title = Agendax::getPost('title', '');
$description = Agendax::getPost('description', '');
$contact = Agendax::getPost('contact', '');
$email = Agendax::getPost('email', '');
$cat = Agendax::getPost('cat', '');
$startDate = Agendax::getPost('startDate', '');
$endDate = Agendax::getPost('endDate', '');
$rpt_end_use = Agendax::getPost('rpt_end_use', 'n');
$rpt_freq_daily = Agendax::getPost('rpt_freq_daily', '0');
$rpt_freq_weekly = Agendax::getPost('rpt_freq_weekly', '0');
$rpt_freq_yearly = Agendax::getPost('rpt_freq_yearly', '0');
$rpt_freq_monthlyByDay = Agendax::getPost('rpt_freq_monthlyByDay', '0');
$rpt_freq_monthlyByDayR = Agendax::getPost('rpt_freq_monthlyByDayR', '0');
$rpt_freq_monthlyByDate = Agendax::getPost('rpt_freq_monthlyByDate', '0');
$event_rpt_type = Agendax::getPost('rpt_type', 'none');
$url = Agendax::getPost('url', 'none');

//if (isset($_POST['rpt_type'])) $event_rpt_type = $_POST['rpt_type']; else $event_rpt_type = 'none';

$rptfreqname = 'rpt_freq_' . $event_rpt_type;
if ('none' != $event_rpt_type) {
    $rpt_freq = $$rptfreqname;
} else {
    $rpt_freq = '0';
}

$rpt_sun = Agendax::getPost('rpt_sun', 'n');
$rpt_mon = Agendax::getPost('rpt_mon', 'n');
$rpt_tue = Agendax::getPost('rpt_tue', 'n');
$rpt_wed = Agendax::getPost('rpt_wed', 'n');
$rpt_thu = Agendax::getPost('rpt_thu', 'n');
$rpt_fri = Agendax::getPost('rpt_fri', 'n');
$rpt_sat = Agendax::getPost('rpt_sat', 'n');

//if (isset($_POST['rpt_sun'])) $rpt_sun = $_POST['rpt_sun']; else $rpt_sun = 'n';
//if (isset($_POST['rpt_mon'])) $rpt_mon = $_POST['rpt_mon']; else $rpt_mon = 'n';
//if (isset($_POST['rpt_tue'])) $rpt_tue = $_POST['rpt_tue']; else $rpt_tue = 'n';
//if (isset($_POST['rpt_wed'])) $rpt_wed = $_POST['rpt_wed']; else $rpt_wed = 'n';
//if (isset($_POST['rpt_thu'])) $rpt_thu = $_POST['rpt_thu']; else $rpt_thu = 'n';
//if (isset($_POST['rpt_fri'])) $rpt_fri = $_POST['rpt_fri']; else $rpt_fri = 'n';
//if (isset($_POST['rpt_sat'])) $rpt_sat = $_POST['rpt_sat']; else $rpt_sat = 'n';

global $myts;

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
    # uploading picture allowed ?

    if (1 == $uploadviasite) {
        if (is_uploaded_file($_FILES['foto']['tmp_name'])) {
            # check for size of picture

            $size = $_FILES['foto']['size'];

            # check for extension !

            $name = $_FILES['foto']['name'];

            $ext = explode('.', $name);

            $ext = array_reverse($ext);

            $ext = $ext[0];

            $valid = 0;

            for ($i = 0, $iMax = count($extensions); $i < $iMax; $i++) {
                if (preg_match('/' . $extensions[$i] . '$/i', $ext)) {
                    $valid = 1;
                }
            }

            if ($size > $filesize) {
                $output_str .= _('Your file is too large') . back();

                exit;
            } elseif (1 != $valid) {
                $output_str .= _('File extension not valid') . back();

                exit;
            }

            move_uploaded_file($_FILES['foto']['tmp_name'], XOOPS_ROOT_PATH . '/uploads/' . $_FILES['foto']['name']);

            @chmod(XOOPS_ROOT_PATH . '/uploads/' . $_FILES['foto']['name'], 0644);

            $foto = $_FILES['foto']['name'];
        } else {
            $foto = '';
        }
    } else {
        $foto = '';
    }

    $url = str_replace('http://', '', $url);

    $approve = ($caleventapprove >= 1) ? 0 : 1;

    // if admin, auto approve

    if ($xoopsUser) {
        $xoopsModule = XoopsModule::getByDirname('agendax');

        if ($xoopsUser->isAdmin($xoopsModule->mid())) {
            $approve = 1;
        }

        $submit_by = $xoopsUser->uid();
    } else {
        $submit_by = 0;
    }

    $title = $myts->addSlashes($title);

    $description = $myts->addSlashes($description);

    $contact = $myts->addSlashes($contact);

    $startDate = str_replace('-', '', $startDate);

    $modif_date = date('Ymd');

    $modif_time = date('His');

    // 'E'= non_repeating event, 'R'= repeating event, 'B'= ressource booking event

    ('none' == $event_rpt_type) ? $type = 'E' : $type = 'R';

    $access = 'P';   // 'P' public, 'R' restricted, will be extended to restricted/private access type

    $query = 'insert into ' . XOOPS_DB_PREFIX . "_agendax_events values('', NULL, NULL, '$title','$description','$contact','$url','$email','$foto','$cat','$startDate','0',$modif_date,$modif_time,'0','2','0','0','0','$approve', '$submit_by', '$type', '$access')";

    $GLOBALS['xoopsDB']->queryF($query);

    $event_id = $GLOBALS['xoopsDB']->getInsertId();

    //for repeat events

    if ('R' == $type) {
        if ('n' == $rpt_end_use) {
            $event_end = null;
        } else {
            $event_end = str_replace('-', '', $endDate);
        }

        if ('weekly' == $event_rpt_type) {
            $event_repeaton_days = $rpt_sun . $rpt_mon . $rpt_tue . $rpt_wed . $rpt_thu . $rpt_fri . $rpt_sat;
        } else {
            $event_repeaton_days = 'nnnnnnn';
        }

        $query2 = 'INSERT INTO ' . XOOPS_DB_PREFIX . "_agendax_event_repeats VALUES('$event_id', '$event_rpt_type', '$event_end', '$rpt_freq', '$event_repeaton_days')";

        $GLOBALS['xoopsDB']->queryF($query2);
    }

    if (0 != $caleventapprove) {
        # send email ?

        //        if ($receiveemail == 1)

        //        {

        //          $mailTo="$emailadress";

        //          $mailSubject= _("mail title");

        //          $mailBody = _("mail body")."\n";

        //          $mailBody .= "$siteurl\n";

        //          $mailHeaders = "From: $emailadress <$emailadress>\n";

        //          $mailHeaders .= "X-Sender: <$emailadress>\n";

        //          $mailHeaders .= "X-Mailer: PHP\n"; // mailer

        //          # send mail

        //          mail($mailTo, $mailSubject, $mailBody, $mailHeaders);

        //        }

        $output_str .= '<div align=center>' . _('Thank you for your submission, we will examine it and list it shortly') . '</div>';
    } else {
        redirect_header($agendax_url . '/index.php', 3, _('Thank you for your submission !'));

        exit();
    }
}
