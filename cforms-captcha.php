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
