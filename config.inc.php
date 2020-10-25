<?php

// ------------------------------------------------------------------------- //
//                           Agendax-X for Xoops                             //
//                              Version:  2.1                                //
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
global $xoopsModuleConfig;

$agendax_path = XOOPS_ROOT_PATH . '/modules/agendax';
$agendax_url = XOOPS_URL . '/modules/agendax';

$agd_version = '2.2.0';
$agd_debug = false;
$gettext_debug = false;
//$gettext_debug = true;

$calauth = 1;

# don't change lines until end language !
//-------------------

//month names

$maand[1] = 'January';
$maand[2] = 'February';
$maand[3] = 'March';
$maand[4] = 'April';
$maand[5] = 'May';
$maand[6] = 'June';
$maand[7] = 'July';
$maand[8] = 'August';
$maand[9] = 'September';
$maand[10] = 'October';
$maand[11] = 'November';
$maand[12] = 'December';

//week names

$week[1] = 'Sunday';
$week[2] = 'Monday';
$week[3] = 'Tuesday';
$week[4] = 'Wednesday';
$week[5] = 'Thursday';
$week[6] = 'Friday';
$week[7] = 'Saturday';

//end of language

# ----------------------------------------------------------------------------------
# default view
#
# this is the default view you'll see on the live site
#
# day = dayview
# week = weekview
# month = monthview
# flat = monthview but in flat style
# ----------------------------------------------------------------------------------

$caldefault = @$xoopsModuleConfig['caldefault'];

# ----------------------------------------------------------------------------------
# views
#
# set several things (url, search) on/off
# 1 is on, 0 is off
# ----------------------------------------------------------------------------------

//$caleventapprove = '1'; # events submited by user should be approved; 1 = yes, 0 = no
$caleventapprove = @$xoopsModuleConfig['caleventapprove'];

# on which day should the month-view start (month view and minical - live site)
//$day_start = '1';  # 0 for sunday, 1 for monday
$day_start = '1';  # 0 for sunday, 1 for monday
//$day_start = @$xoopsModuleConfig['day_start'];

# set next var to one if links to the past should be displayed (on week-, month(flyer)-view)
//$archive = '1';
$archive = @$xoopsModuleConfig['archive'];

// I don't think this us being used
//$viewtodaydate = '1';   # view today date at the top
//$viewtodaydate = $xoopsModuleConfig['viewtodaydate'];

# Receive email when someone enters an event on the site ?
//$receiveemail = '0'; # 1 = yes, 0 = no
//$emailadress = 'someone@somedomain.com'; # this will be the address to send email in case of new eventsubmission!
//$urlsite = 'http://www.somewhere.com'; # url used in email message

# some options for forms
//$showemailfield = '1';   # show input field for email
//$showcontactfield = '1'; # show input field for contact
//$showurlfield = '1';     # show input field for url
//$uploadviasite = '1';    # may user upload to the website ?
$uploadviasite = @$xoopsModuleConfig['uploadviasite'];

//$filesize = '100000';    # in bytes, maximum size of picture allowed (not limited for admin)
$filesize = @$xoopsModuleConfig['filesize'];

# if you want event submitor's email should be validated by program set it to 1
//$email_should_be_validated = 0;
$email_should_be_validated = @$xoopsModuleConfig['email_should_be_validated'];

# valid extensions for the picture in live site ! (seperate with comma's ! - lowercase)
# do not move it to xoops_version file !!! quasi global
$extensions = ['png', 'gif', 'jpg', 'jpeg'];

# ----------------------------------------------------------------------------------
# colors & images
# ----------------------------------------------------------------------------------

# delimiter between links in header (can be image if you like)
//$delimiter = ' | ';
$delimiter = @$xoopsModuleConfig['delimiter'];

# use image-arrows for next and previous link ?
# you can replace the arrows with your own if you like
# if you don't use arrows '<==' and '==>' will be printed
//$usearrows = '1';
$usearrows = @$xoopsModuleConfig['usearrows'];

$arrowleft = $agendax_url . '/images/mini_arrowleft.gif';
$arrowleftleft = $agendax_url . '/images/mini_arrowleftleft.gif';
$arrowright = $agendax_url . '/images/mini_arrowright.gif';
$arrowrightright = $agendax_url . '/images/mini_arrowrightright.gif';

$displaylink = '1'; # -> print 'next day/month/week' & 'previous day/month/week' or not
//$displaylink = $xoopsModuleConfig['displaylink'];

# vars for categories
# two colors because the <tr>'s alternate
$firstcatcolor = '#BBBBBB';
$secondcatcolor = '#DDDDDD';

# vars for event from one category
# two colors, because the colors alternate
$firstcatevcolor = '#BBBBBB';
$secondcatevcolor = '#DDDDDD';

# vars calendar-month-view in flyer style
$showpicture = '1';
$tablewidthf = '98%'; # width of table
$tableborderf = '0'; # border ?
$cellsf = '0';    # cellspacing
$cellpf = '2';    # cellpadding
$calfontbackf = '2'; # link previous month
$calfontaskedf = '4'; # link asked month
$calfontnextf = '2'; # link next month
$tr1 = '#FFFFFF'; # color of tr
$tr2 = '#CCCCCC'; # color of next tr
$fontday = '3'; # font of day

# vars for calendar-month-view
$monthborder = '1';
//$monthborder = @$xoopsModuleConfig['monthborder'];
$tablewidth = '98%';
//$tablewidth = @$xoopsModuleConfig['tablewidth'];
//$tdwidth = '14%'; # width of cell
$tdwidth = @$xoopsModuleConfig['tdwidth'];
//$tdtopheight = '20'; # standard height of top cell
$tdtopheight = @$xoopsModuleConfig['tdtopheight'];
//$tddayheight = '20'; # standar height of weekday-cell
$tddayheight = @$xoopsModuleConfig['tddayheight'];
//$tdheight = '60'; # standard height of day-cell
$tdheight = @$xoopsModuleConfig['tdheight'];
//$calcells = '1'; # cellspacing
$calcells = @$xoopsModuleConfig['calcells'];
//$calcellp = '0'; # cellpadding
$calcellp = @$xoopsModuleConfig['calcellp'];
//$trtopcolor = '#CCCCCC'; # top <tr>
$trtopcolor = @$xoopsModuleConfig['trtopcolor'];
//$sundaytopclr = '#80E1A8'; # color sundayname in <tr>-top
$sundaytopclr = @$xoopsModuleConfig['sundaytopclr'];
//$weekdaytopclr = '#DDDDDD'; # color weekday-names
$weekdaytopclr = @$xoopsModuleConfig['weekdaytopclr'];
//$sundayemptyclr = '#F0F0F0'; # color of sunday that isn't in month
$sundayemptyclr = @$xoopsModuleConfig['sundayemptyclr'];
//$weekdayemptyclr = '#F0F0F0'; # color empty <td>
$weekdayemptyclr = @$xoopsModuleConfig['weekdayemptyclr'];
//$todayclr = '#AAAAAA'; # color today
$todayclr = @$xoopsModuleConfig['todayclr'];
//$sundayclr = '#80E1A8'; # color calendarsunday
$sundayclr = @$xoopsModuleConfig['sundayclr'];
//$weekdayclr = '#DDDDDD'; # color calendarweekday
$weekdayclr = @$xoopsModuleConfig['weekdayclr'];

# arrows & colors for mini_calendar (include)
$mini_arrowleft = $agendax_url . '/images/mini_arrowleft.gif';
$mini_arrowright = $agendax_url . '/images/mini_arrowright.gif';
$mini_headbackground = $agendax_url . '/images/headbackground.gif';
$mini_weekday = $agendax_url . '/images/weekday_';
$mini_todaybg = $agendax_url . '/images/rect.gif';
$mini_bgcolor = 'white'; # width of table
$mini_tablewidth = '138'; # width of table
$mini_monthborder = '1'; # tableborder or not
$mini_tdwidth = '20'; # width of cell
$mini_tdtopheight = '20'; # standard height of top <tr> cell
$mini_tddayheight = '20'; # standar height of weekday-cell
$mini_tdheight = '20'; # standard height of day-cell
$mini_calfontasked = '-2'; # link asked month
