<?php
if (!defined('ABSPATH')) exit;
function rrv_handle_booking(){
    if($_SERVER['REQUEST_METHOD']!=='POST'||empty($_POST['rrv_booking_submit'])) return;
    if(empty($_POST['rrv_booking_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rrv_booking_nonce'])),'rrv_booking')){set_transient('rrv_booking_error','Security check failed. Please try again.',30);return;}
    if(!empty($_POST['website'])) return;
    $ip=sanitize_text_field($_SERVER['REMOTE_ADDR']??'unknown');$key='rrv_rate_'.md5($ip);
    if(get_transient($key)){set_transient('rrv_booking_error','Please wait a moment before sending another request.',30);return;}set_transient($key,1,20);
    $data=[];foreach(['name','email','phone','room','checkin','checkout','guests','message'] as $k){$raw=$_POST['rrv_'.$k]??'';$data[$k]=$k==='email'?sanitize_email(wp_unslash($raw)):($k==='message'?sanitize_textarea_field(wp_unslash($raw)):sanitize_text_field(wp_unslash($raw)));}
    $errors=[];
    if(!$data['name']||!is_email($data['email'])||!$data['phone'])$errors[]='Please provide your name, a valid email and phone number.';
    $in=DateTime::createFromFormat('Y-m-d',$data['checkin']);$out=DateTime::createFromFormat('Y-m-d',$data['checkout']);
    if(!$in||!$out||$out<=$in)$errors[]='Please choose valid check-in and check-out dates.';
    if($errors){set_transient('rrv_booking_error',implode(' ',$errors),30);return;}
    $booking_id=wp_insert_post(['post_type'=>'rrv_booking','post_status'=>'publish','post_title'=>$data['name'].' — '.$data['checkin']]);
    if(is_wp_error($booking_id)){set_transient('rrv_booking_error','We could not save your request. Please contact us directly.',30);return;}
    foreach($data as $k=>$v)update_post_meta($booking_id,'_rrv_'.$k,$v);update_post_meta($booking_id,'_rrv_status','pending');
    $to=get_theme_mod('rrv_booking_email',get_option('admin_email'));$subject=get_theme_mod('rrv_booking_subject','New Royal Rest Villa booking request');
    $body="New booking request\n\nGuest: {$data['name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nRoom: {$data['room']}\nCheck-in: {$data['checkin']}\nCheck-out: {$data['checkout']}\nGuests: {$data['guests']}\n\nMessage:\n{$data['message']}";
    wp_mail($to,$subject,$body,['Reply-To: '.$data['name'].' <'.$data['email'].'>']);
    wp_mail($data['email'],'We received your Royal Rest Villa booking request',"Hello {$data['name']},\n\nThank you for choosing Royal Rest Villa. We received your booking request for {$data['checkin']} to {$data['checkout']}. We will confirm availability with you shortly.\n\nRoyal Rest Villa");
    set_transient('rrv_booking_success','Thank you. Your booking request has been received and we will contact you shortly.',30);
    wp_safe_redirect(add_query_arg('booking','received',wp_get_referer()?:rrv_booking_url()));exit;
}
add_action('template_redirect','rrv_handle_booking');

function rrv_booking_form_shortcode(){
    $rooms=get_posts(['post_type'=>'rrv_room','numberposts'=>-1,'post_status'=>'publish','orderby'=>'menu_order title','order'=>'ASC']);
    ob_start();
    $success=get_transient('rrv_booking_success');$error=get_transient('rrv_booking_error');delete_transient('rrv_booking_success');delete_transient('rrv_booking_error');
    if($success)echo '<div class="rrv-alert rrv-alert--success">'.esc_html($success).'</div>';if($error)echo '<div class="rrv-alert rrv-alert--error">'.esc_html($error).'</div>';
    ?>
    <form class="rrv-booking-form" method="post">
      <?php wp_nonce_field('rrv_booking','rrv_booking_nonce'); ?><input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
      <div class="rrv-form-grid">
        <div class="rrv-field"><label>Full name *</label><input name="rrv_name" required autocomplete="name"></div>
        <div class="rrv-field"><label>Email *</label><input type="email" name="rrv_email" required autocomplete="email"></div>
        <div class="rrv-field"><label>Phone / WhatsApp *</label><input name="rrv_phone" required autocomplete="tel"></div>
        <div class="rrv-field"><label>Room</label><select name="rrv_room"><option value="Any available room">Any available room</option><?php foreach($rooms as $room)echo '<option>'.esc_html($room->post_title).'</option>';?></select></div>
        <div class="rrv-field"><label>Check-in *</label><input type="date" name="rrv_checkin" min="<?php echo esc_attr(wp_date('Y-m-d'));?>" required></div>
        <div class="rrv-field"><label>Check-out *</label><input type="date" name="rrv_checkout" min="<?php echo esc_attr(wp_date('Y-m-d',strtotime('+1 day')));?>" required></div>
        <div class="rrv-field"><label>Guests</label><input type="number" name="rrv_guests" min="1" max="20" value="2"></div>
        <div class="rrv-field full"><label>Message</label><textarea name="rrv_message" placeholder="Anything we should know about your stay?"></textarea></div>
        <div class="full"><button class="rrv-btn rrv-btn--gold" type="submit" name="rrv_booking_submit" value="1">Send booking request</button></div>
      </div>
    </form>
    <?php return ob_get_clean();
}
add_shortcode('rrv_booking_form','rrv_booking_form_shortcode');
