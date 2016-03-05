<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2016 Bastian Germann
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

###
### other global init stuff
###
$track = array();



### debug message handling
if (!defined('WP_DEBUG_CFORMS2')) {
	define('WP_DEBUG_CFORMS2', false);
}
function cforms2_dbg($m){
    if ( WP_DEBUG_CFORMS2 ) trigger_error('DEBUG cforms2: ' . $m);
}



### make time
function cforms2_make_time($t) {
	$time = str_replace('/', '.', $t) . sprintf(' %+d', get_option('gmt_offset'));
	$time = strtotime($time);
	if ($time === false)
		return 0;
	return $time;
}



### check time constraints
function cforms2_check_time($no) {
	global $cformsSettings;

    $t1f = $t2f = false;

    if( strlen($cformsSettings['form'.$no]['cforms'.$no.'_startdate']) > 1 )
        $t1 = cforms2_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_startdate']));
	else
    	$t1f=true;

    if( strlen($cformsSettings['form'.$no]['cforms'.$no.'_enddate']) > 1 )
        $t2 = cforms2_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_enddate']));
	 else
     	$t2f=true;

	return ($t1f || $t1 <= current_time('timestamp')) && ($t2f || $t2 >= current_time('timestamp'));
}



### sanitize label ID's
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


### map data
function cforms2_all_tracks($session){
	$t = array();

	### clean up underscores
    foreach( array_keys($session) as $key){
		if ( strpos($key,'cf_')===false ) continue;
		foreach( $session[$key] as $k => $v ) {
            $t[$key.'_'.$k] = $v;
        }
	}

	return $t;
}



function cforms2_format_email($track,$no){
	global $cformsSettings;
	$customspace = (int)($cformsSettings['form'.$no]['cforms'.$no.'_space']>0) ? $cformsSettings['form'.$no]['cforms'.$no.'_space'] : 30;

	$t = $h = '';

    $eol = ($cformsSettings['global']['cforms_crlf']['b']!=1)?"\r\n":"\n";

    foreach( array_keys($track) as $k){

		$v = stripslashes($track[$k]);
		
		### Exclude empty fields?
		if ($v == '' && $cformsSettings['form'.$no]['cforms'.$no.'_emptyoff'])
			continue;

		if ( preg_match('/\$\$\$/',$k) ) continue;

		if ( strpos($k, 'cf_form') !== false && preg_match('/^cf_form\d*_(.+)/',$k, $r) )
        	$k = $r[1];

		if ( strpos($k, '___') !== false && preg_match('/^(.+)___\d+/',$k, $r) )
        	$k = $r[1];

		###  fieldsets
	    if ( strpos($k,'FieldsetEnd')!==false ){
   			$t .= $eol;
            $h .= '<tr><td style="" colspan="2">&nbsp;</td></tr>' . $eol;
			continue;
		}
	    elseif ( strpos($k,'Fieldset')!==false ){
	        $space='-';
	        $n = ((($customspace*2)+2) - strlen(strip_tags($v))) / 2;
	        $n = ($n<0)?0:$n;
	        if ( strlen(strip_tags($v)) < (($customspace*2)-2) )
	            $space = str_repeat("-", $n );

   			$t .= substr($eol."$space".stripslashes( strip_tags($v) )."$space",0,($customspace*2)) . $eol . $eol;
            $h .= '<tr><td '.$cformsSettings['global']['cforms_style']['fs'].' colspan="2">' . $v . '</td></tr>' . $eol;
			continue;
		}

		###  Upload fields?
		if ( strpos($k,'[*')!==false ) {
			$k = substr($k,0,strpos($k,'[*'));
        }

		### HTML = TEXT (key, value)
		$hk = $k;
		$hv = htmlspecialchars($v);

		### checkboxes get a symbol for value
	 	if ( $v == '(x)' ) 
			$hv = "<strong>&#10004;</strong>";
			
		###  CRs for textareas \r\n user input hardcoded!
		if ( strpos($v,"\n")!==false ) {
	        $k = $eol . $k;
	        $hv = str_replace("\r\n","\n",$v);
	        $hv = str_replace("\n",'<br />'.$eol,$hv);
	        $v = $eol . $v . $eol;
		}

        ###  TEXT: spacing
        $space='';
        if ( strlen(stripslashes($k)) < $customspace )   ###  don't count ->\"  sometimes adds more spaces?!?
              $space = str_repeat(" ",$customspace-strlen(stripslashes($k)));

        ###  create formdata block for email
        $t .= stripslashes( strip_tags($k) ). ': '. $space . $v . $eol;
        $h .= '<tr><td '.$cformsSettings['global']['cforms_style']['key_td'].'>' . $hk . '</td><td '.$cformsSettings['global']['cforms_style']['val_td'].'>' . $hv . '</td></tr>' . $eol;

	}
	$r['text'] = $t;
    $r['html'] = '<div '.$cformsSettings['global']['cforms_style']['admin'].'><span '.$cformsSettings['global']['cforms_style']['title'].'>'.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']).'</span><table cellpadding="0" cellspacing="0" '.$cformsSettings['global']['cforms_style']['table'].'>'.stripslashes($h).'</table></div>';
	return $r;
}



### replace standard & custom variables in message/subject text
function cforms2_get_current_page(){

	$page = $_SERVER['REQUEST_URI'];

	$page = (trim($page)=='' || strpos($page,'admin-ajax.php')!==false )?$_SERVER['HTTP_REFERER']:trim($page); // for ajax
	return htmlspecialchars($page);

}



### check for post custom fields in string
### TODO what is call to get_post_custom for?
function cforms2_check_post_vars($fv){
    preg_match_all('/\\{([^\\{]+)\\}/',$fv,$fall);

    if ( count($fall[1]) > 0 ) {

    	$custArr = get_post_custom( get_the_ID() );
        foreach ( $fall[1] as $fvar ) {
            if( $custArr[$fvar][0] <> '')
                $fv = str_replace('{'.$fvar.'}', $custArr[$fvar][0], $fv);
        }

    }
	return $fv;
}



### look for default/system variables
function cforms2_check_default_vars($m,$no) {
	global $subID, $cformsSettings;

	$eol = ($cformsSettings['global']['cforms_crlf']['b']!=1)?"\r\n":"\n";

	if ( isset($_POST['comment_post_ID'.$no] ) && $_POST['comment_post_ID'.$no] )
		$pid = $_POST['comment_post_ID'.$no];
	else
		$pid = get_the_ID();

	if ( isset($_POST['cforms_pl'.$no] ) && $_POST['cforms_pl'.$no] )
		$permalink = $_POST['cforms_pl'.$no];
	else
		$permalink = get_permalink($pid);

	$date = current_time(get_option('date_format'));

	$time = current_time(get_option('time_format'));
	$page = cforms2_get_current_page();

	$find = get_post($pid);
	if (!empty($find)) {
		$user = get_user_by('id', $find->post_author);
		$user_name = $user->display_name;
		$post_title = $find->post_title;
		$post_excerpt = $find->post_excerpt;
	} else {
		$user_name = $post_title = $post_excerpt = '';
	}

	$CurrUser = wp_get_current_user();

	if (isset($_SERVER['HTTP_REFERER'])) 
		$m  = str_replace( '{Referer}',	$_SERVER['HTTP_REFERER'], $m );
	$m  = str_replace( '{PostID}',		$pid, $m );
	$m 	= str_replace( '{Form Name}',	$cformsSettings['form'.$no]['cforms'.$no.'_fname'], $m );
	$m 	= str_replace( '{Page}',		$page, $m );
	$m 	= str_replace( '{Date}',		$date, $m );
	$m 	= str_replace( '{Author}',		$user_name, $m );
	$m 	= str_replace( '{Time}',		$time, $m );
	$m 	= str_replace( '{IP}',			cforms2_get_ip(), $m );
	$m 	= str_replace( '{BLOGNAME}',	get_option('blogname'), $m );

	$m 	= str_replace( '{CurUserID}',	$CurrUser->ID, $m );
	$m 	= str_replace( '{CurUserName}',	$CurrUser->display_name, $m );
	$m 	= str_replace( '{CurUserEmail}',$CurrUser->user_email, $m );
	$m 	= str_replace( '{CurUserFirstName}', $CurrUser->user_firstname, $m );
	$m 	= str_replace( '{CurUserLastName}',	$CurrUser->user_lastname, $m );

	$m 	= str_replace( '{Permalink}',	$permalink, $m );
	$m 	= str_replace( '{Title}',		$post_title, $m );
	$m 	= str_replace( '{Excerpt}',		$post_excerpt, $m );

	$m 	= preg_replace( "/\r\n\./", "\n", $m );

	### normalize
	$m 	= str_replace( "\r\n", "\n", $m );
	$m 	= str_replace( "\r", "\n", $m );
	$m 	= str_replace( "\n", $eol, $m );

	if  ( $cformsSettings['global']['cforms_database'] && $subID<>'' )
		$m 	= str_replace( '{ID}', $subID, $m );

	return $m;
}



### look for custom variables
function cforms2_check_cust_vars($m,$t,$html=false) {
	
	global $cformsSettings;
    $eol = ($cformsSettings['global']['cforms_crlf']['b']!=1)?"\r\n":"\n";

	preg_match_all('/\\{([^\\{]+)\\}/',$m,$findall);
	if ( count($findall[1]) > 0 ) {
		$allvars = array_keys($t);

		foreach ( $findall[1] as $fvar ) {

			$fTrackedVar = addslashes($fvar);

			### convert _fieldXYZ to actual label name tracked...
			if ( strpos($fvar,'_field')!==false ){
				$fNo = substr($fvar,6);
				if ( $allvars[$fNo]<>'' )
					$fTrackedVar = $t['$$$'.$fNo];  ### reset $fvar to actual label name and continue
			}

			### convert if alt [id:] used
			if ( in_array( '$$$'.$fTrackedVar, $allvars ) ){
				if ( $t['$$$'.$fTrackedVar]<>'' )
					$fTrackedVar = $t['$$$'.$fTrackedVar];  ### reset $fvar to actual label name and continue
			}

			### check if label name is tracked...
			if( in_array( $fTrackedVar,$allvars ) ){
				
				$v = stripslashes($t[$fTrackedVar]);
				
				###  CRs for textareas \r\n user input hardcoded!
				if ( $html && strpos($v,"\n")!==false )
					$v = str_replace("\n",'<br />'.$eol,$v);
				
				$m = str_replace('{'.$fvar.'}', $v, $m);
			}
			
		}
	}
	return $m;
}



function cforms2_get_ip() {
	if (isset($_SERVER)) {
 		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
 		elseif (isset($_SERVER["HTTP_CLIENT_IP"]))	$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
 		else										$ip_addr = $_SERVER["REMOTE_ADDR"];
	} else {
 		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) 	$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
 		elseif ( getenv( 'HTTP_CLIENT_IP' ) )	  	$ip_addr = getenv( 'HTTP_CLIENT_IP' );
 		else										$ip_addr = getenv( 'REMOTE_ADDR' );
	}
	return $ip_addr;
}


function cforms2_compare( $a,$b ){
	global $cfdataTMP, $cfsort, $cfsortdir;

	if (!is_array($a) && !is_array($b)){
		
		$na = ($cfdataTMP[$a]['data'][$cfsort]<>'') ? $cfdataTMP[$a]['data'][$cfsort]:false;
		$nb = ($cfdataTMP[$b]['data'][$cfsort]<>'') ? $cfdataTMP[$b]['data'][$cfsort]:false;
	
		if ( !($na && $nb) ) {
			if ( !$na ) return 1;
			if ( !$nb ) return -1;
			return 0;
		}
	}

    $tmpA=(int)trim($na);
    $tmpB=(int)trim($nb);
    if ( is_numeric($na) && is_numeric($nb) ){
	    if ( stristr($cfsortdir,'asc')===false ){
	        return ($tmpB > $tmpA)?-1:1;
	    } else {
	        return ($tmpA < $tmpB)?-1:1;
		}
    } else {
	    if ( stristr($cfsortdir,'asc')===false ){
	        return strcasecmp($nb, $na);
	    }else{
	        return strcasecmp($na, $nb);
    	}
	}
}


### API function: get_cforms_entries
if (!function_exists('get_cforms_entries')) {
function get_cforms_entries($fname=false,$from=false,$to=false,$s=false,$limit=false,$sd='asc') {
	global $wpdb, $cformsSettings, $cfdataTMP, $cfsort, $cfsortdir;
	
	//unify
	if( $s=='date' || $s=='timestamp' )
		$s = 'sub_date';

	//set limit
    $limit = ($limit && $limit<>'')?'LIMIT 0,'.$limit:'';
	
	
	$ORDER_1 = $cfsort = '';
	if( in_array($s,array('id','form','timestamp','email','ip')) )
		$ORDER_1 = "ORDER BY " . $s . ' ' . $sd;
	else{
		$ORDER_1 = "ORDER BY id DESC";
		$cfsort = $s;
	}

	//SORT
	$cfdata 	= array();
    $cfsortdir	= $sd;
	
	//GENERAL WHERE
	$where = false;

    $fname_in = '';
	for ($i=1; $i <= $cformsSettings['global']['cforms_formcount']; $i++){
	    $n = ( $i==1 )?'':$i;
	    $fnames[$i]=stripslashes($cformsSettings['form'.$n]['cforms'.$n.'_fname']);
		if ( $fname && preg_match('/'.$fname.'/i',$fnames[$i]) )
        	$fname_in .= "'$n'".',';
	}

    if ( $fname<>'' )
		$where = ($fname_in<>'')?' form_id IN ('.substr($fname_in,0,-1).')':" form_id='-1'";
	$where .= $from?($where?' AND':'')." sub_date > '$from'":'';
	$where .= $to?($where?' AND':'')." sub_date < '$to'":'';
    $where = $where?'WHERE'.$where:'';
	
    $in = '';

    $sql = "SELECT *, UNIX_TIMESTAMP(sub_date) as rawdate  FROM {$wpdb->cformssubmissions} $where $ORDER_1 $limit";
	$all = $wpdb->get_results($sql);
	
	foreach ( $all as $d ){
    	$in .= $wpdb->prepare("%d,", $d->id);
	    $n = ( $d->form_id=='' )?1:$d->form_id;
    	$cfdata[$d->id]['id'] = $d->id;
    	$cfdata[$d->id]['form'] = $fnames[$n];
    	$cfdata[$d->id]['date'] = $d->sub_date;
    	$cfdata[$d->id]['timestamp'] = $d->rawdate;
    	$cfdata[$d->id]['email'] = $d->email;
    	$cfdata[$d->id]['ip'] = $d->ip;
	}

    if ( $in=='' )
    	return false;

    $sql = "SELECT * FROM {$wpdb->cformsdata} WHERE sub_id IN (".substr($in,0,-1).")";
	$all = $wpdb->get_results($sql);
	$offsets = array();
	foreach ( $all as $d ){

		if( isset($offsets[$d->sub_id][$d->field_name]) && $offsets[$d->sub_id][$d->field_name]<>'')
	    	$offsets[$d->sub_id][$d->field_name]++;
        else
			$offsets[$d->sub_id][$d->field_name]=1;

        $tmp = '';
		if( $offsets[$d->sub_id][$d->field_name]>1)
        	$tmp = '-'.$offsets[$d->sub_id][$d->field_name];

        $cfdata[$d->sub_id]['data'][$d->field_name.$tmp] = $d->field_val;

    }

	if ( $cfsort <> '' ){
		$cfdataTMP = $cfdata;
		uksort ($cfdata, 'cforms2_compare');
	}
	return $cfdata;
}
}
