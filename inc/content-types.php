<?php
if (!defined('ABSPATH')) exit;

function rrv_register_content(){
    register_post_type('rrv_room',[
        'labels'=>['name'=>'Rooms','singular_name'=>'Room','add_new_item'=>'Add New Room','edit_item'=>'Edit Room'],
        'public'=>false,
        'show_ui'=>true,
        'show_in_rest'=>true,
        'publicly_queryable'=>false,
        'exclude_from_search'=>true,
        'menu_icon'=>'dashicons-bed',
        'supports'=>['title','editor','excerpt','thumbnail','page-attributes']
    ]);
    register_post_type('rrv_testimonial',[
        'labels'=>['name'=>'Testimonials','singular_name'=>'Testimonial'],
        'public'=>false,'show_ui'=>true,'show_in_rest'=>true,'menu_icon'=>'dashicons-format-quote',
        'supports'=>['title','editor','thumbnail','page-attributes']
    ]);
    register_post_type('rrv_booking',[
        'labels'=>['name'=>'Bookings','singular_name'=>'Booking'],
        'public'=>false,'show_ui'=>true,'show_in_rest'=>false,'menu_icon'=>'dashicons-calendar-alt',
        'supports'=>['title']
    ]);
}
add_action('init','rrv_register_content');

function rrv_meta_boxes(){
    add_meta_box('rrv_room_details','Room Details','rrv_room_meta_box','rrv_room','normal','high');
    add_meta_box('rrv_testimonial_details','Guest Details','rrv_testimonial_meta_box','rrv_testimonial','side','default');
    add_meta_box('rrv_booking_details','Booking Details','rrv_booking_meta_box','rrv_booking','normal','high');
}
add_action('add_meta_boxes','rrv_meta_boxes');

function rrv_room_meta_box($post){
    wp_nonce_field('rrv_room_meta','rrv_room_nonce');
    $fields=['price'=>'Nightly price','guests'=>'Max guests','beds'=>'Beds','size'=>'Room size','badge'=>'Badge','amenities'=>'Amenities (comma separated)'];
    echo '<p>Room content is displayed on the one-page homepage. Use the featured image as the room photo and the excerpt as the card description.</p><table class="form-table">';
    foreach($fields as $k=>$label){
        $v=get_post_meta($post->ID,'_rrv_'.$k,true);
        echo '<tr><th><label for="rrv_'.$k.'">'.esc_html($label).'</label></th><td><input class="regular-text" id="rrv_'.$k.'" name="rrv_'.$k.'" value="'.esc_attr($v).'" /></td></tr>';
    }
    echo '</table>';
}
function rrv_testimonial_meta_box($post){
    wp_nonce_field('rrv_testimonial_meta','rrv_testimonial_nonce');
    $role=get_post_meta($post->ID,'_rrv_role',true);
    $rating=get_post_meta($post->ID,'_rrv_rating',true)?:5;
    echo '<p><label>Guest note/location</label><input class="widefat" name="rrv_role" value="'.esc_attr($role).'" /></p><p><label>Rating (1–5)</label><input class="widefat" type="number" min="1" max="5" name="rrv_rating" value="'.esc_attr($rating).'" /></p>';
}
function rrv_booking_meta_box($post){
    $keys=['reference','name','email','phone','room','checkin','checkout','guests','message'];
    echo '<table class="widefat striped"><tbody>';
    foreach($keys as $k){
        $v=get_post_meta($post->ID,'_rrv_'.$k,true);
        echo '<tr><th style="width:180px">'.esc_html(ucwords(str_replace('_',' ',$k))).'</th><td>'.esc_html($v).'</td></tr>';
    }
    echo '</tbody></table><p><label><strong>Status</strong></label> <select name="rrv_booking_status">';
    $current=get_post_meta($post->ID,'_rrv_status',true)?:'pending';
    foreach(['pending','confirmed','checked-in','completed','cancelled'] as $status)echo '<option value="'.esc_attr($status).'" '.selected($current,$status,false).'>'.esc_html(ucfirst($status)).'</option>';
    echo '</select></p>';
}

function rrv_save_meta($post_id){
    if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;
    if(get_post_type($post_id)==='rrv_room'&&isset($_POST['rrv_room_nonce'])&&wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rrv_room_nonce'])),'rrv_room_meta')){
        foreach(['price','guests','beds','size','badge','amenities'] as $k){
            if(isset($_POST['rrv_'.$k]))update_post_meta($post_id,'_rrv_'.$k,sanitize_text_field(wp_unslash($_POST['rrv_'.$k])));
        }
    }
    if(get_post_type($post_id)==='rrv_testimonial'&&isset($_POST['rrv_testimonial_nonce'])&&wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rrv_testimonial_nonce'])),'rrv_testimonial_meta')){
        update_post_meta($post_id,'_rrv_role',sanitize_text_field(wp_unslash($_POST['rrv_role']??'')));
        update_post_meta($post_id,'_rrv_rating',min(5,max(1,absint($_POST['rrv_rating']??5))));
    }
    if(get_post_type($post_id)==='rrv_booking'&&current_user_can('edit_post',$post_id)&&isset($_POST['rrv_booking_status'])){
        update_post_meta($post_id,'_rrv_status',sanitize_key(wp_unslash($_POST['rrv_booking_status'])));
    }
}
add_action('save_post','rrv_save_meta');

function rrv_booking_columns($cols){
    return ['cb'=>$cols['cb'],'reference'=>'Reference','title'=>'Guest','room'=>'Room','dates'=>'Dates','status'=>'Status','date'=>'Received'];
}
add_filter('manage_rrv_booking_posts_columns','rrv_booking_columns');
function rrv_booking_column($col,$id){
    if($col==='reference')echo '<strong>'.esc_html(get_post_meta($id,'_rrv_reference',true)).'</strong>';
    if($col==='room')echo esc_html(get_post_meta($id,'_rrv_room',true));
    if($col==='dates')echo esc_html(get_post_meta($id,'_rrv_checkin',true).' → '.get_post_meta($id,'_rrv_checkout',true));
    if($col==='status')echo esc_html(ucfirst(get_post_meta($id,'_rrv_status',true)?:'pending'));
}
add_action('manage_rrv_booking_posts_custom_column','rrv_booking_column',10,2);
