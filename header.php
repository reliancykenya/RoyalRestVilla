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
      <?php
      $logo_id=absint(get_theme_mod('custom_logo'));
      if($logo_id){
          echo wp_get_attachment_image($logo_id,'full',false,['class'=>'custom-logo','alt'=>get_bloginfo('name')]);
      }else{
          echo '<span class="rrv-wordmark">'.esc_html(get_bloginfo('name')).'</span>';
      }
      ?>
    </a>
    <button class="rrv-menu-toggle" aria-expanded="false" aria-controls="rrv-primary">☰<span class="screen-reader-text">Menu</span></button>
    <nav id="rrv-primary" class="rrv-nav" aria-label="Primary navigation">
      <?php wp_nav_menu([
          'theme_location'=>'primary',
          'container'=>false,
          'menu_class'=>'rrv-nav-menu',
          'fallback_cb'=>'rrv_one_page_menu_fallback',
          'depth'=>1
      ]); ?>
    </nav>
    <a class="rrv-btn rrv-btn--gold rrv-header__cta" href="<?php echo esc_url(rrv_booking_url()); ?>">Book now</a>
  </div>
</header>
<main id="main-content">
