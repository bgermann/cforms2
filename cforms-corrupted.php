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

### Check Whether User Can Manage Database
cforms2_check_access_priv();
?>
<div class="wrap">
	<div id="icon-cforms-corrupted" class="icon32"><br/></div><h2><?php _e('cforms error','cforms2')?></h2>

<?php if( $_POST['fixsettings'] ) :?>

<div class="error"><p><?php _e('Please deactivate and then re-activate the cforms plugin now.','cforms2'); ?></p></div>
<?php
	$c = stripslashes($_POST['currentsettings']);

    $nc='';
    for($i=0; $i<strlen($c); $i++ ){

		if ( substr($c,$i,2) == 's:' ){
			$q1=strpos($c,'"',$i);
			$q2=strpos($c,'";',$q1)-1;
        	$nc .= 's:'.($q2-$q1).':'.substr($c,$q1,($q2-$q1)+3);
            $i = $i + ($q2-$q1) +6 + (strlen(strval($q2-$q1))) -1;
        }
		else
        	$nc .= substr($c,$i,1);
    }

    update_option('cforms_settings',$nc);
	die();
?>

<?php elseif( $_POST['resetsettings'] ) : ?>

<div class="updated fade"><p><?php _e('Please deactivate and then re-activate the cforms plugin now.','cforms2'); ?></p></div>
<?php
    delete_option('cforms_settings');
	cforms2_setup_db();
	wp_die();
?>

<?php else :?>

<div class="error"><p><?php _e('It appears that WP has corrupted your cforms settings, the settings array can not be read properly.','cforms2'); ?></p></div>

<?php endif;

global $wpdb;
$c = str_replace('&','&amp;',$wpdb->get_var("SELECT option_value FROM `$wpdb->options` WHERE option_name='cforms_settings'"));

?>

	<form name="corruptedsettings" class="corruptedsettings" method="POST">

	    <h3><?php _e('Corrupted cforms settings detected','cforms2'); ?></h2>
	    <table class="form-table">
	        <tr><td><?php _e('You can either try and fix the settings array or reset it and start from scratch.','cforms2'); ?> &nbsp;<input class="allbuttons deleteall" type="submit" name="resetsettings" id="resetsettings" value="<?php _e('RESET','cforms2'); ?>"/></td></tr>
	    </table>

	    <h3><?php _e('Corrupted cforms settings array (raw code)','cforms2'); ?></h2>
	    <table class="form-table">
        	<tr><td>
            	<?php _e('Depending on your Wordpress/PHP skills you may want to try and fix the serialized data below, then hit the fix button or try just like that, cforms may magically fix it for you.','cforms2'); ?>
			</td></tr>
        	<tr><td>
		        <textarea rows="16" cols="10" name="currentsettings" id="currentsettings"><?php echo $c; ?></textarea>
            </td></tr>
        	<tr><td>
		        <input class="allbuttons" type="submit" name="fixsettings" id="fixsettings" value="<?php _e('FIX and save data','cforms2'); ?>"/>
            </td></tr>
		</table>

    </form>

</div>
<?php wp_die();
