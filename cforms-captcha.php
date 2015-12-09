<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2015 Bastian Germann
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

/**
 * The foundation for the pluggable CAPTCHA support.
 * Each implementation should be instantiated once and added to cformsII
 * by using the filter cforms2_add_captcha with the method add_instance.
 */
abstract class cforms2_captcha {

	/**
	 * @return string The human readable name for this CAPTCHA type that appears in the GUI.
	 */
	abstract public function get_name();

	/**
	 * Returns an associative array consisting of
	 * "html" => HTML including at least an input field with this class's name as name
	 * "hint" => the information needed for check_response method to evaluate the response
	 * 
	 * @param string $input_classes The class names for the input field
	 * @param string $input_title The title for the input field
	 * @return array array("html"=>... , "hint"=>...)
	 */
	abstract public function get_request($input_classes, $input_title);

	/**
	 * Checks the answer given by the user for correctness.
	 * 
	 * @param string $hint The hint that leads to the correct answer.
	 * @param string $answer The answer given by the user.
	 * @return bool true, if $answer was correct.
	 */
	abstract public function check_response($hint, $answer);

	/**
	 * Renders the HTML required for the settings modal dialog.
	 */
	abstract public function render_settings();

	/**
	 * Adds this instance with the classname as a key to the array.
	 * 
	 * @param array $captchas
	 * @return array The original array with a pair added.
	 */
	public final function add_instance(array $captchas) {
		$captchas[get_class($this)] = $this;
		return $captchas;
	}

	/**
	 * @return bool true, if all users have to resolve the CAPTCHA, including the authenticated users.
	 */
	public function check_authn_users() {
		return false;
	}

}

/**
 * Replaces the old Q&A feature.
 */
final class cforms2_question_and_answer extends cforms2_captcha {

	private $cforms_settings;

	public function __construct() {
		$this->cforms_settings = get_option('cforms_settings');
	}

	public function get_name() {
		return __('Visitor verification (Q&amp;A)', 'cforms2');
	}

	public function check_authn_users() {
		return $this->cforms_settings['global']['cforms_captcha_def']['foqa'] == '1';
	}

	public function check_response($hint, $answer) {
		$q = $this->question_and_answer(intval($hint));
		return strcasecmp($answer, $q[2]) === 0;
	}

	public function get_request($input_classes, $input_title) {
		$id = get_class($this);
        $q = $this->question_and_answer();
		$label = stripslashes(htmlspecialchars($q[1]));
		$req['hint'] = $q[0];

		$req['html'] = '<label for="'.$id.'" class="secq"><span>' . stripslashes(($label)) . '</span></label>'
				     . '<input type="text" name="'.$id.'" id="'.$id.'" '
		             . 'class="'.$input_classes.'" title="'.$input_title.'"/>';
		return $req;
	}
	
	/**
	 * Returns the nth question & answer pair.
	 * 
	 * @param int $n The nth pair. If negative, $n is random.
	 * @return array array(n, qestion, answer)
	 */
	private function question_and_answer($n = -1) {
		$qall = explode( "\r\n", $this->cforms_settings['global']['cforms_sec_qa'] );
		if ($n < 0)
			$n = mt_rand( 0, count($qall)-1 );
		$q = explode( '=', $qall[$n]);
		array_unshift($q, $n);
		return $q;
	}

	public function render_settings() {
		require ('include/textfield.php');
	}

}

add_filter('cforms2_add_captcha', array(new cforms2_question_and_answer(), 'add_instance'));

add_action( 'wp_ajax_cforms2_reset_captcha', 'cforms2_reset_captcha' );
add_action( 'wp_ajax_nopriv_cforms2_reset_captcha', 'cforms2_reset_captcha' );

function cforms2_reset_captcha() {
check_admin_referer( 'cforms2_reset_captcha' );

$cformsSettings = get_option('cforms_settings');
$cap = $cformsSettings['global']['cforms_captcha_def'];

### overwrite for admin demo purposes, no cookie set though
if ( count($_GET)>4 )
	$cap = $_GET;

$min = cforms2_prepVal( $cap['c1'],4 );
$max = cforms2_prepVal( $cap['c2'],5 );
$src = cforms2_prepVal( $cap['ac'], 'abcdefghijkmnpqrstuvwxyz23456789');

$img_sz_type	= 0;
$img_sz_width	= cforms2_prepVal($cap['w'],115);
$img_sz_height	= cforms2_prepVal($cap['h'],25);

$im_bg_type		= 1;
$im_bg_url		= plugin_dir_path(__FILE__) . 'captchabg/' . ( cforms2_prepVal($cap['bg'],'1.gif') );

$font_url		= plugin_dir_path(__FILE__) . 'captchafonts/' . ( cforms2_prepVal($cap['f'],'font4.ttf') );

$min_font_size	= cforms2_prepVal($cap['f1'],17);
$max_font_size	= cforms2_prepVal($cap['f2'],19);

$min_angle		= cforms2_prepVal($cap['a1'],-12);
$max_angle		= cforms2_prepVal($cap['a2'],12);

$col_txt_type	= 4;
$col			= cforms2_prepVal($cap['c'],'#000066');
$col_txt_r		= hexdec(substr($col,1,2));
$col_txt_g		= hexdec(substr($col,3,2));
$col_txt_b		= hexdec(substr($col,5,2));

$border			= cforms2_prepVal($cap['l'],'#000066');
$border_r		= hexdec(substr($border,1,2));
$border_g		= hexdec(substr($border,3,2));
$border_b		= hexdec(substr($border,5,2));

$char_padding	= 2;
$output_type	= 'png';

$no 			= cforms2_prepVal($_GET['ts'],'');

### captcha random code
$srclen = strlen($src)-1;
$length = mt_rand($min,$max);

$turing = '';
for($i=0; $i<$length; $i++) {
	$turing .= substr($src, mt_rand(0, $srclen), 1);
}

$tu = ($cap['i']=='i')?strtolower($turing):$turing;

if ( ! ( isset($_GET['c1']) || isset($_GET['c2']) || isset($_GET['ac']) ) )
	setcookie("turing_string_".$no, $cap['i'].'+'.md5($tu),(time()+60*60*5),"/");

$font = $font_url;

### initialize variables

$length = strlen($turing);
$data = array();
$image_width = $image_height = 0;

$codelen = 0;

### build the data array of the characters, size, placement, etc.

for($i=0; $i<$length; $i++) {
  $char = substr($turing, $i, 1);

  $size = mt_rand($min_font_size, $max_font_size);
  $angle = mt_rand($min_angle, $max_angle);

  $bbox = ImageTTFBBox( $size, $angle, $font, $char );

  $char_width = max($bbox[2], $bbox[4]) - min($bbox[0], $bbox[6]);
  $char_height = max($bbox[1], $bbox[3]) - min($bbox[7], $bbox[5]);

  $codelen = $codelen + $char_width + $char_padding;

  $image_width += $char_width + $char_padding;
  $image_height = max($image_height, $char_height);
  $data[] = array('char'=>$char,'size'=>$size,'angle'=>$angle,'height'=>$char_height,'width'=>$char_width);
}

### calculate the final image size, adding some padding

$x_padding = 12;
if ( $img_sz_type == 1 ) {
	$image_width += ($x_padding * 2);
	$image_height = ($image_height * 1.5) + 2;
} else {
	$image_width = $img_sz_width;
	$image_height = $img_sz_height;
}

### build the image, and allocte the colors

$im = ImageCreate($image_width, $image_height);

$d1 = $d2 = $d3 = 0;
while ( ($d1<50) AND ($d2<50) AND ($d3<50) ) {
	$r = mt_rand(200,255);	$g = mt_rand(200,255);	$b = mt_rand(200,255);
	$d1 = abs($r-$g);	$d2 = abs($r-$b);	$d3 = abs($g-$b);
}

ImageColorAllocate($im, $r, $g, $b );
$color_border = ImageColorAllocate($im, $border_r, $border_g, $border_b);
ImageColorAllocate($im, round($r*0.85), round($g*0.85), round($b*0.85) );
ImageColorAllocate($im, round($r*0.95), round($g*0.95), round($b*0.95) );
ImageColorAllocate($im, round($r*0.90), round($g*0.90), round($b*0.90) );

$d1 = mt_rand(0,50); $d2 = mt_rand(0,50); $d3 = mt_rand(0,50);

$d1 = $d2 = $d3 = 0;
while ( ($d1<100) AND ($d2<100) AND ($d3<100) ) {
	$r = mt_rand(0,150); $g = mt_rand(0,150); $b = mt_rand(0,150);
	$d1 = abs($r-$g); $d2 = abs($r-$b); $d3 = abs($g-$b);
}

switch ( $col_txt_type ) {
	case 1 : $col_txt    = ImageColorAllocate($im, $r, $g, $b ); break;
	case 2 : $col_txt    = ImageColorAllocate($im, 0, 0, 0 ); break;
	case 3 : $col_txt    = ImageColorAllocate($im, 255, 255, 255 );	break;
	case 4 : $col_txt    = ImageColorAllocate($im, $col_txt_r, $col_txt_g, $col_txt_b ); break;
}

$image_data=getimagesize($im_bg_url);
$image_type=$image_data[2];

if($image_type==1)      $img_src=imagecreatefromgif($im_bg_url);
elseif($image_type==2)  $img_src=imagecreatefromjpeg($im_bg_url);
elseif($image_type==3)  $img_src=imagecreatefrompng($im_bg_url);

if ( $im_bg_type == 1 ) {
	imagesettile($im,$img_src);
	imageFilledRectangle ($im, 0, 0, $image_width, $image_height, IMG_COLOR_TILED);
} else
	imagecopyresampled($im,$img_src,0,0,0,0,$image_width,$image_height,$image_data[0],$image_data[1]);

$pos_x = ($image_width - $codelen) / 2;
foreach($data as $d) {
	$pos_y = ( ( $image_height + $d['height'] ) / 2 );
	ImageTTFText($im, $d['size'], $d['angle'], $pos_x, $pos_y, $col_txt, $font, $d['char'] );
	$pos_x += $d['width'] + $char_padding;
}

### a nice border
ImageRectangle($im, 0, 0, $image_width-1, $image_height-1, $color_border);

// There can be some output from other loaded PHP files, therefore clean output.
ob_end_clean();

switch ($output_type) {
	case 'jpeg': Header('Content-type: image/jpeg'); ImageJPEG($im,NULL,100); break;
	case 'png':
    default:	Header('Content-type: image/png'); ImagePNG($im); break;
}

flush();
ImageDestroy($im);
die();
}

### strip stuff
function cforms2_prepVal($v,$d) {
	return ($v<>'') ? stripslashes($v) : $d;
}
