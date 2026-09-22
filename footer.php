</main>
<footer class="rrv-footer">
  <div class="rrv-container rrv-footer__grid">
    <div>
      <?php if(has_custom_logo())the_custom_logo();else echo '<h3>'.esc_html(get_bloginfo('name')).'</h3>'; ?>
      <p><?php echo esc_html(get_theme_mod('rrv_seo_default_description')); ?></p>
    </div>
    <div>
      <h3>Explore</h3>
      <ul>
        <li><a href="<?php echo esc_url(rrv_home_anchor('about')); ?>">The Villa</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('rooms')); ?>">Rooms</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('gallery')); ?>">Gallery</a></li>
        <li><a href="<?php echo esc_url(rrv_home_anchor('book')); ?>">Book your stay</a></li>
      </ul>
    </div>
    <div>
      <h3>Say hello</h3>
      <p><?php echo esc_html(get_theme_mod('rrv_address','Wiyumiririe, Nyeri–Nyahururu Road, Kenya')); ?></p>
      <p>
        <a href="tel:<?php echo esc_attr(rrv_phone_href(get_theme_mod('rrv_phone'))); ?>"><?php echo esc_html(get_theme_mod('rrv_phone')); ?></a><br>
        <a href="mailto:<?php echo esc_attr(get_theme_mod('rrv_email')); ?>"><?php echo esc_html(get_theme_mod('rrv_email')); ?></a>
      </p>
      <div class="rrv-socials">
        <?php foreach(['instagram'=>'Instagram','facebook'=>'Facebook','tiktok'=>'TikTok'] as $k=>$label){$u=get_theme_mod('rrv_'.$k);if($u)echo '<a target="_blank" rel="noopener" href="'.esc_url($u).'">'.esc_html($label).'</a>';} ?>
      </div>
    </div>
  </div>
  <div class="rrv-container rrv-footer__bottom">
    <div>© <?php echo esc_html(wp_date('Y')); ?> Royal Rest Villa. All rights reserved.</div>
    <a href="#home">Back to top ↑</a>
  </div>
</footer>
<?php $wa=get_theme_mod('rrv_whatsapp'); if($wa): ?>
<a class="rrv-whatsapp-float" target="_blank" rel="noopener" href="https://wa.me/<?php echo esc_attr(rrv_whatsapp_href($wa)); ?>" aria-label="Chat with Royal Rest Villa on WhatsApp">WhatsApp</a>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
