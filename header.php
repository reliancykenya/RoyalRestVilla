<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="rrv-header" id="site-header">
  <div class="rrv-container rrv-header__inner">
    <a class="rrv-brand" href="<?php echo esc_url(rrv_home_anchor('home')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
      <?php if(has_custom_logo()){the_custom_logo();}else{echo '<span class="rrv-wordmark">'.esc_html(get_bloginfo('name')).'</span>';} ?>
    </a>
    <button class="rrv-menu-toggle" aria-expanded="false" aria-controls="rrv-primary">☰<span class="screen-reader-text">Menu</span></button>
    <nav id="rrv-primary" class="rrv-nav" aria-label="Primary navigation">
      <ul>
        <li><a href="<?php echo esc_url(rrv_home_anchor('home')); ?>">Home</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('about')); ?>">The Villa</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('rooms')); ?>">Rooms</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('amenities')); ?>">Amenities</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('gallery')); ?>">Gallery</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('reviews')); ?>">Reviews</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('contact')); ?>">Contact</a></li>
      </ul>
    </nav>
    <a class="rrv-btn rrv-btn--gold rrv-header__cta" href="<?php echo esc_url(rrv_booking_url()); ?>">Book now</a>
  </div>
</header>
<main id="main-content">
