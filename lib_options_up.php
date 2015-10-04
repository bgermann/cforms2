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

	$file = $_FILES['importall'];
	$err = '';

    $noDISP = '1'; $no='';
    if( $_REQUEST['noSub']<>'1' )
        $noDISP = $no = $_REQUEST['noSub'];

	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
			$err = $file['error'];

	// A non-empty file will pass this test.
	if ( !( $file['size'] > 0 ) )
			$err = __('File is empty. Please upload something more substantial.', 'cforms2');

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! is_uploaded_file( $file['tmp_name'] ) )
			$err = __('Specified file failed upload test.', 'cforms2');

	if ( $err <> '' ){

	  echo '<div id="message" class="updated fade"><p>'.__('Error:', 'cforms2').' '.$err.'</p></div>';

	} else if( isset($_REQUEST['uploadcformsdata']) ) {

		$fo = fopen($file['tmp_name'],"rb");
		$cformsSettings['form'.$no] = cforms2_load_array( $no , $fo );
		update_option('cforms_settings',$cformsSettings);

		echo '<div id="message" class="updated fade"><p>'.__('All form specific settings have been restored from the backup file.', 'cforms2').'</p></div>';

	} else if( isset($_REQUEST['restoreallcformsdata']) ) {

		$fo = fopen($file['tmp_name'],"rb");
		$cformsSettings = cforms2_load_array( '-1', $fo );

        update_option('cforms_settings',$cformsSettings);

		echo '<div id="message" class="updated fade"><p>'.__('All cforms settings have been restored from the backup file.', 'cforms2').'</p></div>';
	}

	function cforms2_load_array($k, $vFile){
	    $ForRet = array();

/*		### corrupted file fix
        if (  ftell($vFile)==0  ){
        	while (  $Wert != "00"  ){
	            $pos  = ftell($vFile);
				$Wert = bin2hex( fread($vFile,1) );
                if( $pos > 10 )
					wp_die(__('Corrupted File detected. Restore process aborted.', 'cforms2'));
            }
		    fseek($vFile, $pos+1);
		}
*/
	    $Wert = fread($vFile,2);

	    if ($Wert != "\0{") return;
	    while (true) {
	        if (cforms2_next_matches($vFile,"\0}")) {
	            fread($vFile,2);
	            return $ForRet;
	        }

	        $MyKey = "";
	        while (true) {
	            $Zeichen = fread($vFile,1);
	            if ($Zeichen == "\0")
	                break;
	            else
	                $MyKey .= $Zeichen;
	        }
	        $MyKey = stripslashes($MyKey);

	        if (cforms2_next_matches($vFile,"\0{")) {
				if ($k<>'-1' && !is_array($MyKey))
                	$MyKey = 'cforms'.$k.substr( $MyKey, strpos($MyKey,'_') );
                $ForRet[$MyKey] = cforms2_load_array($k,$vFile);
	            fread($vFile,1);
	        } else {
	            $MyVal = "";
	            while (true) {
	                $Zeichen = fread($vFile,1);
	                if ($Zeichen == "\0")
	                    break;
	                else
	                    $MyVal .= $Zeichen;
	            }
	            $MyVal = stripslashes($MyVal);
				if ($k<>'-1' && !is_array($MyKey))
                	$MyKey = 'cforms'.$k.substr( $MyKey, strpos($MyKey,'_') );
	            $ForRet[$MyKey] = $MyVal;
	        }

	    }
	}
	### Syntax: cforms2_next_matches($vFile, $Text);
	function cforms2_next_matches($vFile, $Text){
	    $PrevPos = ftell($vFile);
	    $Jump = strlen($Text);
	    $stats = fstat($vFile);
	    if (ftell($vFile) + $Jump > $stats[7])
	        return false;
	    $Erg = fread($vFile,$Jump);
	    fseek($vFile, $PrevPos);
	    return ($Erg == $Text);
	}
