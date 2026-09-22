<?php
if (!defined('ABSPATH')) exit;

define('RRV_VERSION', '1.0.0');
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
    add_theme_support('custom-logo', ['height'=>120,'width'=>320,'flex-height'=>true,'flex-width'=>true]);
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    register_nav_menus(['primary'=>__('Primary Menu','royalrestvilla'),'footer'=>__('Footer Menu','royalrestvilla')]);
    add_image_size('rrv-room', 900, 675, true);
    add_image_size('rrv-gallery', 1200, 850, true);
}
add_action('after_setup_theme','rrv_setup');

function rrv_assets() {
    wp_enqueue_style('rrv-fonts','https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap',[],null);
    wp_enqueue_style('rrv-style',get_stylesheet_uri(),['rrv-fonts'],RRV_VERSION);
    wp_enqueue_script('rrv-main',RRV_URI.'/assets/js/main.js',[],RRV_VERSION,true);
}
add_action('wp_enqueue_scripts','rrv_assets');

function rrv_body_classes($classes){ $classes[]='rrv-site'; return $classes; }
add_filter('body_class','rrv_body_classes');

function rrv_get_theme_mod($key,$default=''){ return get_theme_mod($key,$default); }
function rrv_phone_href($phone){ return preg_replace('/[^0-9+]/','',$phone); }
function rrv_whatsapp_href($phone){ return preg_replace('/\D/','',$phone); }
function rrv_booking_url(){
    $page = get_page_by_path('book-now');
    return $page ? get_permalink($page) : home_url('/book-now/');
}

function rrv_activation_seed(){
    if (get_option('rrv_seeded')) return;
    $page_id = wp_insert_post(['post_title'=>'Book Now','post_name'=>'book-now','post_status'=>'publish','post_type'=>'page','post_content'=>'[rrv_booking_form]']);
    if (!is_wp_error($page_id)) update_option('rrv_seeded',1);
}
add_action('after_switch_theme','rrv_activation_seed');
