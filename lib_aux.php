<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2017 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function cforms2_dbg($m) {
    if (WP_DEBUG_CFORMS2)
        trigger_error('DEBUG cforms2: ' . $m);

}

function cforms2_get_the_id($no) {
    if (isset($_POST['comment_post_ID' . $no]) && $_POST['comment_post_ID' . $no])
        return (int) $_POST['comment_post_ID' . $no];
    else
        return get_the_ID();

}

/** make time */
function cforms2_make_time($t) {
    $time = str_replace('/', '.', $t) . sprintf(' %+d', get_option('gmt_offset'));
    $time = strtotime($time);
    if ($time === false)
        return 0;
    return $time;

}

/** check time constraints */
function cforms2_check_time($no) {
    global $cformsSettings;

    $t1f = $t2f = false;

    if (strlen($cformsSettings['form' . $no]['cforms' . $no . '_startdate']) > 1)
        $t1 = cforms2_make_time(stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_startdate']));
    else
        $t1f = true;

    if (strlen($cformsSettings['form' . $no]['cforms' . $no . '_enddate']) > 1)
        $t2 = cforms2_make_time(stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_enddate']));
    else
        $t2f = true;

    return ($t1f || $t1 <= current_time('timestamp')) && ($t2f || $t2 >= current_time('timestamp'));

}

/** sanitize label ID's */
function cforms2_sanitize_ids($t) {

    $t = strip_tags($t);
    $t = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $t);
    $t = str_replace('%', '', $t);
    $t = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $t);

    $t = remove_accents($t);
    if (seems_utf8($t)) {
        $t = utf8_uri_encode($t, 200);
    }

    $t = preg_replace('/&.+?;/', '', $t); // kill entities
    $t = preg_replace('/\s+/', '-', $t);
    $t = preg_replace('|-+|', '-', $t);
    $t = preg_replace("|'|", '-', $t);
    $t = trim($t, '-');

    $t = str_replace('[', '', $t);
    $t = str_replace(']', '', $t);

    return $t;

}

/** map data */
function cforms2_all_tracks($session) {
    $t = array();

    // clean up underscores
    foreach (array_keys($session) as $key) {
        if (strpos($key, 'cf_') === false)
            continue;
        foreach ($session[$key] as $k => $v) {
            $t[$key . '_' . $k] = $v;
        }
    }

    return $t;

}

function cforms2_format_email($track, $no) {
    global $cformsSettings;
    $customspace = (int) ($cformsSettings['form' . $no]['cforms' . $no . '_space'] > 0) ? $cformsSettings['form' . $no]['cforms' . $no . '_space'] : 30;

    $t = $h = '';

    $eol = ($cformsSettings['global']['cforms_crlf']['b'] != 1) ? "\r\n" : "\n";

    foreach (array_keys($track) as $k) {

        $v = stripslashes($track[$k]);

        // Exclude empty fields?
        if ($v == '' && $cformsSettings['form' . $no]['cforms' . $no . '_emptyoff'])
            continue;

        if (preg_match('/\$\$\$/', $k))
            continue;

        if (strpos($k, 'cf_form') !== false && preg_match('/^cf_form\d*_(.+)/', $k, $r))
            $k = $r[1];

        if (strpos($k, '___') !== false && preg_match('/^(.+)___\d+/', $k, $r))
            $k = $r[1];

        // fieldsets
        if (strpos($k, 'FieldsetEnd') !== false) {
            $t .= $eol;
            $h .= '<tr><td style="" colspan="2">&nbsp;</td></tr>' . $eol;
            continue;
        } elseif (strpos($k, 'Fieldset') !== false) {
            $space = '-';
            $n = ((($customspace * 2) + 2) - strlen(strip_tags($v))) / 2;
            $n = ($n < 0) ? 0 : $n;
            if (strlen(strip_tags($v)) < (($customspace * 2) - 2))
                $space = str_repeat("-", $n);

            $t .= substr($eol . "$space" . stripslashes(strip_tags($v)) . "$space", 0, ($customspace * 2)) . $eol . $eol;
            $h .= '<tr><td ' . $cformsSettings['global']['cforms_style']['fs'] . ' colspan="2">' . $v . '</td></tr>' . $eol;
            continue;
        }

        // Upload fields?
        if (strpos($k, '[*') !== false) {
            $k = substr($k, 0, strpos($k, '[*'));
        }

        // HTML = TEXT (key, value)
        $hk = $k;
        $hv = htmlspecialchars($v);

        // checkboxes get a symbol for value
        if ($v == '(x)')
            $hv = "<strong>&#10004;</strong>";

        // CRs for textareas \r\n user input hardcoded!
        if (strpos($v, "\n") !== false) {
            $k = $eol . $k;
            $hv = str_replace("\r\n", "\n", $v);
            $hv = str_replace("\n", '<br />' . $eol, $hv);
            $v = $eol . $v . $eol;
        }

        // TEXT: spacing
        $space = '';
        if (strlen(stripslashes($k)) < $customspace) // don't count ->\"  sometimes adds more spaces?!?
            $space = str_repeat(" ", $customspace - strlen(stripslashes($k)));

        // create formdata block for email
        $t .= stripslashes(strip_tags($k)) . ': ' . $space . $v . $eol;
        $h .= '<tr><td ' . $cformsSettings['global']['cforms_style']['key_td'] . '>' . $hk . '</td><td ' . $cformsSettings['global']['cforms_style']['val_td'] . '>' . $hv . '</td></tr>' . $eol;
    }
    $r['text'] = $t;
    $r['html'] = '<div ' . $cformsSettings['global']['cforms_style']['admin'] . '><span ' . $cformsSettings['global']['cforms_style']['title'] . '>' . stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_fname']) . '</span><table cellpadding="0" cellspacing="0" ' . $cformsSettings['global']['cforms_style']['table'] . '>' . stripslashes($h) . '</table></div>';
    return $r;

}

function cforms2_get_current_page() {

    $page = $_SERVER['REQUEST_URI'];

    $page = (trim($page) == '' || strpos($page, 'admin-ajax.php') !== false ) ? $_SERVER['HTTP_REFERER'] : trim($page); // for ajax
    return htmlspecialchars($page);

}

/** check for post custom fields in string */
function cforms2_check_post_vars($fv) {
    preg_match_all('/\\{([^\\{]+)\\}/', $fv, $fall);

    if (count($fall[1]) > 0) {

        $custArr = get_post_custom();
        foreach ($fall[1] as $fvar) {
            if (!empty($custArr[$fvar][0]))
                $fv = str_replace('{' . $fvar . '}', $custArr[$fvar][0], $fv);
        }
    }
    return $fv;

}

/** look for default/system variables */
function cforms2_check_default_vars($m, $no) {
    global $cformsSettings;

    $eol = ($cformsSettings['global']['cforms_crlf']['b'] != 1) ? "\r\n" : "\n";

    $pid = cforms2_get_the_id($no);

    if (isset($_POST['cforms_pl' . $no]) && $_POST['cforms_pl' . $no])
        $permalink = $_POST['cforms_pl' . $no];
    else
        $permalink = get_permalink($pid);

    $date = current_time(get_option('date_format'));

    $time = current_time(get_option('time_format'));
    $page = cforms2_get_current_page();

    $post = get_post($pid);
    if (!empty($post)) {
        $user = get_user_by('id', $post->post_author);
        $user_name = $user->display_name;
        $post_title = $post->post_title;
        $post_excerpt = $post->post_excerpt;
    } else {
        $user_name = $post_title = $post_excerpt = '';
    }

    $current_user = wp_get_current_user();

    if (isset($_SERVER['HTTP_REFERER']))
        $m = str_replace('{Referer}', $_SERVER['HTTP_REFERER'], $m);
    $m = str_replace('{PostID}', $pid, $m);
    $m = str_replace('{Form Name}', $cformsSettings['form' . $no]['cforms' . $no . '_fname'], $m);
    $m = str_replace('{Page}', $page, $m);
    $m = str_replace('{Date}', $date, $m);
    $m = str_replace('{Author}', $user_name, $m);
    $m = str_replace('{Time}', $time, $m);
    $m = str_replace('{IP}', cforms2_get_ip(), $m);
    $m = str_replace('{BLOGNAME}', get_option('blogname'), $m);

    $m = str_replace('{CurUserID}', $current_user->ID, $m);
    $m = str_replace('{CurUserName}', $current_user->display_name, $m);
    $m = str_replace('{CurUserEmail}', $current_user->user_email, $m);
    $m = str_replace('{CurUserFirstName}', $current_user->user_firstname, $m);
    $m = str_replace('{CurUserLastName}', $current_user->user_lastname, $m);

    $m = str_replace('{Permalink}', $permalink, $m);
    $m = str_replace('{Title}', $post_title, $m);
    $m = str_replace('{Excerpt}', $post_excerpt, $m);

    $m = preg_replace("/\r\n\./", "\n", $m);

    // normalize
    $m = str_replace("\r\n", "\n", $m);
    $m = str_replace("\r", "\n", $m);
    $m = str_replace("\n", $eol, $m);

    return $m;

}

/** look for custom variables */
function cforms2_check_cust_vars($m, $track, $html = false) {

    global $cformsSettings;
    $eol = ($cformsSettings['global']['cforms_crlf']['b'] != 1) ? "\r\n" : "\n";

    preg_match_all('/\\{([^\\{]+)\\}/', $m, $findall);
    if (count($findall[1]) > 0) {
        $allvars = array_keys($track);

        foreach ($findall[1] as $fvar) {

            $fTrackedVar = addslashes($fvar);

            // convert _fieldXYZ to actual label name tracked...
            if (strpos($fvar, '_field') !== false) {
                $fNo = substr($fvar, 6);
                if (!empty($allvars[$fNo]))
                    $fTrackedVar = $track['$$$' . $fNo]; // reset to actual label name and continue
            }

            // convert if alt [id:] used
            if (in_array('$$$' . $fTrackedVar, $allvars)) {
                if (!empty($track['$$$' . $fTrackedVar]))
                    $fTrackedVar = $track['$$$' . $fTrackedVar]; // reset to actual label name and continue
            }

            // check if label name is tracked...
            if (in_array($fTrackedVar, $allvars)) {

                $v = stripslashes($track[$fTrackedVar]);

                // CRs for textareas \r\n user input hardcoded!
                if ($html && strpos($v, "\n") !== false)
                    $v = str_replace("\n", '<br />' . $eol, $v);

                $m = str_replace('{' . $fvar . '}', $v, $m);
            }
        }
    }
    return $m;

}

function cforms2_get_ip() {
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
        elseif (isset($_SERVER["HTTP_CLIENT_IP"]))
            $ip_addr = $_SERVER["HTTP_CLIENT_IP"];
        else
            $ip_addr = $_SERVER["REMOTE_ADDR"];
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR'))
            $ip_addr = getenv('HTTP_X_FORWARDED_FOR');
        elseif (getenv('HTTP_CLIENT_IP'))
            $ip_addr = getenv('HTTP_CLIENT_IP');
        else
            $ip_addr = getenv('REMOTE_ADDR');
    }
    return $ip_addr;

}

function cforms2_compare($a, $b) {
    global $cfdataTMP, $cfsort, $cfsortdir;

    if (!is_array($a) && !is_array($b)) {

        $na = empty($cfdataTMP[$a]['data'][$cfsort]) ? false : $cfdataTMP[$a]['data'][$cfsort];
        $nb = empty($cfdataTMP[$b]['data'][$cfsort]) ? false : $cfdataTMP[$b]['data'][$cfsort];

        if (!($na && $nb)) {
            if (!$na)
                return 1;
            if (!$nb)
                return -1;
            return 0;
        }
    }

    $tmpA = (int) trim($na);
    $tmpB = (int) trim($nb);
    if (is_numeric($na) && is_numeric($nb)) {
        if (stristr($cfsortdir, 'asc') === false) {
            return ($tmpB > $tmpA) ? -1 : 1;
        } else {
            return ($tmpA < $tmpB) ? -1 : 1;
        }
    } else {
        if (stristr($cfsortdir, 'asc') === false) {
            return strcasecmp($nb, $na);
        } else {
            return strcasecmp($na, $nb);
        }
    }

}

if (!function_exists('insert_cform')) {

    /**
     * API function: insert_cform
     * 
     * Inserts a cform anywhere you want. If you use the $customfields parameter, a dynamic form is generated.
     * 
     * A few things to note on dynamic forms:
     * 1. Dynamic forms only work in non-AJAX mode.
     * 2. Each dynamic form references and thus requires a base form defined in the cformsII form settings.
     *    All its settings will be used, except the form (&field) definition.
     * 3. Any of the form fields described in the plugin's HELP! section can be dynamically generated.
     * 
     * @param string $no The numeric ID of the form that you want to render. '' for the first form.
     * @param array $customfields The fields to be used for the dynamic form:
     * 
     *              $customfields['label'][n]      : field name;
     *              $customfields['type'][n]       : input field type;       default: 'textfield';
     *              $customfields['isreq'][n]      : bool;                   default: false;
     *              $customfields['isemail'][n]    : bool;                   default: false;
     *              $customfields['isclear'][n]    : bool;                   default: false;
     *              $customfields['isdisabled'][n] : bool;                   default: false;
     *              $customfields['isreadonly'][n] : bool;                   default: false;
     * 
     *              for each form n. n = 0,1,2... and input field type is one of the values returned
     *              by cforms2_fieldtype::get_id() and the overwriting methods in its subclasses.
     * 
     * @return void The form's HTML output will be printed. The output depends on the submission state.
     */
    function insert_cform($no = '', $customfields = array()) {

        if (!is_numeric($no))
            $no = cforms2_check_form_name($no);

        echo cforms2($no, $customfields);

    }

}

if (!function_exists('get_cforms_entries')) {

    /**
     * API function: get_cforms_entries
     * 
     * This function allows to conveniently retrieve submitted data from the cforms tracking tables.
     * 
     * @param string $fname text string (regexp pattern), e.g. the form name
     * @param string $from DATETIME string (format: Y-m-d H:i:s). Date & time defining the target period, e.g. 2008-09-17 15:00:00
     * @param string $to   DATETIME string (format: Y-m-d H:i:s). Date & time defining the target period, e.g. 2008-09-17 15:00:00
     * @param string $sort 'form', 'id', 'date', 'ip', 'email' or any form input field, e.g. 'Your Name'
     * @param int $limit limiting the number of results, '' (empty or false) = no limits!
     * @param string $sortdir "asc" for ascending or "desc" for descending sort direction
     * 
     * @return array a set of stored form submissions in a multi-dimensional array
     * 
     * @deprecated since version 14.14. Install cforms2-old-tracking-db plugin to continue using it.
     * 
     * Examples:
     * get_cforms_entries() => all data, no filters
     * get_cforms_entries('contact',false,false,'date',5,'desc') => last 5 submissions of "my contact form", order by date
     * get_cforms_entries(false,date ("Y-m-d H:i:s", time()-(3600*2))) => all submissions in the last 2 hours
     */
    function get_cforms_entries($fname = false, $from = false, $to = false, $sort = false, $limit = false, $sortdir = 'asc', $limitstart = 0) {
        global $wpdb, $cfdataTMP, $cfsort, $cfsortdir;

        $cformsSettings = get_option('cforms_settings');

        // unify
        if ($sort === 'date' || $sort === 'timestamp')
            $sort = 'sub_date';
        elseif ($sort === 'form')
            $sort = 'form_id';

        $limit = empty($limit) ? '' : 'LIMIT ' . ((int) $limitstart) . ',' . (int) $limit;

        $sortdir = strtolower($sortdir) === 'asc' ? 'asc' : 'desc';


        $ORDER_1 = $cfsort = '';
        if (in_array($sort, array('id', 'form_id', 'sub_date', 'email', 'ip')))
            $ORDER_1 = "ORDER BY " . $sort . ' ' . $sortdir;
        else {
            $ORDER_1 = "ORDER BY id DESC";
            $cfsort = $sort;
        }

        // SORT
        $cfdata = array();
        $cfsortdir = $sortdir;

        // GENERAL WHERE
        $where = false;

        $fname_in = '';
        for ($i = 1; $i <= $cformsSettings['global']['cforms_formcount']; $i++) {
            $n = ( $i == 1 ) ? '' : $i;
            $fnames[$i] = stripslashes($cformsSettings['form' . $n]['cforms' . $n . '_fname']);
            if ($fname && preg_match('/' . $fname . '/i', $fnames[$i]))
                $fname_in .= "'$n'" . ',';
        }

        if (!empty($fname))
            $where = empty($fname_in) ? " form_id='-1'" : ' form_id IN (' . substr($fname_in, 0, -1) . ')';
        $where .= $from ? ($where ? ' AND' : '') . $wpdb->prepare(" sub_date > '%s'", $from) : '';
        $where .= $to ? ($where ? ' AND' : '') . $wpdb->prepare(" sub_date < '%s'", $to) : '';
        $where = $where ? 'WHERE' . $where : '';

        $in = '';

        $sql = "SELECT *, UNIX_TIMESTAMP(sub_date) as rawdate  FROM {$wpdb->prefix}cformssubmissions $where $ORDER_1 $limit";
        $all = $wpdb->get_results($sql);

        foreach ($all as $d) {
            $in .= $wpdb->prepare("%d,", $d->id);
            $n = ( $d->form_id == '' ) ? 1 : $d->form_id;
            $cfdata[$d->id]['id'] = $d->id;
            $cfdata[$d->id]['form'] = $fnames[$n];
            $cfdata[$d->id]['date'] = $d->sub_date;
            $cfdata[$d->id]['timestamp'] = $d->rawdate;
            $cfdata[$d->id]['email'] = $d->email;
            $cfdata[$d->id]['ip'] = $d->ip;
        }

        if ($in == '')
            return array();

        $sql = "SELECT * FROM {$wpdb->prefix}cformsdata WHERE sub_id IN (" . substr($in, 0, -1) . ")";
        $all = $wpdb->get_results($sql);
        $offsets = array();
        foreach ($all as $d) {

            if (isset($offsets[$d->sub_id][$d->field_name]) && !empty($offsets[$d->sub_id][$d->field_name]))
                $offsets[$d->sub_id][$d->field_name] ++;
            else
                $offsets[$d->sub_id][$d->field_name] = 1;

            $tmp = '';
            if ($offsets[$d->sub_id][$d->field_name] > 1)
                $tmp = '-' . $offsets[$d->sub_id][$d->field_name];

            $cfdata[$d->sub_id]['data'][$d->field_name . $tmp] = $d->field_val;
        }

        if (!empty($cfsort)) {
            $cfdataTMP = $cfdata;
            uksort($cfdata, 'cforms2_compare');
        }
        return $cfdata;

    }

}
