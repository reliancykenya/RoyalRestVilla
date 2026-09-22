<?php get_header();
$hero_id=get_theme_mod('rrv_hero_image');$hero=$hero_id?wp_get_attachment_image_url($hero_id,'full'):'';
$about_id=get_theme_mod('rrv_about_image');$about=$about_id?wp_get_attachment_image_url($about_id,'rrv-gallery'):'';
$rooms=new WP_Query(['post_type'=>'rrv_room','posts_per_page'=>6,'post_status'=>'publish','orderby'=>'menu_order title','order'=>'ASC']);
$quotes=new WP_Query(['post_type'=>'rrv_testimonial','posts_per_page'=>3,'post_status'=>'publish','orderby'=>'menu_order date','order'=>'ASC']);
?>
<section class="rrv-hero"<?php if($hero)echo ' style="--rrv-hero-image:url(\''.esc_url($hero).'\')"';?>>
  <div class="rrv-container rrv-hero__content">
    <div class="rrv-kicker"><?php echo esc_html(get_theme_mod('rrv_hero_kicker','Welcome to the highlands'));?></div>
    <h1><?php echo esc_html(get_theme_mod('rrv_hero_title','Welcome to Royal Rest Villa'));?></h1>
    <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_hero_text'));?></p>
    <div class="rrv-actions">
      <a class="rrv-btn rrv-btn--gold" href="<?php echo esc_url(rrv_booking_url());?>"><?php echo esc_html(get_theme_mod('rrv_hero_button','Book your stay'));?></a>
      <a class="rrv-btn rrv-btn--ghost" href="#rooms">Explore rooms</a>
    </div>
  </div>
</section>

<div class="rrv-container rrv-bookbar">
<form class="rrv-bookbar__inner" action="<?php echo esc_url(rrv_booking_url());?>" method="get">
  <div class="rrv-field"><label>Check-in</label><input type="date" name="checkin" min="<?php echo esc_attr(wp_date('Y-m-d'));?>"></div>
  <div class="rrv-field"><label>Check-out</label><input type="date" name="checkout"></div>
  <div class="rrv-field"><label>Guests</label><select name="guests"><option>1</option><option selected>2</option><option>3</option><option>4</option><option>5+</option></select></div>
  <div class="rrv-field"><label>Room</label><select name="room"><option>Any room</option><?php foreach(get_posts(['post_type'=>'rrv_room','numberposts'=>-1]) as $room)echo '<option>'.esc_html($room->post_title).'</option>';?></select></div>
  <button class="rrv-btn rrv-btn--gold">Check availability</button>
</form>
</div>

<section class="rrv-section" id="about"><div class="rrv-container"><div class="rrv-split">
  <div class="rrv-image-frame"><?php if($about)echo '<img src="'.esc_url($about).'" alt="Royal Rest Villa">';else echo '<div style="aspect-ratio:4/3;border-radius:30px;background:linear-gradient(135deg,#d8c89a,#26362b)"></div>';?></div>
  <div>
    <div class="rrv-kicker"><?php echo esc_html(get_theme_mod('rrv_about_kicker'));?></div>
    <h2><?php echo esc_html(get_theme_mod('rrv_about_title'));?></h2>
    <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_about_text'));?></p>
    <div class="rrv-checks"><div class="rrv-check">Secure parking</div><div class="rrv-check">Quiet countryside</div><div class="rrv-check">Smart kitchen</div><div class="rrv-check">Warm hospitality</div></div>
    <a class="rrv-btn" href="<?php echo esc_url(rrv_booking_url());?>">Reserve your stay</a>
  </div>
</div></div></section>

<section class="rrv-section rrv-section--mist"><div class="rrv-container">
<div class="rrv-stats">
  <div class="rrv-stat"><strong>Quiet</strong><span>Private countryside setting</span></div>
  <div class="rrv-stat"><strong>Secure</strong><span>Comfort with peace of mind</span></div>
  <div class="rrv-stat"><strong>Warm</strong><span>Thoughtful, homely details</span></div>
  <div class="rrv-stat"><strong>Direct</strong><span>Book with the villa</span></div>
</div>
</div></section>

<section class="rrv-section" id="rooms"><div class="rrv-container">
  <div class="rrv-heading"><div class="rrv-kicker">Stay your way</div><h2><?php echo esc_html(get_theme_mod('rrv_rooms_title'));?></h2><p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_rooms_text'));?></p></div>
  <div class="rrv-grid">
  <?php if($rooms->have_posts()):while($rooms->have_posts()):$rooms->the_post();
    $price=get_post_meta(get_the_ID(),'_rrv_price',true);$guests=get_post_meta(get_the_ID(),'_rrv_guests',true);$beds=get_post_meta(get_the_ID(),'_rrv_beds',true);$badge=get_post_meta(get_the_ID(),'_rrv_badge',true);?>
    <article class="rrv-card">
      <a class="rrv-card__media" href="<?php the_permalink();?>"><?php if(has_post_thumbnail())the_post_thumbnail('rrv-room');if($badge)echo '<span class="rrv-card__badge">'.esc_html($badge).'</span>';?></a>
      <div class="rrv-card__body">
        <div class="rrv-card__meta"><?php if($guests)echo '<span>👤 '.esc_html($guests).' guests</span>';if($beds)echo '<span>🛏 '.esc_html($beds).' beds</span>';?></div>
        <h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
        <p><?php echo esc_html(get_the_excerpt());?></p>
        <div class="rrv-card__footer"><div class="rrv-price"><?php if($price)echo esc_html(get_theme_mod('rrv_currency','KES').' '.$price).'<small> / night</small>';?></div><a class="rrv-btn" href="<?php echo esc_url(rrv_booking_url().'?room='.rawurlencode(get_the_title()));?>">Book</a></div>
      </div>
    </article>
  <?php endwhile;else:?><p>Add rooms under <strong>Rooms → Add New</strong> in WordPress admin.</p><?php endif;wp_reset_postdata();?>
  </div>
</div></section>

<section class="rrv-section rrv-section--mist"><div class="rrv-container">
  <div class="rrv-heading"><div class="rrv-kicker">Made for easy stays</div><h2><?php echo esc_html(get_theme_mod('rrv_features_title'));?></h2></div>
  <div class="rrv-feature-grid">
    <div class="rrv-feature"><div class="rrv-feature__icon">P</div><h3>Secure parking</h3><p>Convenient on-site parking within the villa compound.</p></div>
    <div class="rrv-feature"><div class="rrv-feature__icon">⌂</div><h3>Smart kitchen</h3><p>A practical, well-equipped space for flexible stays.</p></div>
    <div class="rrv-feature"><div class="rrv-feature__icon">❄</div><h3>Comfort</h3><p>Thoughtful rooms built around calm, sleep and privacy.</p></div>
    <div class="rrv-feature"><div class="rrv-feature__icon">✓</div><h3>Security</h3><p>A peaceful environment so you can switch off and relax.</p></div>
  </div>
</div></section>

<section class="rrv-section"><div class="rrv-container">
  <div class="rrv-heading"><div class="rrv-kicker">Guest stories</div><h2><?php echo esc_html(get_theme_mod('rrv_testimonials_title'));?></h2></div>
  <div class="rrv-testimonials">
  <?php if($quotes->have_posts()):while($quotes->have_posts()):$quotes->the_post();$rating=(int)get_post_meta(get_the_ID(),'_rrv_rating',true)?:5;$role=get_post_meta(get_the_ID(),'_rrv_role',true);?>
    <blockquote class="rrv-quote"><div class="rrv-stars"><?php echo esc_html(str_repeat('★',$rating));?></div><p><?php echo wp_kses_post(get_the_content());?></p><footer><?php the_title();?><span><?php echo esc_html($role);?></span></footer></blockquote>
  <?php endwhile;else:?><blockquote class="rrv-quote"><div class="rrv-stars">★★★★★</div><p>Peaceful, clean and welcoming — the kind of place that makes it easy to truly rest.</p><footer>Guest experience<span>Royal Rest Villa</span></footer></blockquote><?php endif;wp_reset_postdata();?>
  </div>
</div></section>

<section class="rrv-section"><div class="rrv-container"><div class="rrv-cta">
  <div><div class="rrv-kicker">Plan your stay</div><h2><?php echo esc_html(get_theme_mod('rrv_cta_title'));?></h2><p><?php echo esc_html(get_theme_mod('rrv_cta_text'));?></p></div>
  <div class="rrv-actions"><a class="rrv-btn rrv-btn--gold" href="<?php echo esc_url(rrv_booking_url());?>">Book now</a><?php $wa=get_theme_mod('rrv_whatsapp');if($wa)echo '<a class="rrv-btn rrv-btn--light" target="_blank" rel="noopener" href="https://wa.me/'.esc_attr(rrv_whatsapp_href($wa)).'">WhatsApp us</a>';?></div>
</div></div></section>
<?php get_footer(); ?>