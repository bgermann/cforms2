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

	$noDISP = '1'; $no='';
	if( $_REQUEST['no']<>'1' )
		$noDISP = $no = $_REQUEST['no'];

	for ( $i=(int)$noDISP; $i < $cformsSettings['global']['cforms_formcount']; $i++) {  // move all forms "to the left"

		$n = ($i==1)?'':$i;
		unset( $cformsSettings['form'.$n] );

		foreach(array_keys($cformsSettings['form'.($i+1)]) as $key){
            $newkey = ( strpos($key,'form2_')!==false )?str_replace('2_','_',$key):str_replace(($i+1).'_',$i.'_',$key);
			$cformsSettings['form'.$n][$newkey] = $cformsSettings['form'.($i+1)][$key];
		}

	}

    unset( $cformsSettings['form'.$cformsSettings['global']['cforms_formcount']] );

	$FORMCOUNT=$FORMCOUNT-1;

	if ( $FORMCOUNT>1 ) {
		if( isset($_REQUEST['no']) && (int)$_REQUEST['no'] > $FORMCOUNT ) // otherwise stick with the current form
			$no = $noDISP = $FORMCOUNT;
	} else {
		$noDISP = '1'; $no='';
	}
	$cformsSettings['global']['cforms_formcount'] = (string)($FORMCOUNT);

	update_option('cforms_settings',$cformsSettings);

	echo '<div id="message" class="updated fade"><p>'. __('Form deleted', 'cforms2').'.</p></div>';
