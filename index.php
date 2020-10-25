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

if (!isset($xoopsIntro)) {
    require dirname(__DIR__, 2) . '/mainfile.php';

    $GLOBALS['xoopsOption']['template_main'] = 'agendax_index.html';

    require XOOPS_ROOT_PATH . '/header.php';

    $xoopsIntro = true;
}

# header include
include './agendax_header.inc.php';

if (!defined('_SHORTDATESTRING')) {
    define('_SHORTDATESTRING', 'Y/n/j');
}

#include some functions
require_once './functions.inc.php';

require_once './include/agendax.class.php';

#print date of today
$date_str = '';

$weekday = date('w', mktime(12, 0, 0, $m, $d, $y));
$weekday++;
if (_SHORTDATESTRING == 'n/j/Y') {
    $date_str .= _($week[$weekday]) . ' ' . _($maand[$m]) . ' ' . $d . $dateSuffix['ri'] . ' ' . $y . $dateSuffix['nian'];
} elseif (_SHORTDATESTRING == 'j/n/Y') {
    $date_str .= _($week[$weekday]) . ' ' . $d . $dateSuffix['ri'] . ' ' . _($maand[$m]) . ' ' . $y . $dateSuffix['nian'];
} else {          //default to "Y/n/j"
    $date_str .= $y . $dateSuffix['nian'] . ' ' . _($maand[$m]) . ' ' . $d . $dateSuffix['ri'] . ' ' . _($week[$weekday]);
}

$agendax['date'] = '<div class=axDateToday>' . _('Today is') . ' ' . $date_str . '</div><br><br>';

unset($date_str);
//reset $output_str
$output_str = '';

/*
** Specify the permission we are going to check for. This must be one of the permission names created on the admin side.
*/
$perm_name = 'Global Permission';

/*
** Get group ids that the current user belongs to.
*/
if ($xoopsUser) {
    $groups = $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}

/*
** Get the current module ID.
*/
$module_id = $xoopsModule->getVar('mid');

/*
** Get the group permission handler.
*/
$gpermHandler = xoops_getHandler('groupperm');

/*
** The unique id of an item to check permissions for.
*/
$perm_itemid = AGENDAX_PERM_EDITDELETE_EVENTS;

/*
** check permission
*/
if ($gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
    // will be used in functions.php file

    $agendax_edit_permission_ok = true;
}

// admin menu bar if adequat
$agendax_isadmin = false;
if ($xoopsUser) {
    $xoopsModule = XoopsModule::getByDirname('agendax');

    if ($xoopsUser->isAdmin($xoopsModule->mid())) {
        $agendax_isadmin = true;
    }
}

$perm_itemid = AGENDAX_PERM_APPROVE; //permission to approve events
$can_approve = $gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id);

if ($agendax_isadmin) {
    $agendax['navbar'] .= '<a href="index.php?op=outof">' . _('Active&Expired events') . '</a>' . ' | ';
}
if ($can_approve) {
    $agendax['navbar'] .= '<a href="index.php?op=pending">' . _('Pending Events') . '</a>';
}
if ($agendax_isadmin) {
    $agendax['navbar'] .= ' | <a href="http://www.wjue.org/support/">' . _('Tech Support') . '</a><br><br></div>';
}

require_once './include/functions.php';

# -------------------------------
# Switch controlling all actions
# -------------------------------
switch ($op) {
    # -------------------------------------------------------------------
    # overview of category
    # -------------------------------------------------------------------
    case'cats':
    {
        include './overview_cats.inc.php';
        break;
    }

    # -------------------------------------------------------------------
    # overview of one cat
    # -------------------------------------------------------------------
    case'cat':
    {
        include './categoryview.inc.php';
        break;
    }

    # ----------------------------------------------------------------------
    # add new category
    # ----------------------------------------------------------------------
    case'addcat':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_MANAGE_CATEGORIES;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            $cat = trim(Agendax::getPost('cat'));
            if (!empty($cat)) {
                $query = 'insert into ' . XOOPS_DB_PREFIX . "_agendax_cat values('','" . addslashes($cat) . "')";

                $GLOBALS['xoopsDB']->queryF($query);
            }
            redirect_header('index.php?op=cats', 2, _('Data base updated '));
            exit();
    }
    # ----------------------------------------------------------------------
    # edit category
    # ----------------------------------------------------------------------
    case 'editcat':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_MANAGE_CATEGORIES;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            $query = 'select cat_name from ' . XOOPS_DB_PREFIX . "_agendax_cat where cat_id='$id'";
            $result = $GLOBALS['xoopsDB']->queryF($query);
            $row = $GLOBALS['xoopsDB']->fetchObject($result);
            $output_str .= "<form action=index.php?op=updatecat&id=$id method=post>\n";
            $output_str .= '<input type=text name=cat value="' . stripslashes($row->cat_name) . "\"><br>\n";
            $output_str .= '<input type=submit value="' . _('update') . "\">\n";
            $output_str .= "</form>\n";
            break;
    }
    # ----------------------------------------------------------------------
    # update category
    # ----------------------------------------------------------------------
    case'updatecat':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_MANAGE_CATEGORIES;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            $cat = trim(Agendax::getPost('cat'));
            if (!empty($cat)) {
                $query = 'update ' . XOOPS_DB_PREFIX . "_agendax_cat set cat_name='" . addslashes($cat) . "' where cat_id='$id'";

                $GLOBALS['xoopsDB']->queryF($query);
            }
            redirect_header('index.php?op=cats', 2, _('Data base updated '));
            exit();
    }
    # ----------------------------------------------------------------------
    # delete cat: question
    # ----------------------------------------------------------------------
    case'delcat':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_MANAGE_CATEGORIES;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/indÜx.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            $output_str .= _(
                'Are you sure to remove this category ? All events associated with this category will be permanently deleted !
'
            ) . "<br><br>\n";
            $output_str .= '<a href=javascript:history.back()>' . _('No, I am not sure') . "</a><br><br>\n";
            $output_str .= "<a href=indÜx.php?op=delcatok&id=$id>" . _('Yes, I confirm') . "</a>\n";
            break;
    }

    # ----------------------------------------------------------------------
    # confirmation of delete cat
    # ----------------------------------------------------------------------
    case 'delcatok':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_MANAGE_CATEGORIES;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/indÜx.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            $GLOBALS['xoopsDB']->queryF('DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_cat WHERE cat_id='$id'");

            # del events with cat = $id
            $querysel = 'SELECT id FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE cat='$id'";
            $resultsel = $GLOBALS['xoopsDB']->queryF($querysel);
            while (false !== ($rowsel = $GLOBALS['xoopsDB']->fetchObject($resultsel))) {
                $querypic = 'SELECT picture, type FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE id = '" . $rowsel->id . "'";

                $result = $GLOBALS['xoopsDB']->queryF($querypic);

                $row = $GLOBALS['xoopsDB']->fetchObject($result);

                if ('' != $row->picture) {
                    unlink(XOOPS_ROOT_PATH . '/uploads/' . $row->picture);
                }

                $querydel = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE id='" . $rowsel->id . "'";

                $GLOBALS['xoopsDB']->queryF($querydel);

                if ('R' == $row->type) {
                    $querydelrpt = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_event_repeats WHERE event_id='" . $rowsel->id . "'";

                    $GLOBALS['xoopsDB']->queryF($querydelrpt);

                    $querydelrpt_not = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_event_repeats_not WHERE event_id='" . $rowsel->id . "'";

                    $GLOBALS['xoopsDB']->queryF($querydelrpt_not);
                }
            }
            redirect_header('indÜx.php?op=cats', 2, _('Data base updated '));
            exit();
    }
    # -------------------------------------------------------------------
    # add event form
    # -------------------------------------------------------------------
    case'eventform':
    {
        include './eventform.inc.php';
        break;
    }

    # ----------------------------------------------------------------------
    # approve event
    # ----------------------------------------------------------------------
    case 'approve':
    {
        if (!$can_approve) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/indÜx.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed to approve
            */

            $GLOBALS['xoopsDB']->queryF('update ' . XOOPS_DB_PREFIX . "_agendax_events set approved='1' where id='$id'");
            redirect_header('indÜx.php', 2, _('Data base updated '));
            exit();
    }

    # ----------------------------------------------------------------------
    # delete event: question
    # ----------------------------------------------------------------------
    case'delev':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_EDITDELETE_EVENTS;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/indÜx.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            $output_str .= _('Do you really want to delete this event') . ' ? <br>' . _('Caution : In case of recurring event, all occurrences will also be deleted') . '.<br>';
            $output_str .= back();
            $output_str .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=indÜx.php?op=delevok&id=$id>" . _('Yes, delete it') . '</a>';
            break;
    }
    # ----------------------------------------------------------------------
    # delete event: ok
    # ----------------------------------------------------------------------
    case'delevok':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_EDITDELETE_EVENTS;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            $querypic = 'SELECT id, picture, type FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE id = '$id' OR group_id = '$id'";
            $result = $GLOBALS['xoopsDB']->queryF($querypic);
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($result))) {
                if ('' != $row['picture']) {
                    @unlink(XOOPS_ROOT_PATH . '/uploads/' . $row['picture']);
                }

                $query = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE id='" . $row['id'] . "'";

                $res = $GLOBALS['xoopsDB']->queryF($query) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

                if ('R' == $row['type']) {
                    $query = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_event_repeats WHERE event_id='" . $row['id'] . "'";

                    $res = $GLOBALS['xoopsDB']->queryF($query) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

                    $query = 'DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_event_repeats_not WHERE event_id='" . $row['id'] . "'";

                    $res = $GLOBALS['xoopsDB']->queryF($query) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());
                }
            }
            redirect_header('index.php', 2, _('Data base updated '));

            break;
    }
    # ----------------------------------------------------------------------
    # delete all out of date events: question
    # ----------------------------------------------------------------------
    case'delalloodev':
    {
        if (!$agendax_isadmin) {
            redirect_header('index.php', 2, _('Sorry, you are not admin of Agenda-X'));
        }
        $date = $kdate;
        $id = '';
        $query = 'SELECT id, date, type, event_end FROM ' . XOOPS_DB_PREFIX . '_agendax_events LEFT JOIN ' . XOOPS_DB_PREFIX . '_agendax_event_repeats ON event_id=id WHERE ';
        $query .= "(date<'$date' AND type='E') OR type='R' ORDER BY id ASC";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
            if ('R' == $row->type) {
                if (null != $row->event_end and 0 != $row->event_end and $row->event_end < $date) {
                    $id .= $row->id . '|';
                }
            } else {
                $id .= $row->id . '|';
            }
        }

        $output_str .= _('Do you really want to delete all out of date events') . " ?\n";
        $output_str .= back();
        $output_str .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=index.php?op=delalloodevok&id=$id>" . _('Yes, delete them') . "</a>\n";
        break;
    }

    # ----------------------------------------------------------------------
    # delete all out of date events: ok
    # ----------------------------------------------------------------------
    case'delalloodevok':
    {
        if (!$agendax_isadmin) {
            redirect_header('index.php', 2, _('Sorry, you are not admin of Agenda-X'));
        }
        $id = explode('|', $id);

        for ($i = 0, $iMax = count($id); $i < $iMax; $i++) {
            $GLOBALS['xoopsDB']->queryF('DELETE FROM ' . XOOPS_DB_PREFIX . "_agendax_events WHERE id='" . $id[$i] . "'");
        }

        header('Location: index.php');

        break;
    }

    # -------------------------------------------------------------------
    # add event
    # -------------------------------------------------------------------
    case 'addevent':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_SUBMIT;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            include './addevent.inc.php';
            break;
    }
    # -------------------------------------------------------------------
    # view details of event
    # -------------------------------------------------------------------
    case 'view':
    {
        include './viewevent.inc.php';
        break;
    }

    # -------------------------------------------------------------------
    # view per day
    # -------------------------------------------------------------------
    case'day':
    {
        include './dayview.inc.php';
        break;
    }

    # -------------------------------------------------------------------
    # view per week
    # -------------------------------------------------------------------
    case'week':
    {
        include './weekview.inc.php';
        break;
    }

    # -------------------------------------------------------------------
    # view cal per month
    # -------------------------------------------------------------------
    case'cal':
    {
        include './monthview.inc.php';
        break;
    }

    # -------------------------------------------------------------------
    # view events per month, flyer style
    # -------------------------------------------------------------------
    case'flat':
    {
        include './flatview.inc.php';
        break;
    }

    case'res':
    {
        include './searchresults.inc.php';
        break;
    }

    case 'search':
    {
        $GLOBALS['xoopsOption']['template_main'] = 'agendax_searchform.html';
        $agendax['searchform'] = search();
        break;
    }
    # ----------------------------------------------------------------------
    # out-of-date events
    # ----------------------------------------------------------------------
    case'outof':
    {
        include './admin/outofdate.inc.php';
        break;
    }

    # ----------------------------------------------------------------------
    # update event
    # ----------------------------------------------------------------------
    case'upevent':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_EDITDELETE_EVENTS;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            include './admin/update.inc.php';
            break;
    }
    # ----------------------------------------------------------------------
    # update event (exception)
    # ----------------------------------------------------------------------
    case 'exception':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_EDITDELETE_EVENTS;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            include './admin/exception.inc.php';
            break;
    }
    # ----------------------------------------------------------------------
    # edit event form
    # ----------------------------------------------------------------------
    case'edit':
    {
        /*
        ** The unique id of an item to check permissions for.
        */
        $perm_itemid = AGENDAX_PERM_EDITDELETE_EVENTS;

        /*
        ** check permission
        */
        if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            // not allowed

            redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

            exit;
        }
            /*
            ** allowed
            */
            if (!isset($_GET['override'])) {
                include './admin/edit.inc.php';
            } else {
                include './admin/edit4thisdate.inc.php';
            }
            break;
    }
    # ----------------------------------------------------------------------
    # Minical Image Url setting
    # ----------------------------------------------------------------------
    //  case "mi":
    //  {
    //      if (!$agendax_isadmin) redirect_header("index.php", 2, _("Sorry, you are not admin of Agenda-X"));
    //      include ("./admin/mcalurl.inc.php");
    //      break;
    //  }
    //
    # ----------------------------------------------------------------------
    # edit event form
    # ----------------------------------------------------------------------
    case'pending':
    {
        if (!$can_approve) {
            redirect_header('index.php', 2, _('Sorry, you are not admin of Agenda-X'));
        }
        $query = 'select id,title,cat_name,date from ' . XOOPS_DB_PREFIX . '_agendax_events left join ' . XOOPS_DB_PREFIX . '_agendax_cat on ' . XOOPS_DB_PREFIX . '_agendax_events.cat=' . XOOPS_DB_PREFIX . '_agendax_cat.cat_id ';
        $query .= "where approved='0' order by date ASC";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        $rows = $GLOBALS['xoopsDB']->getRowsNum($result);
        if (!$rows) {
            $output_str .= '<h3>' . _('Pending Approval') . "</h3>\n";
        } else {
            $output_str .= '<h3>' . _('Pending Approval') . ' (' . $rows . ")</h3>\n";

            $foo = '';

            while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
                $foo++ % 2 ? $color = 'BBBBBB' : $color = 'EEEEEE';

                $output_str .= "<table border=1 bgcolor=$color cellspacing=0 cellpadding=4 width=\"100%\">\n";

                $output_str .= "<tr><td>\n<li><b>" . stripslashes($row->title) . '</b> ' . _('on') . ' ' . $row->date;

                $output_str .= ' - ' . _('Category') . ' : ' . $row->cat_name . "\n";

                $output_str .= ' - <a href=index.php?op=view&id=' . $row->id . '>' . 'view' . "</a>\n";

                $output_str .= ' - <a href=index.php?op=edit&id=' . $row->id . '>' . 'edit' . "</a>\n";

                $output_str .= ' - <a href=index.php?op=approve&id=' . $row->id . '>' . 'approve' . "</a>\n";

                $output_str .= ' - <a href=index.php?op=delev&id=' . $row->id . '>' . 'Delete' . "</a>\n";

                $output_str .= "</td></tr>\n";

                $output_str .= "</table>\n";
            }
        }
        break;
    }

    # default: look what's chosing in config and redirect
    default:
    {
        if ('day' == $caldefault) {
            header("location: $agendax_url/index.php?op=day");

            exit();
        }

        if ('week' == $caldefault) {
            header("location: $agendax_url/index.php?op=week");

            exit();
        }

        if ('month' == $caldefault) {
            header("location: $agendax_url/index.php?op=cal");

            exit();
        }
        if ('flat' == $caldefault) {
            header("location: $agendax_url/index.php?op=flat");

            exit();
        }
        break;
    }
}

# footer
//include ('agendax_footer.inc.php');

//Removing of this credit notice voids the free use licence of Agenda-X
$agendax['body'] = $output_str . "<HR><div align=\"center\"><small>Agenda-X v$agd_version by <a href=\"http://China-Offshore.com\">wjue(Wang Jue)</a> & <a href=\"http://www.guanxiCRM.com\">guanxiCRM.com</a></small></div>";
unset($output_str);

$agendax['credits'] = "<HR><div align=\"center\"><small>Agenda-X v$agd_version by <a href=\"http://China-Offshore.com\">wjue(Wang Jue)</a> & <a href=\"http://www.guanxiCRM.com\">guanxiCRM.com</a></small></div>";
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
$xoopsTpl->assign('agendax', $agendax);

// Xoops footer
require XOOPS_ROOT_PATH . '/footer.php';
