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

	$file = plugin_dir_path(__FILE__) . 'formpresets'. DIRECTORY_SEPARATOR . $_REQUEST['formpresets'];

    if( is_file($file) && filesize($file) > 0)
        $fields = file($file);
    else {
        echo '<div id="message" class="updated fade"><p><strong>'.__('Sorry, this form preset can\'t be loaded. I Can\'t find file ', 'cforms2').'<br />'.$file.'</strong></p></div>';
        return;
    }

	$i = 1;
	$taf = false;
	foreach( $fields as $field ){
		if ( strpos($field,'~~~')===false ) continue;

		$data = explode('~~~',$field);
		if( $data[0]=='ff' ){
			$cformsSettings['form'.$no]["cforms{$no}_count_field_{$i}"] = str_replace(array("\n","\r"),array('',''),$data[1]);
			$i++;
		}
		else if( $data[0]=='mx' ){
			$cformsSettings['form'.$no]["cforms{$no}_maxentries"] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='su' ){
			$cformsSettings['form'.$no]["cforms{$no}_submit_text"] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='lt' ){
			$cformsSettings['form'.$no]["cforms{$no}_limittxt"] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='hd' ){
			$cformsSettings['form'.$no]["cforms{$no}_hide"] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='ri' ){
			$cformsSettings['form'.$no]["cforms{$no}_required"] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='re' ){
			$cformsSettings['form'.$no]["cforms{$no}_emailrequired"] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='tf' ){
			$cformsSettings['form'.$no]["cforms{$no}_tellafriend"] =  str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='tt' ){
			$cformsSettings['form'.$no]["cforms{$no}_cmsg"] =  str_replace('|nl|',"\r\n",$data[1]) ;
			$cformsSettings['form'.$no]["cforms{$no}_cmsg_html"] =  str_replace('|nl|',"<br />\r\n",$data[1]) ;
			$cformsSettings['form'.$no]["cforms{$no}_confirm"] =  '1';
			$taf = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='ts' ){
			$cformsSettings['form'.$no]["cforms{$no}_csubject"] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='cs' ){
			$cformsSettings['global']['cforms_css'] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
		else if( $data[0]=='dp' ){
			$cformsSettings['global']['cforms_datepicker'] = str_replace(array("\n","\r"),array('',''),$data[1]);
		}
	}

	$max = $cformsSettings['form'.$no]["cforms{$no}_count_fields"];
	for ( $j=$i; $j<=$max; $j++) {
		$cformsSettings['form'.$no]["cforms{$no}_count_field_{$j}"] = '';
    }

	$cformsSettings['form'.$no]["cforms{$no}_count_fields"] = ($i-1);
