<?php
get_header();

$hero_id=absint(get_theme_mod('rrv_hero_image'));
$hero=$hero_id?wp_get_attachment_image_url($hero_id,'full'):'';
$about_id=absint(get_theme_mod('rrv_about_image'));
$about=$about_id?wp_get_attachment_image_url($about_id,'rrv-gallery'):'';
$rooms=new WP_Query(['post_type'=>'rrv_room','posts_per_page'=>6,'post_status'=>'publish','orderby'=>'menu_order title','order'=>'ASC']);
$quotes=new WP_Query(['post_type'=>'rrv_testimonial','posts_per_page'=>3,'post_status'=>'publish','orderby'=>'menu_order date','order'=>'ASC']);
$gallery=rrv_gallery_images();
$currency=get_theme_mod('rrv_currency','KES');
?>
<section class="rrv-hero" id="home"<?php if($hero)echo ' style="--rrv-hero-image:url(\''.esc_url($hero).'\')"';?>>
  <div class="rrv-container rrv-hero__content">
    <div class="rrv-kicker"><?php echo esc_html(get_theme_mod('rrv_hero_kicker','Serenity in the countryside'));?></div>
    <h1><?php echo esc_html(get_theme_mod('rrv_hero_title','Welcome to Royal Rest Villa'));?></h1>
    <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_hero_text','A peaceful countryside retreat for rest, privacy and quiet luxury along the Nyeri–Nyahururu Road.'));?></p>
    <div class="rrv-actions">
      <a class="rrv-btn rrv-btn--gold" href="#book"><?php echo esc_html(get_theme_mod('rrv_hero_button','Book your stay'));?></a>
      <a class="rrv-btn rrv-btn--ghost" href="#rooms">Explore rooms</a>
    </div>
  </div>
  <div class="rrv-scroll-cue"><span>Scroll to unwind</span><i>↓</i></div>
</section>

<div class="rrv-container rrv-bookbar" aria-label="Quick availability form">
  <div class="rrv-bookbar__inner">
    <div class="rrv-field"><label for="rrv-quick-checkin">Check-in</label><input type="date" id="rrv-quick-checkin" min="<?php echo esc_attr(wp_date('Y-m-d'));?>"></div>
    <div class="rrv-field"><label for="rrv-quick-checkout">Check-out</label><input type="date" id="rrv-quick-checkout" min="<?php echo esc_attr(wp_date('Y-m-d',strtotime('+1 day')));?>"></div>
    <div class="rrv-field"><label for="rrv-quick-guests">Guests</label><select id="rrv-quick-guests"><option value="1">1 guest</option><option value="2" selected>2 guests</option><option value="3">3 guests</option><option value="4">4 guests</option><option value="5">5+ guests</option></select></div>
    <div class="rrv-field"><label for="rrv-quick-room">Room</label><select id="rrv-quick-room"><option value="Any available room">Any room</option><?php foreach(get_posts(['post_type'=>'rrv_room','numberposts'=>-1,'post_status'=>'publish']) as $room)echo '<option>'.esc_html($room->post_title).'</option>';?></select></div>
    <button class="rrv-btn rrv-btn--gold" type="button" data-rrv-quick-book>Check availability</button>
  </div>
</div>

<section class="rrv-section rrv-intro" id="about">
  <div class="rrv-container">
    <div class="rrv-split">
      <div class="rrv-image-frame">
        <?php if($about):?>
          <img src="<?php echo esc_url($about);?>" alt="<?php echo esc_attr(get_bloginfo('name'));?>">
        <?php else:?>
          <div class="rrv-image-placeholder" aria-label="Add the villa image in Appearance > Customize"></div>
        <?php endif;?>
        <div class="rrv-image-badge"><strong>Rest.</strong><span>Reset. Return renewed.</span></div>
      </div>
      <div>
        <div class="rrv-kicker"><?php echo esc_html(get_theme_mod('rrv_about_kicker','Quiet luxury, naturally'));?></div>
        <h2><?php echo esc_html(get_theme_mod('rrv_about_title','Thoughtfully crafted for unhurried stays'));?></h2>
        <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_about_text','Modern comfort meets the calm of the countryside. Settle in, slow down and enjoy a refined stay designed around privacy, warmth and genuine rest.'));?></p>
        <div class="rrv-checks">
          <div class="rrv-check">Quiet countryside setting</div>
          <div class="rrv-check">Warm, private rooms</div>
          <div class="rrv-check">Secure on-site parking</div>
          <div class="rrv-check">Direct local hospitality</div>
        </div>
        <a class="rrv-text-link" href="#gallery">Discover the villa <span>→</span></a>
      </div>
    </div>
  </div>
</section>

<section class="rrv-section rrv-section--mist" aria-label="Royal Rest Villa highlights">
  <div class="rrv-container">
    <div class="rrv-stats">
      <div class="rrv-stat"><strong>Quiet</strong><span>Private countryside atmosphere</span></div>
      <div class="rrv-stat"><strong>Secure</strong><span>Rest with peace of mind</span></div>
      <div class="rrv-stat"><strong>Warm</strong><span>A homely, considered stay</span></div>
      <div class="rrv-stat"><strong>Direct</strong><span>Book directly with the villa</span></div>
    </div>
  </div>
</section>

<section class="rrv-section" id="rooms">
  <div class="rrv-container">
    <div class="rrv-heading">
      <div class="rrv-kicker">Stay your way</div>
      <h2><?php echo esc_html(get_theme_mod('rrv_rooms_title','Rooms designed for real rest'));?></h2>
      <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_rooms_text','Warm finishes, comfortable bedding and thoughtful details create a calm place to reset.'));?></p>
    </div>
    <div class="rrv-grid rrv-room-grid">
      <?php if($rooms->have_posts()):while($rooms->have_posts()):$rooms->the_post();
        $price=get_post_meta(get_the_ID(),'_rrv_price',true);
        $guests=get_post_meta(get_the_ID(),'_rrv_guests',true);
        $beds=get_post_meta(get_the_ID(),'_rrv_beds',true);
        $size=get_post_meta(get_the_ID(),'_rrv_size',true);
        $badge=get_post_meta(get_the_ID(),'_rrv_badge',true);?>
        <article class="rrv-card rrv-room-card">
          <div class="rrv-card__media">
            <?php if(has_post_thumbnail())the_post_thumbnail('rrv-room',['loading'=>'lazy']);else echo '<div class="rrv-room-placeholder"></div>';?>
            <?php if($badge)echo '<span class="rrv-card__badge">'.esc_html($badge).'</span>';?>
          </div>
          <div class="rrv-card__body">
            <div class="rrv-card__meta">
              <?php if($guests)echo '<span>'.esc_html($guests).' guests</span>';?>
              <?php if($beds)echo '<span>'.esc_html($beds).' beds</span>';?>
              <?php if($size)echo '<span>'.esc_html($size).'</span>';?>
            </div>
            <h3><?php the_title();?></h3>
            <p><?php echo esc_html(rrv_trim_words(get_the_excerpt()?:get_the_content(),22));?></p>
            <div class="rrv-card__footer">
              <div class="rrv-price"><?php if($price)echo esc_html($currency.' '.$price).'<small> / night</small>';else echo '<small>Contact us for rate</small>';?></div>
              <button class="rrv-btn rrv-btn--compact" type="button" data-rrv-room="<?php echo esc_attr(get_the_title());?>">Choose room</button>
            </div>
          </div>
        </article>
      <?php endwhile;else:?>
        <div class="rrv-empty-state"><strong>Your rooms will appear here.</strong><span>Add them from WordPress Admin → Rooms.</span></div>
      <?php endif;wp_reset_postdata();?>
    </div>
  </div>
</section>

<section class="rrv-section rrv-section--forest" id="amenities">
  <div class="rrv-container">
    <div class="rrv-heading rrv-heading--light">
      <div class="rrv-kicker">Comfort without complication</div>
      <h2><?php echo esc_html(get_theme_mod('rrv_features_title','Everything you need, without the noise'));?></h2>
    </div>
    <div class="rrv-feature-grid">
      <?php
      $icons=['P','⌂','✦','✓'];
      for($i=1;$i<=4;$i++):
      ?>
      <article class="rrv-feature">
        <div class="rrv-feature__icon"><?php echo esc_html($icons[$i-1]);?></div>
        <h3><?php echo esc_html(get_theme_mod('rrv_amenity_'.$i.'_title'));?></h3>
        <p><?php echo esc_html(get_theme_mod('rrv_amenity_'.$i.'_text'));?></p>
      </article>
      <?php endfor;?>
    </div>
  </div>
</section>

<section class="rrv-section" id="gallery">
  <div class="rrv-container">
    <div class="rrv-heading">
      <div class="rrv-kicker">See the stay</div>
      <h2><?php echo esc_html(get_theme_mod('rrv_gallery_title','A closer look at Royal Rest Villa'));?></h2>
      <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_gallery_text','Explore the spaces, views and details that make every stay feel calm and considered.'));?></p>
    </div>
    <div class="rrv-gallery-grid">
      <?php if($gallery): foreach($gallery as $index=>$image):?>
        <button class="rrv-gallery-item<?php echo $index===0?' rrv-gallery-item--wide':'';?>" type="button" data-rrv-lightbox="<?php echo esc_url($image['url']);?>" aria-label="Open gallery image">
          <img src="<?php echo esc_url($image['url']);?>" alt="<?php echo esc_attr($image['alt']?:'Royal Rest Villa');?>" loading="lazy">
        </button>
      <?php endforeach; else:?>
        <?php for($i=1;$i<=6;$i++):?><div class="rrv-gallery-placeholder"><span>Gallery <?php echo esc_html($i);?></span></div><?php endfor;?>
      <?php endif;?>
    </div>
  </div>
</section>

<section class="rrv-section rrv-section--mist" id="reviews">
  <div class="rrv-container">
    <div class="rrv-heading">
      <div class="rrv-kicker">Guest stories</div>
      <h2><?php echo esc_html(get_theme_mod('rrv_testimonials_title','What guests remember'));?></h2>
    </div>
    <div class="rrv-testimonials">
      <?php if($quotes->have_posts()):while($quotes->have_posts()):$quotes->the_post();
        $rating=(int)get_post_meta(get_the_ID(),'_rrv_rating',true)?:5;
        $role=get_post_meta(get_the_ID(),'_rrv_role',true);?>
        <blockquote class="rrv-quote">
          <div class="rrv-stars"><?php echo esc_html(str_repeat('★',min(5,max(1,$rating))));?></div>
          <p><?php echo esc_html(rrv_trim_words(get_the_content(),44));?></p>
          <footer><?php the_title();?><span><?php echo esc_html($role);?></span></footer>
        </blockquote>
      <?php endwhile;else:?>
        <blockquote class="rrv-quote"><div class="rrv-stars">★★★★★</div><p>Peaceful, clean and welcoming — the kind of place that makes it easy to truly rest.</p><footer>Guest experience<span>Royal Rest Villa</span></footer></blockquote>
        <blockquote class="rrv-quote"><div class="rrv-stars">★★★★★</div><p>Quiet surroundings, thoughtful spaces and the feeling of having somewhere genuinely restful to return to.</p><footer>Guest experience<span>Central Kenya</span></footer></blockquote>
        <blockquote class="rrv-quote"><div class="rrv-stars">★★★★★</div><p>A comfortable countryside stay with the privacy and calm you want after a long day.</p><footer>Guest experience<span>Royal Rest Villa</span></footer></blockquote>
      <?php endif;wp_reset_postdata();?>
    </div>
  </div>
</section>

<section class="rrv-section rrv-book-section" id="book">
  <div class="rrv-container">
    <div class="rrv-booking-page">
      <div class="rrv-book-copy">
        <div class="rrv-kicker">Book direct</div>
        <h2><?php echo esc_html(get_theme_mod('rrv_booking_title','Reserve your stay'));?></h2>
        <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_booking_text','Send your preferred dates and room. We will confirm availability directly with you.'));?></p>
        <div class="rrv-book-points">
          <div><strong>Check-in</strong><span><?php echo esc_html(get_theme_mod('rrv_checkin_time','14:00'));?></span></div>
          <div><strong>Check-out</strong><span><?php echo esc_html(get_theme_mod('rrv_checkout_time','10:00'));?></span></div>
          <div><strong>Direct support</strong><span>Phone & WhatsApp</span></div>
        </div>
      </div>
      <div><?php echo do_shortcode('[rrv_booking_form]');?></div>
    </div>
  </div>
</section>

<section class="rrv-section" id="contact">
  <div class="rrv-container">
    <div class="rrv-contact-panel">
      <div class="rrv-contact-copy">
        <div class="rrv-kicker">Wiyumiririe · Central Kenya</div>
        <h2><?php echo esc_html(get_theme_mod('rrv_contact_title','Find your way to quiet'));?></h2>
        <p class="rrv-lead"><?php echo esc_html(get_theme_mod('rrv_contact_text','Royal Rest Villa is located in Wiyumiririe along the Nyeri–Nyahururu Road, within easy reach of Central Kenya destinations.'));?></p>
      </div>
      <div class="rrv-contact-cards">
        <a class="rrv-contact-card" href="tel:<?php echo esc_attr(rrv_phone_href(get_theme_mod('rrv_phone')));?>"><span>Call</span><strong><?php echo esc_html(get_theme_mod('rrv_phone','+254 700 000 000'));?></strong></a>
        <a class="rrv-contact-card" href="mailto:<?php echo esc_attr(get_theme_mod('rrv_email'));?>"><span>Email</span><strong><?php echo esc_html(get_theme_mod('rrv_email','hello@royalrestvilla.com'));?></strong></a>
        <?php $maps=get_theme_mod('rrv_maps_url');?>
        <a class="rrv-contact-card" <?php if($maps)echo 'target="_blank" rel="noopener" href="'.esc_url($maps).'"';?>><span>Location</span><strong><?php echo esc_html(get_theme_mod('rrv_address','Wiyumiririe, Nyeri–Nyahururu Road, Kenya'));?></strong></a>
      </div>
    </div>
  </div>
</section>

<div class="rrv-lightbox" aria-hidden="true">
  <button class="rrv-lightbox__close" type="button" aria-label="Close image">×</button>
  <img src="" alt="Royal Rest Villa gallery preview">
</div>

<?php get_footer(); ?>
