<?php
if (!defined('ABSPATH')) exit;

function rrv_seo_description(){
    if(is_singular()){
        $custom=get_post_meta(get_queried_object_id(),'_rrv_seo_description',true);
        if($custom)return $custom;
        if(has_excerpt())return wp_strip_all_tags(get_the_excerpt());
    }
    return get_theme_mod('rrv_seo_default_description','Royal Rest Villa is a peaceful countryside retreat in Wiyumiririe along the Nyeri–Nyahururu Road, offering comfortable short stays, privacy and quiet luxury.');
}

function rrv_seo_image(){
    if(is_singular()&&has_post_thumbnail()) return get_the_post_thumbnail_url(get_queried_object_id(),'full');
    $custom=get_theme_mod('rrv_seo_image');
    if($custom)return $custom;
    $hero=absint(get_theme_mod('rrv_hero_image'));
    if($hero)return wp_get_attachment_image_url($hero,'full');
    $logo=absint(get_theme_mod('custom_logo'));
    return $logo?wp_get_attachment_image_url($logo,'full'):'';
}

function rrv_schema_graph(){
    $home=home_url('/');
    $description=rrv_seo_description();
    $image=rrv_seo_image();
    $phone=get_theme_mod('rrv_phone');
    $email=get_theme_mod('rrv_email');
    $address=get_theme_mod('rrv_address','Wiyumiririe, Nyeri–Nyahururu Road, Kenya');
    $same_as=array_values(array_filter([
        get_theme_mod('rrv_instagram'),
        get_theme_mod('rrv_facebook'),
        get_theme_mod('rrv_tiktok')
    ]));

    $hotel=[
        '@type'=>['Hotel','LodgingBusiness'],
        '@id'=>$home.'#hotel',
        'name'=>get_bloginfo('name'),
        'url'=>$home,
        'description'=>$description,
        'telephone'=>$phone,
        'email'=>$email,
        'priceRange'=>get_theme_mod('rrv_seo_price_range','$$'),
        'address'=>[
            '@type'=>'PostalAddress',
            'streetAddress'=>$address,
            'addressCountry'=>'KE'
        ],
        'amenityFeature'=>[
            ['@type'=>'LocationFeatureSpecification','name'=>'Secure parking','value'=>true],
            ['@type'=>'LocationFeatureSpecification','name'=>'Smart kitchen','value'=>true],
            ['@type'=>'LocationFeatureSpecification','name'=>'Quiet countryside setting','value'=>true],
            ['@type'=>'LocationFeatureSpecification','name'=>'Security','value'=>true]
        ]
    ];
    if($image)$hotel['image']=$image;
    if($same_as)$hotel['sameAs']=$same_as;
    if(get_theme_mod('rrv_maps_url'))$hotel['hasMap']=get_theme_mod('rrv_maps_url');
    $lat=get_theme_mod('rrv_seo_lat');$lng=get_theme_mod('rrv_seo_lng');
    if($lat!==''&&$lng!=='')$hotel['geo']=['@type'=>'GeoCoordinates','latitude'=>(float)$lat,'longitude'=>(float)$lng];

    $room_nodes=[];
    $rooms=get_posts(['post_type'=>'rrv_room','numberposts'=>20,'post_status'=>'publish','orderby'=>'menu_order title','order'=>'ASC']);
    foreach($rooms as $room){
        $node=[
            '@type'=>'HotelRoom',
            '@id'=>$home.'#room-'.$room->ID,
            'name'=>$room->post_title,
            'description'=>rrv_trim_words($room->post_excerpt?:$room->post_content,45),
            'containedInPlace'=>['@id'=>$home.'#hotel']
        ];
        $guests=get_post_meta($room->ID,'_rrv_guests',true);
        if($guests)$node['occupancy']=['@type'=>'QuantitativeValue','maxValue'=>(int)$guests];
        $price=get_post_meta($room->ID,'_rrv_price',true);
        if($price)$node['offers']=[
            '@type'=>'Offer',
            'price'=>$price,
            'priceCurrency'=>get_theme_mod('rrv_currency','KES'),
            'url'=>$home.'#book',
            'availability'=>'https://schema.org/LimitedAvailability'
        ];
        $thumb=get_the_post_thumbnail_url($room->ID,'full');
        if($thumb)$node['image']=$thumb;
        $room_nodes[]=$node;
    }

    $graph=[
        [
            '@type'=>'WebSite',
            '@id'=>$home.'#website',
            'url'=>$home,
            'name'=>get_bloginfo('name'),
            'description'=>$description,
            'publisher'=>['@id'=>$home.'#hotel'],
            'inLanguage'=>get_bloginfo('language')
        ],
        [
            '@type'=>'WebPage',
            '@id'=>$home.'#webpage',
            'url'=>$home,
            'name'=>wp_get_document_title(),
            'description'=>$description,
            'isPartOf'=>['@id'=>$home.'#website'],
            'about'=>['@id'=>$home.'#hotel'],
            'inLanguage'=>get_bloginfo('language')
        ],
        $hotel
    ];
    return array_merge($graph,$room_nodes);
}

function rrv_seo_output(){
    if(is_admin())return;
    $description=rrv_seo_description();
    $canonical=is_front_page()?home_url('/'):(is_singular()?get_permalink():home_url('/'));
    $title=wp_get_document_title();
    $image=rrv_seo_image();

    echo "\n<!-- Royal Rest Villa native SEO -->\n";
    echo '<meta name="description" content="'.esc_attr($description).'">'."\n";
    echo '<link rel="canonical" href="'.esc_url($canonical).'">'."\n";
    echo '<meta property="og:site_name" content="'.esc_attr(get_bloginfo('name')).'">'."\n";
    echo '<meta property="og:type" content="'.(is_singular('post')?'article':'website').'">'."\n";
    echo '<meta property="og:title" content="'.esc_attr($title).'">'."\n";
    echo '<meta property="og:description" content="'.esc_attr($description).'">'."\n";
    echo '<meta property="og:url" content="'.esc_url($canonical).'">'."\n";
    if($image)echo '<meta property="og:image" content="'.esc_url($image).'">'."\n";
    echo '<meta name="twitter:card" content="summary_large_image">'."\n";
    echo '<meta name="twitter:title" content="'.esc_attr($title).'">'."\n";
    echo '<meta name="twitter:description" content="'.esc_attr($description).'">'."\n";
    if($image)echo '<meta name="twitter:image" content="'.esc_url($image).'">'."\n";

    $json=['@context'=>'https://schema.org','@graph'=>rrv_schema_graph()];
    echo '<script type="application/ld+json">'.wp_json_encode($json,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}
add_action('wp_head','rrv_seo_output',4);

function rrv_seo_box($post){
    wp_nonce_field('rrv_seo_save','rrv_seo_nonce');
    $title=get_post_meta($post->ID,'_rrv_seo_title',true);
    $description=get_post_meta($post->ID,'_rrv_seo_description',true);
    $noindex=get_post_meta($post->ID,'_rrv_noindex',true);
    echo '<p><label><strong>SEO title</strong></label><input class="widefat" name="rrv_seo_title" value="'.esc_attr($title).'" maxlength="70"></p>';
    echo '<p><label><strong>Meta description</strong></label><textarea class="widefat" rows="3" name="rrv_seo_description" maxlength="180">'.esc_textarea($description).'</textarea></p>';
    echo '<p><label><input type="checkbox" name="rrv_noindex" value="1" '.checked($noindex,'1',false).'> Do not index this item</label></p>';
}
function rrv_add_seo_boxes(){
    foreach(['post','page','rrv_room'] as $type)add_meta_box('rrv_seo','Royal Rest SEO','rrv_seo_box',$type,'normal','low');
}
add_action('add_meta_boxes','rrv_add_seo_boxes');

function rrv_save_seo_fields($post_id){
    if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;
    if(empty($_POST['rrv_seo_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rrv_seo_nonce'])),'rrv_seo_save'))return;
    if(isset($_POST['rrv_seo_title']))update_post_meta($post_id,'_rrv_seo_title',sanitize_text_field(wp_unslash($_POST['rrv_seo_title'])));
    if(isset($_POST['rrv_seo_description']))update_post_meta($post_id,'_rrv_seo_description',sanitize_textarea_field(wp_unslash($_POST['rrv_seo_description'])));
    update_post_meta($post_id,'_rrv_noindex',isset($_POST['rrv_noindex'])?'1':'0');
}
add_action('save_post','rrv_save_seo_fields');

add_filter('pre_get_document_title',function($title){
    if(is_front_page())return get_bloginfo('name').' | Serenity in the Countryside';
    if(is_singular()){
        $custom=get_post_meta(get_queried_object_id(),'_rrv_seo_title',true);
        if($custom)return $custom;
    }
    return $title;
});

add_filter('wp_robots',function($robots){
    $robots['max-image-preview']='large';
    if(is_singular()&&get_post_meta(get_queried_object_id(),'_rrv_noindex',true)==='1'){
        $robots['noindex']=true;
        $robots['follow']=true;
    }
    return $robots;
});

add_filter('robots_txt',function($output,$public){
    return "User-agent: *\nAllow: /\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\nSitemap: ".home_url('/wp-sitemap.xml')."\n";
},10,2);

function rrv_llms_txt(){
    $path=parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH);
    if(rtrim($path,'/')!=='/llms.txt')return;
    $rooms=get_posts(['post_type'=>'rrv_room','numberposts'=>20,'post_status'=>'publish','orderby'=>'menu_order title','order'=>'ASC']);
    header('Content-Type: text/plain; charset=utf-8');
    echo "# ".get_bloginfo('name')."\n\n";
    echo rrv_seo_description()."\n\n";
    echo "## Canonical website\n".home_url('/')."\n\n";
    echo "## Location\n".get_theme_mod('rrv_address','Wiyumiririe, Nyeri–Nyahururu Road, Kenya')."\n\n";
    echo "## Contact\nPhone: ".get_theme_mod('rrv_phone')."\nEmail: ".get_theme_mod('rrv_email')."\n\n";
    if($rooms){
        echo "## Rooms\n";
        foreach($rooms as $room){
            $price=get_post_meta($room->ID,'_rrv_price',true);
            echo "- ".$room->post_title;
            if($price)echo " — ".get_theme_mod('rrv_currency','KES')." ".$price." per night";
            echo "\n";
        }
    }
    echo "\n## Booking\nDirect booking requests: ".home_url('/#book')."\n";
    exit;
}
add_action('template_redirect','rrv_llms_txt',0);
