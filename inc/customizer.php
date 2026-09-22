<?php
if (!defined('ABSPATH')) exit;

function rrv_customize_register($wp_customize){
    $wp_customize->add_panel('rrv_panel',['title'=>__('Royal Rest Villa','royalrestvilla'),'priority'=>25]);

    $wp_customize->add_section('rrv_brand',['title'=>__('Brand & Colours','royalrestvilla'),'panel'=>'rrv_panel']);
    $colors=['gold'=>'#c79a24','forest'=>'#26362b','background'=>'#fffdf8','mist'=>'#eef6f2','ink'=>'#171717'];
    foreach($colors as $k=>$v){
        $id='rrv_'.$k;
        $wp_customize->add_setting($id,['default'=>$v,'sanitize_callback'=>'sanitize_hex_color']);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,$id,['label'=>ucfirst($k),'section'=>'rrv_brand']));
    }

    $wp_customize->add_section('rrv_home',['title'=>__('One Page Content','royalrestvilla'),'panel'=>'rrv_panel']);
    $fields=[
        'hero_kicker'=>['Serenity in the countryside','Hero eyebrow'],
        'hero_title'=>['Welcome to Royal Rest Villa','Hero title'],
        'hero_text'=>['A peaceful countryside retreat for rest, privacy and quiet luxury along the Nyeri–Nyahururu Road.','Hero description'],
        'hero_button'=>['Book your stay','Hero button'],
        'about_kicker'=>['Quiet luxury, naturally','Villa eyebrow'],
        'about_title'=>['Thoughtfully crafted for unhurried stays','Villa heading'],
        'about_text'=>['Modern comfort meets the calm of the countryside. Settle in, slow down and enjoy a refined stay designed around privacy, warmth and genuine rest.','Villa text'],
        'rooms_title'=>['Rooms designed for real rest','Rooms heading'],
        'rooms_text'=>['Warm finishes, comfortable bedding and thoughtful details create a calm place to reset.','Rooms description'],
        'features_title'=>['Everything you need, without the noise','Amenities heading'],
        'gallery_title'=>['A closer look at Royal Rest Villa','Gallery heading'],
        'gallery_text'=>['Explore the spaces, views and details that make every stay feel calm and considered.','Gallery description'],
        'testimonials_title'=>['What guests remember','Reviews heading'],
        'booking_title'=>['Reserve your stay','Booking heading'],
        'booking_text'=>['Send your preferred dates and room. We will confirm availability directly with you.','Booking description'],
        'contact_title'=>['Find your way to quiet','Contact heading'],
        'contact_text'=>['Royal Rest Villa is located in Wiyumiririe along the Nyeri–Nyahururu Road, within easy reach of Central Kenya destinations.','Contact description']
    ];
    foreach($fields as $k=>$cfg){
        $wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>'sanitize_textarea_field']);
        $wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_home','type'=>str_contains($k,'text')?'textarea':'text']);
    }
    foreach(['hero_image'=>'Hero background image','about_image'=>'Villa / About image'] as $k=>$label){
        $wp_customize->add_setting('rrv_'.$k,['sanitize_callback'=>'absint']);
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,'rrv_'.$k,['label'=>$label,'section'=>'rrv_home','mime_type'=>'image']));
    }

    $wp_customize->add_section('rrv_amenities',['title'=>__('Amenities','royalrestvilla'),'panel'=>'rrv_panel']);
    $amenities=[
        1=>['Secure Parking','Convenient on-site parking within the villa compound.'],
        2=>['Smart Kitchen','A practical, well-equipped kitchen for flexible stays.'],
        3=>['Comfortable Rooms','Thoughtful rooms designed around calm, sleep and privacy.'],
        4=>['Secure Environment','A peaceful setting where guests can relax with confidence.']
    ];
    foreach($amenities as $i=>$defaults){
        $wp_customize->add_setting('rrv_amenity_'.$i.'_title',['default'=>$defaults[0],'sanitize_callback'=>'sanitize_text_field']);
        $wp_customize->add_control('rrv_amenity_'.$i.'_title',['label'=>"Amenity {$i} title",'section'=>'rrv_amenities']);
        $wp_customize->add_setting('rrv_amenity_'.$i.'_text',['default'=>$defaults[1],'sanitize_callback'=>'sanitize_textarea_field']);
        $wp_customize->add_control('rrv_amenity_'.$i.'_text',['label'=>"Amenity {$i} description",'section'=>'rrv_amenities','type'=>'textarea']);
    }

    $wp_customize->add_section('rrv_gallery',['title'=>__('Gallery','royalrestvilla'),'panel'=>'rrv_panel']);
    for($i=1;$i<=6;$i++){
        $wp_customize->add_setting('rrv_gallery_'.$i,['sanitize_callback'=>'absint']);
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,'rrv_gallery_'.$i,['label'=>"Gallery image {$i}",'section'=>'rrv_gallery','mime_type'=>'image']));
    }

    $wp_customize->add_section('rrv_contact',['title'=>__('Contact & Social','royalrestvilla'),'panel'=>'rrv_panel']);
    $contact=[
        'phone'=>['+254 700 000 000','Phone','text'],
        'email'=>['hello@royalrestvilla.com','Email','email'],
        'address'=>['Wiyumiririe, Nyeri–Nyahururu Road, Kenya','Address','text'],
        'whatsapp'=>['254700000000','WhatsApp number','text'],
        'maps_url'=>['','Google Maps URL','url'],
        'instagram'=>['','Instagram URL','url'],
        'facebook'=>['','Facebook URL','url'],
        'tiktok'=>['','TikTok URL','url']
    ];
    foreach($contact as $k=>$cfg){
        $sanitize=$cfg[2]==='url'?'esc_url_raw':($cfg[2]==='email'?'sanitize_email':'sanitize_text_field');
        $wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>$sanitize]);
        $wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_contact','type'=>$cfg[2]]);
    }

    $wp_customize->add_section('rrv_booking',['title'=>__('Booking','royalrestvilla'),'panel'=>'rrv_panel']);
    $booking=[
      'booking_email'=>[get_option('admin_email'),'Booking notification email'],
      'booking_subject'=>['New Royal Rest Villa booking request','Notification subject'],
      'checkin_time'=>['14:00','Check-in time'],
      'checkout_time'=>['10:00','Check-out time'],
      'currency'=>['KES','Currency code'],
      'booking_note'=>['Submitting a request does not constitute a confirmed reservation. We will contact you to confirm availability.','Booking note']
    ];
    foreach($booking as $k=>$cfg){
        $wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>$k==='booking_note'?'sanitize_textarea_field':'sanitize_text_field']);
        $wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_booking','type'=>$k==='booking_note'?'textarea':'text']);
    }

    $wp_customize->add_section('rrv_seo',['title'=>__('SEO & AI Discoverability','royalrestvilla'),'panel'=>'rrv_panel']);
    $seo=[
      'seo_default_description'=>['Royal Rest Villa is a peaceful countryside retreat in Wiyumiririe along the Nyeri–Nyahururu Road, offering comfortable short stays, privacy and quiet luxury.','Default meta description','textarea'],
      'seo_image'=>['','Default social image URL','url'],
      'seo_price_range'=>['$$','Schema price range','text'],
      'seo_lat'=>['','Latitude','text'],
      'seo_lng'=>['','Longitude','text']
    ];
    foreach($seo as $k=>$cfg){
        $wp_customize->add_setting('rrv_'.$k,['default'=>$cfg[0],'sanitize_callback'=>$cfg[2]==='url'?'esc_url_raw':($cfg[2]==='textarea'?'sanitize_textarea_field':'sanitize_text_field')]);
        $wp_customize->add_control('rrv_'.$k,['label'=>$cfg[1],'section'=>'rrv_seo','type'=>$cfg[2]]);
    }
}
add_action('customize_register','rrv_customize_register');

function rrv_customizer_css(){ ?>
<style>:root{--rrv-gold:<?php echo esc_html(get_theme_mod('rrv_gold','#c79a24'));?>;--rrv-forest:<?php echo esc_html(get_theme_mod('rrv_forest','#26362b'));?>;--rrv-bg:<?php echo esc_html(get_theme_mod('rrv_background','#fffdf8'));?>;--rrv-mist:<?php echo esc_html(get_theme_mod('rrv_mist','#eef6f2'));?>;--rrv-ink:<?php echo esc_html(get_theme_mod('rrv_ink','#171717'));?>}</style>
<?php }
add_action('wp_head','rrv_customizer_css',3);
