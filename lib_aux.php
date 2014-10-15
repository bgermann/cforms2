<?php

### other global stuff
$track = array();
$Ajaxpid = '';
$AjaxURL = '';

### SMPT sever configured?
if ( $cformsSettings['global']['cforms_smtp']<>'' )
	$smtpsettings=explode('$#$',$cformsSettings['global']['cforms_smtp']);

if ( $smtpsettings[0]=='1' ) {
	if ( file_exists(dirname(__FILE__) . '/phpmailer/class.phpmailer.php') )
		require_once(dirname(__FILE__) . '/phpmailer/cforms_phpmailer.php');
	else
		$smtpsettings[0]=='0';
}

### make time
function sec2hms($s){
	$t='';
    $h = intval(intval($s) / 3600);
    $m = intval(($s / 60) % 60);
    //$s = intval($s % 60);
     if ($h>0)	$t .= " $h ".__('hour(s)', 'cforms').' &';
     if ($m>0)	$t .= " $m ".__('minute(s)', 'cforms');
     return $t;
}

### make time
function cf_make_time($t) {
    $dh = explode(' ',$t);
    $d  = explode('/',$dh[0]);
    $h  = explode(':',$dh[1]);
    return mktime($h[0], $h[1], '0', $d[1], $d[0], $d[2]);
}

### check time constraints
function cf_check_time($no) {
	global $cformsSettings;

    $t1f = $t2f = false;

    if( strlen($cformsSettings['form'.$no]['cforms'.$no.'_startdate']) > 1 )
        $t1 = cf_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_startdate']));
	else
    	$t1f=true;

    if( strlen($cformsSettings['form'.$no]['cforms'.$no.'_enddate']) > 1 )
        $t2 = cf_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_enddate']));
	 else
     	$t2f=true;

	return ( ($t1f || $t1 <= time()) && ($t2f || $t2 >= time()) )?true:false;
}

### prep captcha get call
function get_captcha_uri() {
	global $cformsSettings;
	$cap = $cformsSettings['global']['cforms_captcha_def'];
	$c1 = prep( $cap['c1'],'3' );
	$c2 = prep( $cap['c2'],'5' );
	$ac = prep( urlencode($cap['ac']),urlencode('abcdefghijkmnpqrstuvwxyz23456789') );
	$i = prep( $cap['i'],'' );
	$h = prep( $cap['h'],25 );
	$w = prep( $cap['w'],115 );
	$c = prep( $cap['c'],'000066' );
	$l = prep( $cap['l'],'000066' );
	$f = prep( $cap['f'],'font4.ttf' );
	$a1 = prep( $cap['a1'],-12 );
	$a2 = prep( $cap['a2'],12 );
	$f1 = prep( $cap['f1'],17 );
	$f2 = prep( $cap['f2'],19 );
	$bg = prep( $cap['bg'],'1.gif');
	return "&amp;c1={$c1}&amp;c2={$c2}&amp;ac={$ac}&amp;i={$i}&amp;w={$w}&amp;h={$h}&amp;c={$c}&amp;l={$l}&amp;f={$f}&amp;a1={$a1}&amp;a2={$a2}&amp;f1={$f1}&amp;f2={$f2}&amp;b={$bg}";
}


### sanitize label ID's
function cf_sanitize_ids($title) {

	$title = strip_tags($title);
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	$title = str_replace('%', '', $title);
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	$title = remove_accents($title);
	if (seems_utf8($title)) {
		$title = utf8_uri_encode($title, 200);
	}

	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');

	return $title;
}

### strip stuff
function prep($v,$d) {
	return ($v<>'') ? stripslashes(htmlspecialchars($v)) : $d;
}

### Special Character Suppoer in subject lines
function encode_header ($str) {
	$x = preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);

	if ($x == 0)
		return ($str);

	$maxlen = 75 - 7 - strlen( get_option('blog_charset') );

	$encoded = base64_encode($str);
	$maxlen -= $maxlen % 4;
	$encoded = trim(chunk_split($encoded, $maxlen, "\n"));

	$encoded = preg_replace('/^(.*)$/m', " =?".get_option('blog_charset')."?B?\\1?=", $encoded);
	$encoded = trim($encoded);

	return $encoded;
}

### formatEmail data
function allTracks($session){
	$t = array(); $i = array();

	### clean up underscores
    foreach( array_keys($session) as $key){
		if ( strpos($key,'cf_')===false ) continue;
		foreach( $session[$key] as $k => $v )
            $t[$key.'_'.$k] = $v;
	}

	return $t;
}

### formatEmail data
function formatEmail($track,$no){
	global $cformsSettings;
	$customspace = (int)($cformsSettings['form'.$no]['cforms'.$no.'_space']>0) ? $cformsSettings['form'.$no]['cforms'.$no.'_space'] : 30;

	$t = $h = '';
    foreach( array_keys($track) as $k){

		$v = $track[$k];

        ### fix labels
		if ( preg_match('/\$\$\$/',$k) ) continue;
		if ( preg_match('/(cf_form\d*_){0,1}([^(___)]*)(___\d+){0,1}/',$k, $r) ) $k = $r[2];

		###  fieldsets
	    if ( strpos($k,'FieldsetEnd')!==false ){
   			$t .= "\n";
            $h .= '<tr><td style=3D"'.$cformsSettings['global']['cforms_style_fsend_td'].'" colspan=3D"2">&nbsp;</td></tr>' . "\n";
			continue;
		}
	    elseif ( strpos($k,'Fieldset')!==false ){
	        $space='-';
	        $n = ((($customspace*2)+2) - strlen($v)) / 2;
	        $n = ($n<0)?0:$n;
	        if ( strlen($v) < (($customspace*2)-2) )
	            $space = str_repeat("-", $n );

   			$t .= substr("\n$space".stripslashes($v)."$space",0,($customspace*2)) . "\n\n";
            $h .= '<tr><td style=3D"'.$cformsSettings['global']['cforms_style_fs_td'].'" colspan=3D"2">' . $v . '</td></tr>' . "\n";
			continue;
		}

		### HTML = TEXT (key, value)
		$hk = $k;
		$hv = str_replace("=","=3D",$v);

		###  CRs for textareas
		if ( strpos($v,"\n")!==false ) {
	        $k = "\n" . $k;
	        $hv = str_replace(array("=","\n"),array("=3D","<br />\n"),$v);
	        $v = "\n" . $v . "\n";
		}

        ###  TEXT: spacing
        $space='';
        if ( strlen(stripslashes($k)) < $customspace )   ###  don't count ->\"  sometimes adds more spaces?!?
              $space = str_repeat(" ",$customspace-strlen(stripslashes($k)));

        ###  create formdata block for email
        $t .= stripslashes( $k ). ': '. $space . $v . "\n";
        $h .= '<tr><td style=3D"'.$cformsSettings['global']['cforms_style_key_td'].'">' . $hk . '</td><td style=3D"'.$cformsSettings['global']['cforms_style_val_td'].'">' . $hv . '</td></tr>' . "\n";

	}
	$r['text'] = $t;
    $r['html'] = '<p style="'.$cformsSettings['global']['cforms_style_title'].'">'.$cformsSettings['form'.$no]['cforms'.$no.'_fname'].'</p><table width=3D"100%" cellpadding=3D"0" cellspacing=3D"0" style="'.$cformsSettings['global']['cforms_style_table'].'">'.stripslashes($h).'</table><span style=3D"'.$cformsSettings['global']['cforms_style_cforms'].'">powered by <a href=3D"http://www.deliciousdays.com/cforms-plugin">cformsII</a></span>';
	return $r;
}

### write DB record
function write_tracking_record($no,$field_email){
		global $wpdb, $track, $cformsSettings;

        if ( $cformsSettings['form'.$no]['cforms'.$no.'_notracking'] || ($cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_form'] && $cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_email']) )
        	return;  ### bail out

		if ( $cformsSettings['global']['cforms_database'] == '1' ) {

			$page = (substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2')?$_POST['cforms_pl'.$no]:get_current_page(); // WP comment fix

			$wpdb->query("INSERT INTO $wpdb->cformssubmissions (form_id,email,ip,sub_date) VALUES ".
						 "('" . $no . "', '" . $field_email . "', '" . cf_getip() . "', '".gmdate('Y-m-d H:i:s', current_time('timestamp'))."');");

    		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformssubmissions;");
    		$subID = ($subID->number=='')?'1':$subID->number;

			$sql = "INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES " .
						 "('$subID','page','$page'),";

            foreach ( $track as $k => $v ){
                    ### clean up keys
					if ( preg_match('/\$\$\$/',$k) ) continue;
	                if ( preg_match('/(cf_form\d*_){0,1}([^(___)]*)(___\d+){0,1}/',$k, $r) ) $k = $r[2];
                    $sql .= "('$subID','".addslashes($k)."','".addslashes($v)."'),";
            }

			$wpdb->query(substr($sql,0,-1));
		}
		else
			$subID = 'noid';

	return $subID;
}

### move uploaded files to local dir
function cf_move_files($no, $subID, $file){
	global $cformsSettings;
    $temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
    $fileuploaddir = $temp[0];

	$filefield=0;
	if ( is_array($file) && isset($file[tmp_name]) ) {
		foreach( $file[tmp_name] as $tmpfile ) {
            ### copy attachment to local server dir

            if ( is_uploaded_file($tmpfile) ){
            	$destfile = $fileuploaddir.'/'.$subID.'-'.str_replace(' ','_',$file['name'][$filefield]);
            	move_uploaded_file($tmpfile,$destfile );
	            if($subID=='xx')
	                $_SESSION['cforms']['upload'][$no][] = $destfile;
            }
        	$filefield++;
		}
	}
}

### replace standard & custom variables in message/subject text
function get_current_page($clean=false){
	global $Ajaxpid;

	$page = $_SERVER['REQUEST_URI'];

	if ($clean && strpos($page,'?')>0)
	        $page = substr( $page, 0, strpos($page,'?'));

	$page = (trim($page)=='' || strpos($page,'lib_ajax.php')!==false )?$_SERVER['HTTP_REFERER']:trim($page); // for ajax
	return $page;

}

### look for default/system variables
function check_default_vars($m,$no) {
		global $subID, $Ajaxpid, $AjaxURL, $post, $wpdb, $wp_db_version, $cformsSettings;

		if ( $_POST['comment_post_ID'.$no] )
			$pid = $_POST['comment_post_ID'.$no];
		else if ( $Ajaxpid<>'' )
			$pid = $Ajaxpid;
		else if ( function_exists('get_the_ID') )
			$pid = get_the_ID();

		if ( $_POST['cforms_pl'.$no] )
			$permalink = $_POST['cforms_pl'.$no];
		else if ( $Ajaxpid<>'' )
			$permalink = $AjaxURL;
		else if ( function_exists('get_permalink') && function_exists('get_userdata') )
			$permalink = get_permalink($pid);

		###
		### if the "month" is not spelled correctly, try the commented out line instead of the one after
		###
		### $date = utf8_encode(html_entity_decode( mysql2date(get_option('date_format'), current_time('mysql')) ));
		$date = mysql2date(get_option('date_format'), current_time('mysql'));

		$time = gmdate(get_option('time_format'), current_time('timestamp'));
		$page = get_current_page();

		if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2' ) // WP comment fix
			$page = $permalink;

		$find = $wpdb->get_row("SELECT p.post_title, p.post_excerpt, u.display_name FROM $wpdb->posts AS p LEFT JOIN ($wpdb->users AS u) ON p.post_author = u.ID WHERE p.ID='$pid'");

		$CurrUser = wp_get_current_user();

		$m  = str_replace( '{Referer}',		$_SERVER['HTTP_REFERER'], $m );
		$m  = str_replace( '{PostID}',		$pid, $m );
		$m 	= str_replace( '{Form Name}',	$cformsSettings['form'.$no]['cforms'.$no.'_fname'], $m );
		$m 	= str_replace( '{Page}',		$page, $m );
		$m 	= str_replace( '{Date}',		$date, $m );
		$m 	= str_replace( '{Author}',		$find->display_name, $m );
		$m 	= str_replace( '{Time}',		$time, $m );
		$m 	= str_replace( '{IP}',			cf_getip(), $m );
		$m 	= str_replace( '{BLOGNAME}',	get_option('blogname'), $m );

		$m 	= str_replace( '{CurUserID}',	$CurrUser->ID, $m );
		$m 	= str_replace( '{CurUserName}',	$CurrUser->display_name, $m );
		$m 	= str_replace( '{CurUserEmail}',$CurrUser->user_email, $m );

		$m 	= str_replace( '{Permalink}',	$permalink, $m );
		$m 	= str_replace( '{Title}',		$find->post_title, $m );
		$m 	= str_replace( '{Excerpt}',		$find->post_excerpt, $m );

		$m 	= preg_replace( "/\r\n\./", "\r\n", $m );

		if  ( $cformsSettings['global']['cforms_database'] && $subID<>'' )
			$m 	= str_replace( '{ID}', $subID, $m );

		return $m;
}

### look for custom variables
function check_cust_vars($m,$t,$no) {

	preg_match_all('/\\{([^\\{]+)\\}/',$m,$findall);

	if ( count($findall[1]) > 0 ) {
		$allvars = array_keys($t);

		foreach ( $findall[1] as $fvar ) {

			$fTrackedVar = $fvar;

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
			if( in_array( $fTrackedVar,$allvars ) )
				$m = str_replace('{'.$fvar.'}', $t[$fTrackedVar], $m);

		}
	}
	return $m;
}

### Can't use WP's function here, so lets use our own
if ( !function_exists('cf_getip') ) :
function cf_getip() {
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
endif;

### API call to get all entries
function get_cforms_entries($fname=false,$from=false,$to=false,$sort=false,$limit=false) {
	global $wpdb, $cformsSettings;
	$data = array();

    $fname_in = '';
	$where = false;

	for ($i=1; $i <= $cformsSettings['global']['cforms_formcount']; $i++){
	    $n = ( $i==1 )?'':$i;
	    $fnames[$i]=stripslashes($cformsSettings['form'.$n]['cforms'.$n.'_fname']);
		if ( $fname && preg_match('/'.$fname.'/i',$fnames[$i]) )
        	$fname_in = "'$n'" . $fname_in . ',';
	}

	$where = ($fname<>'' && $fname_in<>'')?' form_id IN ('.substr($fname_in,0,-1).')':'';
	$where .= $from?($where?' AND':'')." sub_date > '$from'":'';
	$where .= $to?($where?' AND':'')." sub_date < '$to'":'';
    $where = $where?'WHERE'.$where:'';

	$sort = $sort?'ORDER BY '.$sort:'ORDER BY id';

    $limit = ($limit && $limit<>'')?'LIMIT 0,'.$limit:'';
	//$where = ($fname<>''||$from<>''||$to<>'')?'':' WHERE 0,'.$limit;

    $in = '';
    $sql = "SELECT * FROM {$wpdb->cformssubmissions} $where $sort $limit";
	$all = $wpdb->get_results($sql);
//    echo $sql."<br>";

	foreach ( $all as $d ){
    	$in = $in . $d->id . ',';
	    $n = ( $d->form_id=='' )?1:$d->form_id;
    	$data[$d->id]['id'] = $d->id;
    	$data[$d->id]['form'] = $fnames[$n];
    	$data[$d->id]['date'] = $d->sub_date;
    	$data[$d->id]['email'] = $d->email;
    	$data[$d->id]['ip'] = $d->ip;
	}

	$where = 'sub_id IN ('.substr($in,0,-1).')';
    $sql = "SELECT * FROM {$wpdb->cformsdata} WHERE $where";
	$all = $wpdb->get_results($sql);

	foreach ( $all as $d )
    	$data[$d->sub_id]['data'][$d->field_name] = $d->field_val;

	return $data;
}

class cformsRSS {
	function vars($public_query_vars) {
        $public_query_vars[] = 'cformsRSS';
        return $public_query_vars;
    }

	function outputRSS() {
		global $wpdb, $cformsSettings, $plugindir;
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
				$entries = $wpdb->get_results("SELECT * FROM {$wpdb->cformssubmissions} $WHERE ORDER BY sub_date DESC LIMIT 0,".$rsscount);

				$content = '';
				if( count($entries)>0 ){
					foreach($entries as $entry){
			                $entrylink = get_option('siteurl').'/wp-admin/admin.php?page='.$plugindir.'/cforms-database.php&amp;d-id='.$entry->id.'#entry'.$entry->id;
							$content.= "\t".'<item>'."\n".
										"\t\t".'<title>'.$entry->email.'</title>'."\n".
										"\t\t".'<description><![CDATA[ '.__('Form submitted on','cforms').' '.$entry->sub_date.($single?'':' via "'.$cformsSettings['form'.$entry->form_id]['cforms'.$entry->form_id.'_fname'].'"').'. <a href="'.$entrylink.'">'.__('View details','cforms').'</a>.]]></description>'."\n".
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
	<title><?php if ($single) echo __('New submissions for >>', 'cforms').' '.$cformsSettings['form'.$no]['cforms'.$no.'_fname']; else _e('All new form submissions', 'cforms'); ?></title>
	<atom:link href="<?php echo get_option('siteurl').'?cformsRSS='.$no.urlencode('$#$').$cformsSettings['form'.$no]['cforms'.$no.'_rsskey']; ?>" rel="self" type="application/rss+xml" />
	<link><?php echo get_option('siteurl'); ?></link>
	<description><?php _e('This RSS feed provides you with the most recent form submissions.', 'cforms') ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
	<language><?php echo get_option('rss_language'); ?></language>
<?php echo $content; ?>
</channel>
</rss>
<?php
				exit;
			}
		}
	}
}
add_filter('query_vars', array('cformsRSS', 'vars'));
add_action('template_redirect', array('cformsRSS', 'outputRSS'));
?>