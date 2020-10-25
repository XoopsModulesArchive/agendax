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

$modversion['name'] = _MI_AGENDAX_NAME;
$modversion['version'] = '2.2.0';
$modversion['description'] = _MI_AGENDAX_DESC;
$modversion['credits'] = 'Wang Jue (wjue@wjue.org)';
$modversion['author'] = 'written for Xoops<br>by Wang Jue (aka wjue)<br>http://www.guanxiCRM.com and http://www.wjue.org';
$modversion['license'] = 'GPL LICENSE with wjue amendment';
$modversion['official'] = 0;
$modversion['image'] = 'agendax_slogo.png';
$modversion['dirname'] = 'agendax';

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Blocks
$modversion['blocks'][1]['file'] = 'minical.php';
$modversion['blocks'][1]['name'] = _AGX_MINICAL;
$modversion['blocks'][1]['show_func'] = 'minical_show';
$modversion['blocks'][1]['description'] = 'Display a mini calendar in a block';
$modversion['blocks'][1]['options'] = '1|1|0|http://www.China-Offshore.com|modules/agendax/images/minical';
$modversion['blocks'][1]['edit_func'] = 'b_agx_mcal_opt_edit';
$modversion['blocks'][1]['template'] = 'minical_block.html';

$modversion['blocks'][2]['file'] = 'minical.php';
$modversion['blocks'][2]['name'] = _AGX_WEEKEVENT;
$modversion['blocks'][2]['show_func'] = 'thisWeek_show';
$modversion['blocks'][2]['description'] = '';
$modversion['blocks'][2]['template'] = 'thisweek_block.html';

$modversion['blocks'][3]['file'] = 'minical.php';
$modversion['blocks'][3]['name'] = _AGX_MONTHEVENT;
$modversion['blocks'][3]['show_func'] = 'thisMonth_show';
$modversion['blocks'][3]['description'] = '';
$modversion['blocks'][3]['template'] = 'thismonth_block.html';

//$modversion['blocks'][4]['file'] = "minical.php";
//$modversion['blocks'][4]['name'] = 'waiting_events';
//$modversion['blocks'][4]['show_func'] = "pending_approval_show";
//$modversion['blocks'][4]['description'] = "";
//$modversion['blocks'][4]['template'] = 'waiting_events_block.html';

// Menu
$modversion['hasMain'] = 1;
//$modversion['sub'][1]['name'] =_AGXADD_EVENT;
//$modversion['sub'][1]['url'] = "addevent.php";

// Templates
$modversion['templates'][1]['file'] = 'agendax_index.html';
$modversion['templates'][1]['description'] = 'Agenda-X main template file';

$modversion['templates'][2]['file'] = 'agendax_searchform.html';
$modversion['templates'][2]['description'] = 'searchform template file';

$modversion['templates'][3]['file'] = 'agendax_flatview.html';
$modversion['templates'][3]['description'] = 'flatview template file';

$modversion['templates'][4]['file'] = 'agendax_overview_cats.html';
$modversion['templates'][4]['description'] = 'categories overview template file';

$modversion['templates'][5]['file'] = 'agendax_dayview.html';
$modversion['templates'][5]['description'] = 'Day view template file';

$modversion['templates'][6]['file'] = 'agendax_weekview.html';
$modversion['templates'][6]['description'] = 'Week view template file';

$modversion['templates'][7]['file'] = 'agendax_searchresults.html';
$modversion['templates'][7]['description'] = 'Search results template file';

$modversion['templates'][8]['file'] = 'agendax_monthview.html';
$modversion['templates'][8]['description'] = 'Month view template file';

$modversion['templates'][9]['file'] = 'agendax_categoryview.html';
$modversion['templates'][9]['description'] = 'Category view template file';

$modversion['templates'][10]['file'] = 'agendax_eventform.html';
$modversion['templates'][10]['description'] = 'Event form template file';

$modversion['templates'][11]['file'] = 'agendax_viewevent.html';
$modversion['templates'][11]['description'] = 'Event View template file';

$modversion['templates'][12]['file'] = 'agendax_adminmenu.html';
$modversion['templates'][12]['description'] = 'Admin menu template file';

$modversion['templates'][13]['file'] = 'agendax_adminoutofdate.html';
$modversion['templates'][13]['description'] = '';

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'search.inc.php';
$modversion['search']['func'] = 'agendax_xoops_search';

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
//$modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'agendax_cat';
$modversion['tables'][1] = 'agendax_events';
$modversion['tables'][2] = 'agendax_event_repeats';
$modversion['tables'][3] = 'agendax_event_repeats_not';
$modversion['tables'][4] = 'agendax_mcalurl';

/* name of config option for accessing its specified value. i.e. $xoopsModuleConfig['name']
title of this config option displayed in config settings form
description of this config option displayed under title
form element type used in config form for this option. can be one of either textbox, textarea, select, select_multi, yesno, group, group_multi
value type of this config option. can be one of either int, text, float, array, or other
form type of group_multi, select_multi must always be value type of array
the default value for this option
ignore it if no default
'yesno' formtype must be either 0(no) or 1(yes)
*/

$modversion['config'][1]['name'] = 'caldefault';
$modversion['config'][1]['title'] = '_MI_AGX_CALDEFAULT_TITLE';
$modversion['config'][1]['formtype'] = 'select';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = 'flat';
$modversion['config'][1]['options'] = ['_MI_AGX_CALDEFAULT_FLAT' => 'flat', '_MI_AGX_CALDEFAULT_DAY' => 'day', '_MI_AGX_CALDEFAULT_WEEK' => 'week', '_MI_AGX_CALDEFAULT_MONTH' => 'month'];

$modversion['config'][2]['name'] = 'caleventapprove';
$modversion['config'][2]['title'] = '_MI_AGX_CALEVENTAPPROVE_TITLE';
$modversion['config'][2]['formtype'] = 'yesno';
$modversion['config'][2]['valuetype'] = 'int';
$modversion['config'][2]['default'] = '1';

$modversion['config'][3]['name'] = 'archive';
$modversion['config'][3]['title'] = '_MI_AGX_ARCHIVE_TITLE';
$modversion['config'][3]['formtype'] = 'yesno';
$modversion['config'][3]['valuetype'] = 'int';
$modversion['config'][3]['default'] = '1';

$modversion['config'][4]['name'] = 'delimiter';
$modversion['config'][4]['title'] = '_MI_AGX_DELIMITER_TITLE';
$modversion['config'][4]['formtype'] = 'textbox';
$modversion['config'][4]['valuetype'] = 'text';
$modversion['config'][4]['default'] = '|';

$modversion['config'][5]['name'] = 'uploadviasite';
$modversion['config'][5]['title'] = '_MI_AGX_UPLOADVIASITE_TITLE';
$modversion['config'][5]['formtype'] = 'yesno';
$modversion['config'][5]['valuetype'] = 'int';
$modversion['config'][5]['default'] = '1';

$modversion['config'][6]['name'] = 'filesize';
$modversion['config'][6]['title'] = '_MI_AGX_FILESIZE_TITLE';
$modversion['config'][6]['formtype'] = 'textbox';
$modversion['config'][6]['valuetype'] = 'int';
$modversion['config'][6]['default'] = '100000';

$modversion['config'][7]['name'] = 'email_should_be_validated';
$modversion['config'][7]['title'] = '_MI_AGX_VAL_EMAIL_TITLE';
$modversion['config'][7]['formtype'] = 'yesno';
$modversion['config'][7]['valuetype'] = 'int';
$modversion['config'][7]['default'] = '1';

$modversion['config'][8]['name'] = 'usearrows';
$modversion['config'][8]['title'] = '_MI_AGX_USEARROWS_TITLE';
$modversion['config'][8]['formtype'] = 'yesno';
$modversion['config'][8]['valuetype'] = 'int';
$modversion['config'][8]['default'] = '1';

$modversion['config'][9]['name'] = 'displaylink';
$modversion['config'][9]['title'] = '_MI_AGX_DISPLAYLINK_TITLE';
$modversion['config'][9]['formtype'] = 'yesno';
$modversion['config'][9]['valuetype'] = 'int';
$modversion['config'][9]['default'] = '1';

$modversion['config'][10]['name'] = 'monthborder';
$modversion['config'][10]['title'] = '_MI_AGX_MONTHBORDER_TITLE';
$modversion['config'][10]['formtype'] = 'yesno';
$modversion['config'][10]['valuetype'] = 'int';
$modversion['config'][10]['default'] = '1';

$modversion['config'][11]['name'] = 'tablewidth';
$modversion['config'][11]['title'] = '_MI_AGX_TABLEWIDTH_TITLE';
$modversion['config'][11]['formtype'] = 'textbox';
$modversion['config'][11]['valuetype'] = 'text';
$modversion['config'][11]['default'] = '98%';

$modversion['config'][12]['name'] = 'tdwidth';
$modversion['config'][12]['title'] = '_MI_AGX_TDWIDTH_TITLE';
$modversion['config'][12]['formtype'] = 'textbox';
$modversion['config'][12]['valuetype'] = 'text';
$modversion['config'][12]['default'] = '14%';

$modversion['config'][13]['name'] = 'tdtopheight';
$modversion['config'][13]['title'] = '_MI_AGX_TDTOPHEIGHT_TITLE';
$modversion['config'][13]['formtype'] = 'textbox';
$modversion['config'][13]['valuetype'] = 'int';
$modversion['config'][13]['default'] = '20';

$modversion['config'][14]['name'] = 'tddayheight';
$modversion['config'][14]['title'] = '_MI_AGX_TDDAYHEIGHT_TITLE';
$modversion['config'][14]['formtype'] = 'textbox';
$modversion['config'][14]['valuetype'] = 'int';
$modversion['config'][14]['default'] = '20';

$modversion['config'][15]['name'] = 'tdheight';
$modversion['config'][15]['title'] = '_MI_AGX_TDHEIGHT_TITLE';
$modversion['config'][15]['formtype'] = 'textbox';
$modversion['config'][15]['valuetype'] = 'int';
$modversion['config'][15]['default'] = '60';

$modversion['config'][16]['name'] = 'calcells';
$modversion['config'][16]['title'] = '_MI_AGX_CALCELLS_TITLE';
$modversion['config'][16]['formtype'] = 'textbox';
$modversion['config'][16]['valuetype'] = 'int';
$modversion['config'][16]['default'] = '1';

$modversion['config'][17]['name'] = 'calcellp';
$modversion['config'][17]['title'] = '_MI_AGX_CALCELLP_TITLE';
$modversion['config'][17]['formtype'] = 'textbox';
$modversion['config'][17]['valuetype'] = 'int';
$modversion['config'][17]['default'] = '0';

$modversion['config'][18]['name'] = 'trtopcolor';
$modversion['config'][18]['title'] = '_MI_AGX_TRTOPCOLOR_TITLE';
$modversion['config'][18]['formtype'] = 'textbox';
$modversion['config'][18]['valuetype'] = 'text';
$modversion['config'][18]['default'] = '#CCCCCC';

$modversion['config'][19]['name'] = 'sundaytopclr';
$modversion['config'][19]['title'] = '_MI_AGX_SUNDAYTOPCLR_TITLE';
$modversion['config'][19]['formtype'] = 'textbox';
$modversion['config'][19]['valuetype'] = 'text';
$modversion['config'][19]['default'] = '#80E1A8';

$modversion['config'][20]['name'] = 'weekdaytopclr';
$modversion['config'][20]['title'] = '_MI_AGX_WEEKDAYTOPCLR_TITLE';
$modversion['config'][20]['formtype'] = 'textbox';
$modversion['config'][20]['valuetype'] = 'text';
$modversion['config'][20]['default'] = '#DDDDDD';

$modversion['config'][21]['name'] = 'sundayemptyclr';
$modversion['config'][21]['title'] = '_MI_AGX_SUNDAYEMPTYCLR_TITLE';
$modversion['config'][21]['formtype'] = 'textbox';
$modversion['config'][21]['valuetype'] = 'text';
$modversion['config'][21]['default'] = '#F0F0F0';

$modversion['config'][22]['name'] = 'weekdayemptyclr';
$modversion['config'][22]['title'] = '_MI_AGX_WEEKDAYEMPTYCLR_TITLE';
$modversion['config'][22]['formtype'] = 'textbox';
$modversion['config'][22]['valuetype'] = 'text';
$modversion['config'][22]['default'] = '#F0F0F0';

$modversion['config'][23]['name'] = 'todayclr';
$modversion['config'][23]['title'] = '_MI_AGX_TODAYCLR_TITLE';
$modversion['config'][23]['formtype'] = 'textbox';
$modversion['config'][23]['valuetype'] = 'text';
$modversion['config'][23]['default'] = '#AAAAAA';

$modversion['config'][24]['name'] = 'sundayclr';
$modversion['config'][24]['title'] = '_MI_AGX_SUNDAYCLR_TITLE';
$modversion['config'][24]['formtype'] = 'textbox';
$modversion['config'][24]['valuetype'] = 'text';
$modversion['config'][24]['default'] = '#80E1A8';

$modversion['config'][25]['name'] = 'weekdayclr';
$modversion['config'][25]['title'] = '_MI_AGX_WEEKDAYCLR_TITLE';
$modversion['config'][25]['formtype'] = 'textbox';
$modversion['config'][25]['valuetype'] = 'text';
$modversion['config'][25]['default'] = '#DDDDDD';
