<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014      Bastian Germann
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
$Ajaxpid = '';
$AjaxURL = '';



### debug message handling
function cforms2_dbg($m){
    if ( WP_DEBUG ) trigger_error('DEBUG cforms2: ' . $m);
}



### make time
function cforms2_sec2hms($s){
	$t='';
    $h = intval(intval($s) / 3600);
    $m = intval(($s / 60) % 60);
     if ($h>0)	$t .= " $h ".__('hour(s)', 'cforms').' &';
     if ($m>0)	$t .= " $m ".__('minute(s)', 'cforms');
     return $t;
}



### make time
function cforms2_make_time($t) {
    $dh = explode(' ',$t);
    $d  = explode('/',$dh[0]);
    $h  = explode(':',$dh[1]);
    return mktime((int)$h[0], (int)$h[1], 0, (int)$d[1], (int)$d[0], (int)$d[2]);
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

	return ( ($t1f || $t1 <= time()) && ($t2f || $t2 >= time()) )?true:false;
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

        ### fix labels
	 	if ( in_array($k,array('cauthor','email','url','comment','send2author')) ) continue;

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



### write DB record
function cforms2_write_tracking_record($no,$field_email,$c=''){
		global $wpdb, $track, $cformsSettings;

		cforms2_dbg('WRITING TRACKING RECORD');
		$noTracking = $cformsSettings['form'.$no]['cforms'.$no.'_notracking'];
		$mpSession = ($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] && $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_email']);
		
        if ( $noTracking || $mpSession ){
			cforms2_dbg("....bailing out: noTracking=$noTracking, mpSession=$mpSession\n");
			return -1; ### bail out
		}
		
		if ( $cformsSettings['global']['cforms_database'] == '1' ) {

        	### first process fields, perhaps need to bail out
			$sql='';
			$dosave=false;
            foreach ( $track as $k => $v ){
				if( $c <> '' ){
				  	if( !preg_match('/\$\$\$custom/',$k) )
    	            	continue;
                    else{
						$k = $v;
                        $v = $track[$k];
                    }
                }

                ### clean up keys
                if ( preg_match('/\$\$\$/',$k) ) continue;

	            if ( strpos($k, 'cf_form') !== false && preg_match('/^cf_form\d*_(.+)/',$k, $r) )
	                $k = $r[1];

	            if ( strpos($k, '___') !== false && preg_match('/^(.+)___\d+/',$k, $r) )
	                $k = $r[1];


                $sql .= $wpdb->prepare("('-XXX-',%s,%s),", $k, $v);
               	$dosave=true;
            }
            if( !$dosave ) return;

			### good to go:
			$page = (substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2')?$_POST['cforms_pl'.$no]:cforms2_get_current_page(); // WP comment fix

			$wpdb->query($wpdb->prepare(
				"INSERT INTO $wpdb->cformssubmissions (form_id,email,ip,sub_date) VALUES (%s, %s, %s, %s);",
				$no, $field_email, cforms2_get_ip(), gmdate('Y-m-d H:i:s', current_time('timestamp'))
			));

    		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformssubmissions;");
    		$subID = ($subID->number=='')?'1':$subID->number;

			if( $c <> '' )
				$sql = $wpdb->prepare("INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES (%s,'commentID',%s),(%s,'email',%s),", $subID, $c, $subID, $field_email).$sql;
            else
				$sql = $wpdb->prepare("INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES (%s,'page',%s),", $subID, $page).$sql;

			$wpdb->query( substr(str_replace('-XXX-',esc_sql($subID),$sql) ,0,-1));
		}
		else
			$subID = 'noid';

	return $subID;
}



### move uploaded files to local dir
function cforms2_move_files($trackf, $no, $subID){
	global $cformsSettings,$file;
	
    $temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
    $fileuploaddir = $temp[0];
	
	$inSession = (strpos($subID,'xx') !== false);
	//if( !$inSession )
		$subID_ = ($cformsSettings['form'.$no]['cforms'.$no.'_noid']) ? '' : $subID.'-';

    $file2 = $file;
  	$i=0;
	
	$_SESSION['cforms']['upload'][$no]['doAttach'] = !($cformsSettings['form'.$no]['cforms'.$no.'_noattachments']);

	### debug
	cforms2_dbg("... in session=$inSession, moving files on form $no, tracking ID=$subID_");
	
  	if ( is_array($file2) && isset($file2[tmp_name]) ) {
  		foreach( $file2[tmp_name] as $tmpfile ) {
		
            ### copy attachment to local server dir
            if ( is_uploaded_file($tmpfile) ){

				$fileInfoArr = array('name'=>str_replace(' ','_',$file2['name'][$i]),'path'=>$fileuploaddir, 'subID'=>$subID);
				
				if ( function_exists('my_cforms_logic') ){
					$fileInfoArr = my_cforms_logic( $trackf, $fileInfoArr, 'fileDestination');
				}
				
				if( ! array_key_exists('modified', $fileInfoArr) )
					$fileInfoArr['name'] = $subID_ . $fileInfoArr['name'];				
					
				$destfile = $fileInfoArr['path'].'/'.$fileInfoArr['name'];
				
            	move_uploaded_file($tmpfile,$destfile );

				### debug
				cforms2_dbg("   $tmpfile -> $destfile");
      			
				$file[tmp_name][$i] = $destfile;

				if( $inSession )
					$_SESSION['cforms']['upload'][$no]['files'][] = $destfile;

            }
        	$i++;
		}
	}
}



### base64 encode attached files
function cforms2_base64($fn, $attachFlag=false){
	global $fdata, $fpointer;
	if( file_exists($fn) ){
	    $fdata[$fpointer][name] = $fn;
	    $fdata[$fpointer][doAttach] = $attachFlag;
        $fpointer++;
	}
	return;
}


### replace standard & custom variables in message/subject text
function cforms2_get_current_page(){

	$page = $_SERVER['REQUEST_URI'];

	$page = (trim($page)=='' || strpos($page,'admin-ajax.php')!==false )?$_SERVER['HTTP_REFERER']:trim($page); // for ajax
	return htmlspecialchars($page);

}



### check for post custom fields in string
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
		global $subID, $Ajaxpid, $AjaxURL, $wpdb, $cformsSettings;

	    $eol = ($cformsSettings['global']['cforms_crlf']['b']!=1)?"\r\n":"\n";

		if ( $_POST['comment_post_ID'.$no] )
			$pid = $_POST['comment_post_ID'.$no];
		else if ( $Ajaxpid<>'' )
			$pid = $Ajaxpid;
		else
			$pid = get_the_ID();

		if ( $_POST['cforms_pl'.$no] )
			$permalink = $_POST['cforms_pl'.$no];
		else if ( $Ajaxpid<>'' )
			$permalink = $AjaxURL;
		else
			$permalink = get_permalink($pid);

		###
		### if the "month" is not spelled correctly, try the commented out line instead of the one after
		###
		### $date = utf8_encode(html_entity_decode( mysql2date(get_option('date_format'), current_time('mysql')) ));
		$date = mysql2date(get_option('date_format'), current_time('mysql'));

		$time = gmdate(get_option('time_format'), current_time('timestamp'));
		$page = cforms2_get_current_page();

		if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2' ) // WP comment fix
			$page = $permalink;

		$find = $wpdb->get_row($wpdb->prepare("SELECT p.post_title, p.post_excerpt, u.display_name FROM $wpdb->posts AS p LEFT JOIN ($wpdb->users AS u) ON p.post_author = u.ID WHERE p.ID=%s", $pid));

		$CurrUser = wp_get_current_user();

		$m  = str_replace( '{Referer}',		$_SERVER['HTTP_REFERER'], $m );
		$m  = str_replace( '{PostID}',		$pid, $m );
		$m 	= str_replace( '{Form Name}',	$cformsSettings['form'.$no]['cforms'.$no.'_fname'], $m );
		$m 	= str_replace( '{Page}',		$page, $m );
		$m 	= str_replace( '{Date}',		$date, $m );
		$m 	= str_replace( '{Author}',		$find->display_name, $m );
		$m 	= str_replace( '{Time}',		$time, $m );
		$m 	= str_replace( '{IP}',			cforms2_get_ip(), $m );
		$m 	= str_replace( '{BLOGNAME}',	get_option('blogname'), $m );

		$m 	= str_replace( '{CurUserID}',	$CurrUser->ID, $m );
		$m 	= str_replace( '{CurUserName}',	$CurrUser->display_name, $m );
		$m 	= str_replace( '{CurUserEmail}',$CurrUser->user_email, $m );
		$m 	= str_replace( '{CurUserFirstName}', $CurrUser->user_firstname, $m );
		$m 	= str_replace( '{CurUserLastName}',	$CurrUser->user_lastname, $m );

		$m 	= str_replace( '{Permalink}',	$permalink, $m );
		$m 	= str_replace( '{Title}',		$find->post_title, $m );
		$m 	= str_replace( '{Excerpt}',		$find->post_excerpt, $m );

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



class cforms2_rss {
	static function vars($public_query_vars) {
        $public_query_vars[] = 'cformsRSS';
        return $public_query_vars;
    }

	static function outputRSS() {
		global $wpdb, $cformsSettings;
		$temp=get_query_var('cformsRSS');
		if( $temp <> '' ) {

			$cformsRSS = explode('$#$', $temp);
            $no = $cformsRSS[0];
            $key = $cformsRSS[1];

			$all = $no=='-1' && $cformsSettings['global']['cforms_rssall'] && $cformsSettings['global']['cforms_rsskeyall'] == $key;
			$single = $no<>'-1' && $cformsSettings['form'.$no]['cforms'.$no.'_rss'] && $key<>'' && $cformsSettings['form'.$no]['cforms'.$no.'_rsskey'] == $key;
			if( $all || $single ){

				### add opt. form content

				$WHERE='';
				if( $all )
					$rsscount = ($cformsSettings['global']['cforms_rssall_count']>0)?$cformsSettings['global']['cforms_rssall_count']:5;
				else if( $single ){
					$WHERE = "WHERE form_id = '".$no."'";
					$rsscount = ($cformsSettings['form'.$no]['cforms'.$no.'_rss_count']>0)?$cformsSettings['form'.$no]['cforms'.$no.'_rss_count']:5;
				}
				$entries = $wpdb->get_results("SELECT * FROM {$wpdb->cformssubmissions} $WHERE ORDER BY sub_date DESC LIMIT 0,".$rsscount); //TODO check SQL injection

				$content = '';
                if( count($entries)>0 ){
					foreach($entries as $entry){

							$ff = $cformsSettings['form'.$entry->form_id];
							$findex = 'cforms'.$entry->form_id.'_rss_fields';
							$f = isset($ff[$findex]) ? $ff[$findex] : null;
	                        $date = mysql2date(get_option('date_format'), $entry->sub_date);
	                        $time = mysql2date(get_option('time_format'), $entry->sub_date);
							$title = '['.$entry->id.'] '.$entry->email;

                            $description = '<![CDATA[ <div style="margin:8px 0;"><span style="font-size:150%; color:#aaa;font-weight:bold;">#'.$entry->id.'</span> '. "$date&nbsp;<strong>$time</strong>" .( $single?'':' &nbsp;<strong>"'.$cformsSettings['form'.$entry->form_id]['cforms'.$entry->form_id.'_fname'].'"</strong>:' ).'</div>';
							$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->cformsdata} WHERE sub_id=%s", $entry->id));
                            if( is_array($f) && array_count_values($f)>0 ){
								foreach( $data as $e ){
									if( array_search($e->field_name,$f)!==false )
                                    	$description .= '<div style="width:100%; clear:left;"><div style="background:#F8FAFC;width:49%; float:left; text-align:right;margin-right:1%;">'.$e->field_name.':</div><div style="width:50%; float:left;">'.$e->field_val.'</div></div>';
                                }
							}

			                $entrylink = network_site_url().'/wp-admin/admin.php?page='.plugin_dir_path(plugin_basename(__FILE__)).'cforms-database.php&amp;d-id='.$entry->id.'#entry'.$entry->id;
							$description .= '<div style="margin:8px 0;"><a href="'.$entrylink.'">'.__('View details','cforms').'</a></div> ]]>';
							$content.= "\t".'<item>'."\n".
										"\t\t".'<title>'.$title.'</title>'."\n".
										"\t\t".'<description>'.$description.'</description>'."\n".
										"\t\t".'<link>'.$entrylink.'</link>'."\n".
										"\t\t".'<guid isPermaLink="false">'.$entrylink.'</guid>'."\n".
										"\t\t".'<pubDate>'.mysql2date('D, d M Y H:i:s +0000', $entry->sub_date, false).'</pubDate>'."\n".
										"\t".'</item>'."\n";
					}
				}
				else
					$content = '<item><title>'.__('No entries yet','cforms').'</title><description>'.__('You might want to check back in a little while...','cforms').'</description>'.
								'<link></link><guid isPermaLink="false"></guid><pubDate>'.gmdate('D, d M Y H:i:s +0000', current_time('timestamp')).'</pubDate></item>';

	header( 'Content-Type: text/xml; charset='.get_option('blog_charset') );

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
	<title><?php if ($single) echo __('New submissions for >>', 'cforms').' '.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']); else _e('All new form submissions', 'cforms'); ?></title>
	<atom:link href="<?php echo network_site_url().'?cformsRSS='.$no.urlencode('$#$').$cformsSettings['form'.$no]['cforms'.$no.'_rsskey']; ?>" rel="self" type="application/rss+xml" />
	<link><?php echo network_site_url(); ?></link>
	<description><?php _e('This RSS feed provides you with the most recent form submissions.', 'cforms') ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
	<language><?php echo get_option('rss_language'); ?></language>
<?php echo $content; ?>
</channel>
</rss>
<?php
				die();
			}
		}
	}
}
add_filter('query_vars', array('cforms2_rss', 'vars'));
add_action('template_redirect', array('cforms2_rss', 'outputRSS'));


###
###
### API functions
###
###


### API function #1 : get tracked entries
global $cfdata, $cfsort, $cfsortdir;

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
	$all = $wpdb->get_results($sql); //TODO check SQL injection
	
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

		if( $offsets[$d->sub_id][$d->field_name]<>'')
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



### API functions #2 : get tracked entries
if (!function_exists('cf_extra_comment_data')) {
	function cf_extra_comment_data( $id ) {
		global $wpdb;
		$all = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->cformsdata} WHERE sub_id = (SELECT sub_id FROM {$wpdb->cformsdata} WHERE field_name='commentID' AND field_val=%s)", $id));
		foreach( $all as $a ) {
			$r[$a->field_name]=$a->field_val;
        }
		return $r;
	}
}
