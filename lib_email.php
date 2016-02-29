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

class cforms2_mail {
	public $eol;
	public $html_show;
	public $html_show_ac;

	public $f_txt;
	public $f_html;

	public $err  = '';

	public $subj  = '';

	public $body = '';
	public $body_alt  = '';

	private $content_type = 'text/plain';
	private $from = '';
	private $fname = 'cforms';

	private $to = array();
	private $cc = array();
	private $bcc = array();
	private $replyto = array();

	private $up = array();

	private $err_count = 0;

	###
	### setup
	###
    public function __construct($no, $from, $to, $replyto='', $adminEmail=false) {
		$cformsSettings = get_option('cforms_settings');

		$this->eol = $cformsSettings['global']['cforms_crlf']['b'] != 1 ? "\r\n" : "\n";

		$this->html_show    = substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1';
		$this->html_show_ac = substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],3,1)=='1';

	    $this->f_txt  = substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1';
	    $this->f_html = substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],1,1)=='1';

	    $this->to = array();
	    $this->cc = array();
	    $this->bcc = array();
	    $this->replyto = array();
	    $this->up = array();

	    ### from
	    if ( $from=='' )
	        $from = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';

	    $fe=array();
	    $f=array();
	    if( preg_match('/([\w-\+\.]+@([\w-]+\.)+[\w-]{2,63})/',$from,$fe) )
	        $this->from = $fe[0];

	    if( preg_match('/(.*)\s+(([\w-\.]+@|<)).*/',$from,$f) )
	        $this->fname = str_replace('"','',$f[1]);
	    else
	        $this->fname = $fe[0];

	    ### reply-to
	    $te=array();
	    $t=array();
	    if( preg_match('/([\w-\+\.]+@([\w-]+\.)+[\w-]{2,63})/',$replyto,$te) ) {
	        if ( preg_match('/(.*)\s+(([\w-\+\.]+@|<)).*/',$replyto,$t) )
	            $this->add_reply($te[0] ,str_replace('"','',$t[1]) );
	        else
	            $this->add_reply($te[0]);
	    }

	    ### bcc
	    $te=array();
	    $t=array();

	    $addresses = explode(',',stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_bcc']) );
	    foreach( $addresses as $a ){
	        if( preg_match('/([\w-+\.]+@([\w-]+\.)+[\w-]{2,63})/',$a,$te) && $adminEmail) {
	            if ( preg_match('/(.*)\s+(([\w-+\.]+@|<)).*/',$a,$t) )
	                $this->add_bcc($te[0] ,str_replace('"','',$t[1]) );
	            else
		            $this->add_bcc($te[0]);
	        }
		}


	    ### to
	    $te=array();
	    $t=array();
	    $addresses = explode(',',$to);

	    foreach( $addresses as $a ){
	        if( preg_match('/([\w-+\.]+@([\w-]+\.)+[\w-]{2,63})/',$a,$te) ) {
	            if ( preg_match('/(.*)\s+(([\w-+\.]+@|<)).*/',$a,$t) )
	                $this->add_addr($te[0] ,str_replace('"','',$t[1]) );
	            else
	                $this->add_addr($te[0]);
	        }
	    }

    }

	###
	### General Functions
	###
	public function is_html($bool) {
		$this->content_type = $bool?'text/html':'text/plain';
	}

	###
	### Header Functions
	###
	private function add_addr($address, $name = '') {
	    $t = count($this->to);
	    $this->to[$t][0] = trim($address);
	    $this->to[$t][1] = $name;
	}
	private function add_cc($address, $name = '') {
	    $t = count($this->cc);
	    $this->cc[$t][0] = trim($address);
	    $this->cc[$t][1] = $name;
	}
	private function add_bcc($address, $name = '') {
	    $t = count($this->bcc);
	    $this->bcc[$t][0] = trim($address);
	    $this->bcc[$t][1] = $name;
	}
	private function add_reply($address, $name = '') {
	    $t = count($this->replyto);
	    $this->replyto[$t][0] = trim($address);
	    $this->replyto[$t][1] = $name;
	}
	private function addr_add($type, $addr) {
		$addr_str = $type . ': ';
	    $addr_str .= $this->addr_fmt($addr[0]);
	    if(count($addr)>1) {
            for($i = 1; $i < count($addr); $i++) {
                $addr_str .= ', ' . $this->addr_fmt($addr[$i]);
            }
	    }
		return $addr_str;
	}
	private function addr_fmt($addr) {
		return empty($addr[1]) ? $this->fix_header($addr[0]) : $this->fix_header($addr[1]) . " <" . $this->fix_header($addr[0]) . ">";
	}
	private function fix_header($t) {
	    $t = trim($t);
	    $t = str_replace("\r", "", $t);
	    return str_replace("\n", "", $t);
	}
	private function mail_header() {
	    $r = array();

	    $from = array();
	    $from[0][0] = trim($this->from);
	    $from[0][1] = $this->fname;
	    $r[] = $this->addr_add('From', $from);

		$r[] = (count($this->cc) > 0) ? $this->addr_add('Cc', $this->cc):'';
		$r[] = (count($this->bcc) > 0) ? $this->addr_add('Bcc', $this->bcc):'';
	    $r[] = (count($this->replyto) > 0) ? $this->addr_add('Reply-to', $this->replyto):'';

		$r[] = sprintf("Content-Type: %s", $this->content_type);
	    return $r;
	}

	###
	### Body Functions
	###
	private function mail_body() {
	    return $this->err_count > 0 ? '' : $this->body;
	}

	###
	### Send Functions
	###
	public function send() {
	    $this->err_count = 0;

	    if((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
	      $this->set_err( __('You must provide at least one recipient email address.','cforms2') );
	      return false;
	    }

	    $header  = $this->mail_header();
	    $body    = $this->mail_body();

        ### bail out
	    if ($body == '') return false;

	    $to = '';
	    for($i = 0; $i < count($this->to); $i++) {
            $to .= (($i != 0) ? ', ':'' ) . $this->addr_fmt($this->to[$i]);
        }
		add_action('phpmailer_init', array($this, 'phpmailer_init'));
		$rt = wp_mail($to, $this->fix_header($this->subj), $body, $header, $this->up);
		remove_action('phpmailer_init', array($this, 'phpmailer_init'));

	    if(!$rt) {
	      $this->set_err(__('Could not instantiate wp_mail function.','cforms2'));
	      return false;
	    }

	    return true;
	}
	
	public function phpmailer_init($phpmailer) {
		$phpmailer->LE = $this->eol;
		$phpmailer->AltBody = $this->body_alt;
	}

	public function add_file($path) {
	    if(!is_file($path)) {
	        $this->set_err(__('Could not access file: ','cforms2'));
	    }

	    $t = count($this->up);
	    $this->up[$t] = $path;
	}

	private function set_err($m) {
	    $this->err = $m;
	    $this->err_count++;
	}

}
