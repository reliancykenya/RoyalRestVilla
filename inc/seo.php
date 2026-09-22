<?php
if (!defined('ABSPATH')) exit;

function rrv_seo_description() {
    if (is_singular()) {
        $custom = get_post_meta(get_queried_object_id(), '_rrv_seo_description', true);
        if ($custom) return $custom;
        if (has_excerpt()) return wp_strip_all_tags(get_the_excerpt());
    }
    return get_theme_mod('rrv_seo_default_description', 'Royal Rest Villa is a peaceful countryside retreat for comfortable short stays, privacy and quiet luxury.');
}

function rrv_seo_output() {
    if (is_admin()) return;
    $description = rrv_seo_description();
    $canonical = is_singular() ? get_permalink() : home_url('/');
    $title = wp_get_document_title();
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LodgingBusiness',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'description' => $description,
        'telephone' => get_theme_mod('rrv_phone'),
        'email' => get_theme_mod('rrv_email'),
        'priceRange' => get_theme_mod('rrv_seo_price_range', '$$'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => get_theme_mod('rrv_address'),
            'addressCountry' => 'KE'
        ]
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'rrv_seo_output', 4);

function rrv_seo_box($post) {
    wp_nonce_field('rrv_seo_save', 'rrv_seo_nonce');
    $title = get_post_meta($post->ID, '_rrv_seo_title', true);
    $description = get_post_meta($post->ID, '_rrv_seo_description', true);
    echo '<p><label><strong>SEO title</strong></label><input class="widefat" name="rrv_seo_title" value="' . esc_attr($title) . '"></p>';
    echo '<p><label><strong>Meta description</strong></label><textarea class="widefat" rows="3" name="rrv_seo_description">' . esc_textarea($description) . '</textarea></p>';
}
function rrv_add_seo_boxes() {
    foreach (['post','page','rrv_room'] as $type) add_meta_box('rrv_seo','Royal Rest SEO','rrv_seo_box',$type,'normal','low');
}
add_action('add_meta_boxes','rrv_add_seo_boxes');

function rrv_save_seo_fields($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (empty($_POST['rrv_seo_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rrv_seo_nonce'])), 'rrv_seo_save')) return;
    if (isset($_POST['rrv_seo_title'])) update_post_meta($post_id, '_rrv_seo_title', sanitize_text_field(wp_unslash($_POST['rrv_seo_title'])));
    if (isset($_POST['rrv_seo_description'])) update_post_meta($post_id, '_rrv_seo_description', sanitize_textarea_field(wp_unslash($_POST['rrv_seo_description'])));
}
add_action('save_post','rrv_save_seo_fields');

add_filter('pre_get_document_title', function($title) {
    if (is_singular()) {
        $custom = get_post_meta(get_queried_object_id(), '_rrv_seo_title', true);
        if ($custom) return $custom;
    }
    return $title;
});
