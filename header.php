<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head><body <?php body_class(); ?>><?php wp_body_open(); ?>
<header class="rrv-header"><div class="rrv-container rrv-header__inner">
<a class="rrv-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>"><?php if(has_custom_logo()){the_custom_logo();}else{echo '<span class="rrv-wordmark">'.esc_html(get_bloginfo('name')).'</span>';} ?></a>
<button class="rrv-menu-toggle" aria-expanded="false" aria-controls="rrv-primary">☰<span class="screen-reader-text">Menu</span></button>
<nav id="rrv-primary" class="rrv-nav" aria-label="Primary navigation"><?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>'rrv_menu_fallback']); ?></nav>
<a class="rrv-btn rrv-btn--gold rrv-header__cta" href="<?php echo esc_url(rrv_booking_url()); ?>">Book now</a>
</div></header>
<?php function rrv_menu_fallback(){echo '<ul><li><a href="'.esc_url(home_url('/')).'">Home</a></li><li><a href="'.esc_url(home_url('/#rooms')).'">Rooms</a></li><li><a href="'.esc_url(home_url('/#about')).'">About</a></li><li><a href="'.esc_url(home_url('/#contact')).'">Contact</a></li></ul>';} ?>
<main id="main-content">
