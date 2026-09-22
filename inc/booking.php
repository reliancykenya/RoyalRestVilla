<?php
if (!defined('ABSPATH')) exit;

function rrv_booking_redirect($status,$ref=''){
    $url=home_url('/?booking='.rawurlencode($status));
    if($ref)$url.='&ref='.rawurlencode($ref);
    wp_safe_redirect($url.'#book');
    exit;
}

function rrv_room_is_available($room,$checkin,$checkout){
    if(!$room||$room==='Any available room'||$room==='Any room') return true;
    $q=new WP_Query([
        'post_type'=>'rrv_booking',
        'post_status'=>'publish',
        'posts_per_page'=>1,
        'fields'=>'ids',
        'meta_query'=>[
            'relation'=>'AND',
            ['key'=>'_rrv_room','value'=>$room,'compare'=>'='],
            ['key'=>'_rrv_status','value'=>['confirmed','checked-in'],'compare'=>'IN'],
            ['key'=>'_rrv_checkin','value'=>$checkout,'compare'=>'<','type'=>'DATE'],
            ['key'=>'_rrv_checkout','value'=>$checkin,'compare'=>'>','type'=>'DATE']
        ]
    ]);
    return !$q->have_posts();
}

function rrv_handle_booking(){
    if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST'||empty($_POST['rrv_booking_submit'])) return;
    if(empty($_POST['rrv_booking_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rrv_booking_nonce'])),'rrv_booking')) rrv_booking_redirect('security');
    if(!empty($_POST['website'])) rrv_booking_redirect('invalid');

    $ip=sanitize_text_field($_SERVER['REMOTE_ADDR']??'unknown');
    $key='rrv_rate_'.md5($ip);
    if(get_transient($key)) rrv_booking_redirect('rate');
    set_transient($key,1,20);

    $data=[];
    foreach(['name','email','phone','room','checkin','checkout','guests','message'] as $k){
        $raw=$_POST['rrv_'.$k]??'';
        $data[$k]=$k==='email'?sanitize_email(wp_unslash($raw)):($k==='message'?sanitize_textarea_field(wp_unslash($raw)):sanitize_text_field(wp_unslash($raw)));
    }

    if(!$data['name']||!is_email($data['email'])||!$data['phone']) rrv_booking_redirect('invalid');
    $in=DateTime::createFromFormat('Y-m-d',$data['checkin']);
    $out=DateTime::createFromFormat('Y-m-d',$data['checkout']);
    if(!$in||!$out||$out<=$in) rrv_booking_redirect('dates');
    if(!rrv_room_is_available($data['room'],$data['checkin'],$data['checkout'])) rrv_booking_redirect('unavailable');

    $booking_id=wp_insert_post([
        'post_type'=>'rrv_booking',
        'post_status'=>'publish',
        'post_title'=>$data['name'].' — '.$data['checkin']
    ]);
    if(is_wp_error($booking_id)) rrv_booking_redirect('error');

    $ref='RRV-'.wp_date('Ymd').'-'.str_pad((string)$booking_id,4,'0',STR_PAD_LEFT);
    foreach($data as $k=>$v) update_post_meta($booking_id,'_rrv_'.$k,$v);
    update_post_meta($booking_id,'_rrv_status','pending');
    update_post_meta($booking_id,'_rrv_reference',$ref);

    $to=get_theme_mod('rrv_booking_email',get_option('admin_email'));
    $subject=get_theme_mod('rrv_booking_subject','New Royal Rest Villa booking request').' — '.$ref;
    $body="Booking reference: {$ref}\n\nGuest: {$data['name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nRoom: {$data['room']}\nCheck-in: {$data['checkin']}\nCheck-out: {$data['checkout']}\nGuests: {$data['guests']}\n\nMessage:\n{$data['message']}";
    wp_mail($to,$subject,$body,['Reply-To: '.$data['name'].' <'.$data['email'].'>']);

    $guest="Hello {$data['name']},\n\nThank you for choosing Royal Rest Villa. We received your booking request.\n\nReference: {$ref}\nDates: {$data['checkin']} to {$data['checkout']}\nRoom: {$data['room']}\n\nThis is a booking request, not yet a confirmed reservation. We will contact you shortly to confirm availability.\n\nRoyal Rest Villa";
    wp_mail($data['email'],'Royal Rest Villa booking request — '.$ref,$guest);

    rrv_booking_redirect('received',$ref);
}
add_action('template_redirect','rrv_handle_booking');

function rrv_booking_form_shortcode(){
    $rooms=get_posts(['post_type'=>'rrv_room','numberposts'=>-1,'post_status'=>'publish','orderby'=>'menu_order title','order'=>'ASC']);
    $status=isset($_GET['booking'])?sanitize_key(wp_unslash($_GET['booking'])):'';
    $ref=isset($_GET['ref'])?sanitize_text_field(wp_unslash($_GET['ref'])):'';
    $messages=[
        'received'=>['success',$ref?'Thank you. Your request '.$ref.' has been received. We will contact you shortly.':'Thank you. Your booking request has been received.'],
        'invalid'=>['error','Please complete your name, email and phone number and try again.'],
        'dates'=>['error','Please choose valid check-in and check-out dates.'],
        'unavailable'=>['error','That room is already confirmed for part of those dates. Please choose another room or different dates.'],
        'rate'=>['error','Please wait a few seconds before sending another request.'],
        'security'=>['error','Your session expired. Please refresh the page and try again.'],
        'error'=>['error','We could not save your request. Please contact Royal Rest Villa directly.']
    ];

    $prefill_room=isset($_GET['room'])?sanitize_text_field(wp_unslash($_GET['room'])):'';
    ob_start();
    if($status&&isset($messages[$status])) echo '<div class="rrv-alert rrv-alert--'.$messages[$status][0].'" role="status" aria-live="polite">'.esc_html($messages[$status][1]).'</div>';
    ?>
    <form class="rrv-booking-form" method="post" action="<?php echo esc_url(home_url('/')); ?>">
      <?php wp_nonce_field('rrv_booking','rrv_booking_nonce'); ?>
      <input type="text" name="website" tabindex="-1" autocomplete="off" class="rrv-honeypot" aria-hidden="true">
      <div class="rrv-form-grid">
        <div class="rrv-field"><label>Full name *</label><input name="rrv_name" required autocomplete="name"></div>
        <div class="rrv-field"><label>Email *</label><input type="email" name="rrv_email" required autocomplete="email"></div>
        <div class="rrv-field"><label>Phone / WhatsApp *</label><input name="rrv_phone" required autocomplete="tel"></div>
        <div class="rrv-field"><label>Room</label><select name="rrv_room" id="rrv-book-room"><option value="Any available room">Any available room</option><?php foreach($rooms as $room){$selected=$prefill_room===$room->post_title?' selected':'';echo '<option'.$selected.'>'.esc_html($room->post_title).'</option>';}?></select></div>
        <div class="rrv-field"><label>Check-in *</label><input type="date" id="rrv-book-checkin" name="rrv_checkin" min="<?php echo esc_attr(wp_date('Y-m-d'));?>" required></div>
        <div class="rrv-field"><label>Check-out *</label><input type="date" id="rrv-book-checkout" name="rrv_checkout" min="<?php echo esc_attr(wp_date('Y-m-d',strtotime('+1 day')));?>" required></div>
        <div class="rrv-field"><label>Guests</label><input type="number" id="rrv-book-guests" name="rrv_guests" min="1" max="20" value="2"></div>
        <div class="rrv-field full"><label>Message</label><textarea name="rrv_message" placeholder="Anything we should know about your stay?"></textarea></div>
        <div class="full"><button class="rrv-btn rrv-btn--gold" type="submit" name="rrv_booking_submit" value="1">Send booking request</button></div>
      </div>
      <p class="rrv-booking-note"><?php echo esc_html(get_theme_mod('rrv_booking_note','Submitting a request does not constitute a confirmed reservation. We will contact you to confirm availability.')); ?></p>
    </form>
    <?php return ob_get_clean();
}
add_shortcode('rrv_booking_form','rrv_booking_form_shortcode');
