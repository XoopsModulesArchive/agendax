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

require '../config.inc.php';

# some settings of vars
$op    = $_GET['op'] ?? '';
$month = $_GET['month'] ?? '';
$year  = $_GET['year'] ?? '';
$date  = $_GET['date'] ?? '';
$ask   = $_GET['ask'] ?? '';
$da    = $_GET['da'] ?? '';
$mo    = $_GET['mo'] ?? '';
$ye    = $_GET['ye'] ?? '';
$next  = $_GET['next'] ?? '';
$prev  = $_GET['prev'] ?? '';
$id    = $_GET['id'] ?? '';
if (isset($_GET['userid'])) {
    $userid = $_GET['userid'];
}
