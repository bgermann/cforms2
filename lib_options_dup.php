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

	$noDISP='1'; $no='';
	if( isset($_REQUEST['no']) ) {
		if( $_REQUEST['no']<>'1' )
			$noDISP = $no = $_REQUEST['no'];
	}

	$FORMCOUNT++;
	$cformsSettings['global']['cforms_formcount'] =(string)($FORMCOUNT);

	### new settings container
    foreach( array_keys($cformsSettings['form'.$no]) as $k ){
		$tmp = preg_match('/cforms\d*_(.*)/',$k, $kk);
        if( strpos($k,'_fname')!==false )
			$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_'.$kk[1]] = $cformsSettings['form'.$no][$k].' ('.__('copy of form #', 'cforms2').($no==''?'1':$no).')';
		else
			$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_'.$kk[1]] = $cformsSettings['form'.$no][$k];
	}

    echo '<div id="message" class="updated fade"><p>'.__('The form has been duplicated, you\'re now working on the copy.', 'cforms2').'</p></div>';

	update_option('cforms_settings',$cformsSettings);

	//set $no afterwards: need it to duplicate fields
	$no = $noDISP = $FORMCOUNT;
