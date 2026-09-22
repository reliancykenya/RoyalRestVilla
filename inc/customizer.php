<?php
if (!defined('ABSPATH')) exit;
function rrv_customize_register($wp_customize){
    $wp_customize->add_panel('rrv_panel',['title'=>__('Royal Rest Villa','royalrestvilla'),'priority'=>25]);

    $wp_customize->add_section('rrv_brand',['title'=>__('Brand & Colours','royalrestvilla'),'panel'=>'rrv_panel']);
    $colors=['gold'=>'#c79a24','forest'=>'#26362b','background'=>'#fffdf8','mist'=>'#eef6f2','ink'=>'#171717'];
    foreach($colors as $k=>$v){
        $id='rrv_'.$k;
        $wp_customize->add_setting($id,['default'=>$v,'sanitize_callback'=>'sanitize_hex_color','transport'=>'refresh']);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,$id,['label'=>ucfirst($k),'section'=>'rrv_brand']));
    }

    $wp_customize->add_section('rrv_home',['title'=>__('Homepage','royalrestvilla'),'panel'=>'rrv_panel']);
    $fields=[
        'hero_kicker'=>['Welcome to the highlands','Hero eyebrow'],
        'hero_title'=>['Welcome to Royal Rest Villa','Hero title'],
        'hero_text'=>['A peaceful countryside retreat for rest, privacy and quiet luxury along the Nyeri–Nyahururu Road.','Hero description'],
        'hero_button'=>['Book your stay','Primary button label'],
        'about_kicker'=>['Quiet luxury, naturally','About eyebrow'],
        'about_title'=>['Thoughtfully crafted for unhurried stays','About title'],
        'about_text'=>['Modern comfort meets the calm of the countryside. Settle in, slow down and enjoy a refined stay designed around privacy, warmth and genuine rest.','About description'],
        'rooms_title'=>['Rooms designed for real rest','Rooms heading'],
        'rooms_text'=>['Warm finishes, comfortable bedding and thoughtful details create a calm place to reset.','Rooms description'],
        'features_title'=>['Everything you need, without the noise','Features heading'],
        'testimonials_title'=>['What guests remember','Testimonials heading'],
        'cta_title'=>['Your quiet escape is closer than you think','Bottom CTA heading'],
        'cta_text'=>['Check availability and reserve your stay directly with Royal Rest Villa.','Bottom CTA text'],
    ];
    foreach($fields as $k=>$cfg){
        $wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>'sanitize_textarea_field']);
        $wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_home','type'=>str_contains($k,'text')?'textarea':'text']);
    }
    $wp_customize->add_setting('rrv_hero_image',['sanitize_callback'=>'absint']);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,'rrv_hero_image',['label'=>'Hero background image','section'=>'rrv_home','mime_type'=>'image']));
    $wp_customize->add_setting('rrv_about_image',['sanitize_callback'=>'absint']);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,'rrv_about_image',['label'=>'About image','section'=>'rrv_home','mime_type'=>'image']));

    $wp_customize->add_section('rrv_contact',['title'=>__('Contact & Social','royalrestvilla'),'panel'=>'rrv_panel']);
    $contact=[
        'phone'=>['+254 700 000 000','Phone'], 'email'=>['hello@royalrestvilla.com','Email'],
        'address'=>['Wiyumiririe, Nyeri–Nyahururu Road, Kenya','Address'],
        'whatsapp'=>['254700000000','WhatsApp number'], 'instagram'=>['','Instagram URL'],
        'facebook'=>['','Facebook URL'], 'tiktok'=>['','TikTok URL'], 'maps_url'=>['','Google Maps URL']
    ];
    foreach($contact as $k=>$cfg){$wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>in_array($k,['instagram','facebook','tiktok','maps_url'],true)?'esc_url_raw':'sanitize_text_field']);$wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_contact']);}

    $wp_customize->add_section('rrv_booking',['title'=>__('Booking','royalrestvilla'),'panel'=>'rrv_panel']);
    $booking=[
      'booking_email'=>[get_option('admin_email'),'Booking notification email'],
      'booking_subject'=>['New Royal Rest Villa booking request','Email subject'],
      'checkin_time'=>['14:00','Check-in time'],
      'checkout_time'=>['10:00','Check-out time'],
      'currency'=>['KES','Currency code']
    ];
    foreach($booking as $k=>$cfg){$wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>'sanitize_text_field']);$wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_booking']);}

    $wp_customize->add_section('rrv_seo',['title'=>__('SEO & Discoverability','royalrestvilla'),'panel'=>'rrv_panel']);
    $seo=[
      'seo_default_description'=>['Royal Rest Villa is a peaceful countryside retreat in Wiyumiririe along the Nyeri–Nyahururu Road, offering comfortable short stays, privacy and quiet luxury.','Default meta description'],
      'seo_image'=>['','Default social image URL'],
      'seo_twitter'=>['','X/Twitter handle'],
      'seo_price_range'=>['$$','Schema price range'],
      'seo_lat'=>['','Latitude'],
      'seo_lng'=>['','Longitude']
    ];
    foreach($seo as $k=>$cfg){$wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>$k==='seo_image'?'esc_url_raw':'sanitize_text_field']);$wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_seo','type'=>$k==='seo_default_description'?'textarea':'text']);}
}
add_action('customize_register','rrv_customize_register');

function rrv_customizer_css(){ ?>
<style>:root{--rrv-gold:<?php echo esc_html(get_theme_mod('rrv_gold','#c79a24'));?>;--rrv-forest:<?php echo esc_html(get_theme_mod('rrv_forest','#26362b'));?>;--rrv-bg:<?php echo esc_html(get_theme_mod('rrv_background','#fffdf8'));?>;--rrv-mist:<?php echo esc_html(get_theme_mod('rrv_mist','#eef6f2'));?>;--rrv-ink:<?php echo esc_html(get_theme_mod('rrv_ink','#171717'));?>}</style>
<?php }
add_action('wp_head','rrv_customizer_css',3);
