<?php
if (!defined('ABSPATH')) exit;

define('RRV_VERSION', '1.1.0');
define('RRV_DIR', get_template_directory());
define('RRV_URI', get_template_directory_uri());

require_once RRV_DIR . '/inc/customizer.php';
require_once RRV_DIR . '/inc/content-types.php';
require_once RRV_DIR . '/inc/booking.php';
require_once RRV_DIR . '/inc/seo.php';

function rrv_setup() {
    load_theme_textdomain('royalrestvilla', RRV_DIR . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', ['height'=>160,'width'=>420,'flex-height'=>true,'flex-width'=>true]);
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    register_nav_menus(['primary'=>__('One Page Menu','royalrestvilla')]);
    add_image_size('rrv-room', 900, 675, true);
    add_image_size('rrv-gallery', 1200, 850, true);
}
add_action('after_setup_theme','rrv_setup');

function rrv_assets() {
    wp_enqueue_style('rrv-fonts','https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap',[],null);
    wp_enqueue_style('rrv-style',get_stylesheet_uri(),['rrv-fonts'],RRV_VERSION);
    wp_enqueue_style('rrv-one-page',RRV_URI.'/assets/css/one-page.css',['rrv-style'],RRV_VERSION);
    wp_enqueue_script('rrv-main',RRV_URI.'/assets/js/main.js',[],RRV_VERSION,true);
}
add_action('wp_enqueue_scripts','rrv_assets');

function rrv_body_classes($classes){ $classes[]='rrv-site'; if(is_front_page())$classes[]='rrv-one-page'; return $classes; }
add_filter('body_class','rrv_body_classes');

function rrv_get_theme_mod($key,$default=''){ return get_theme_mod($key,$default); }
function rrv_phone_href($phone){ return preg_replace('/[^0-9+]/','',$phone); }
function rrv_whatsapp_href($phone){ return preg_replace('/\D/','',$phone); }
function rrv_home_anchor($id='home'){ return is_front_page() ? '#'.ltrim($id,'#') : home_url('/#'.ltrim($id,'#')); }
function rrv_booking_url(){ return home_url('/#book'); }

function rrv_one_page_menu_fallback(){
    echo '<ul>';
    foreach([
        'home'=>'Home','about'=>'The Villa','rooms'=>'Rooms','amenities'=>'Amenities',
        'gallery'=>'Gallery','reviews'=>'Reviews','contact'=>'Contact'
    ] as $id=>$label){
        echo '<li><a href="'.esc_url(rrv_home_anchor($id)).'">'.esc_html($label).'</a></li>';
    }
    echo '</ul>';
}

function rrv_gallery_images(){
    $images=[];
    for($i=1;$i<=6;$i++){
        $id=absint(get_theme_mod('rrv_gallery_'.$i));
        if($id){
            $url=wp_get_attachment_image_url($id,'rrv-gallery');
            if($url)$images[]=['id'=>$id,'url'=>$url,'alt'=>get_post_meta($id,'_wp_attachment_image_alt',true)];
        }
    }
    return $images;
}

function rrv_trim_words($text,$count=24){
    return wp_trim_words(wp_strip_all_tags($text),$count,'…');
}
