<?php
/**
 * GamiPress Youtube Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Youtube
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_youtube] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_youtube_shortcode() {

    gamipress_register_shortcode( 'gamipress_youtube', array(
        'name'            => __( 'Youtube', 'gamipress' ),
        'description'     => __( 'Render a youtube video.', 'gamipress' ),
        'output_callback' => 'gamipress_youtube_shortcode',
        'fields'      => array(
            'url' => array(
                'name'        => __( 'URL or ID', 'gamipress' ),
                'description' => __( 'The Youtube video URL or ID.', 'gamipress' ),
                'type' 	    => 'text',
            ),
            'width' => array(
                'name'        => __( 'Player width', 'gamipress' ),
                'description' => __( 'The player width (in pixels). By default, 640.', 'gamipress' ),
                'type' 	    => 'text',
                'default' 	=> '640',
            ),
            'height' => array(
                'name'        => __( 'Player height', 'gamipress' ),
                'description' => __( 'The player height (in pixels). By default, 360.', 'gamipress' ),
                'type' 	    => 'text',
                'default' 	=> '360',
            ),
            'autoplay' => array(
                'name'        => __( 'Autoplay', 'gamipress' ),
                'description' => __( 'Check this option to automatically play the video.', 'gamipress' ),
                'type' 	    => 'checkbox',
                'classes' 	=> 'gamipress-switch',
            ),
            'controls' => array(
                'name'        => __( 'Show player controls?', 'gamipress' ),
                'description' => __( 'Check this option to show player controls.', 'gamipress' ),
                'type' 	    => 'checkbox',
                'classes' 	=> 'gamipress-switch',
                'default' 	=> 'yes',
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_youtube_shortcode' );

/**
 * Youtube Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_youtube_shortcode( $atts = array() ) {

    // Get the received shortcode attributes
    $atts = shortcode_atts( array(
        'url'       => '',
        'width'     => '640',
        'height'    => '360',
        'autoplay'  => 'no',
        'controls'  => 'yes',
    ), $atts, 'gamipress_youtube' );

    $video_id = gamipress_youtube_get_video_id_from_url( $atts['url'] );
    $thumbnail_url = GAMIPRESS_YOUTUBE_URL . 'assets/img/youtube-preview.svg';

    // Show thumbnail only if is an admin preview
    $show_thumbnail = ( defined( 'REST_REQUEST' ) && REST_REQUEST );

    // Show thumbnail only for blocks
    if( gamipress_get_renderer() !== 'block' ) {
        $show_thumbnail = false;
    }

    if( ! empty( $video_id ) ) {
        $thumbnail_url = "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
    }

    // Enqueue scripts
    gamipress_youtube_enqueue_scripts();

    ob_start(); ?>
    <div id="<?php echo esc_attr( $video_id ); ?>" class="gamipress-youtube-video"
         data-id="<?php echo esc_attr( $video_id ); ?>"
         data-url="<?php echo esc_attr( $atts['url'] ); ?>"
         data-width="<?php echo esc_attr( $atts['width'] ); ?>"
         data-height="<?php echo esc_attr( $atts['height'] ); ?>"
         data-autoplay="<?php echo esc_attr( $atts['autoplay'] ); ?>"
         data-controls="<?php echo esc_attr( $atts['controls'] ); ?>"
         ><?php if( $show_thumbnail ) : ?><img src="<?php echo esc_attr( $thumbnail_url ); ?>" width="<?php echo esc_attr( $atts['width'] ); ?>"/><?php endif; ?></div>
    <?php $output = ob_get_clean();

    // Return our rendered youtube video
    return $output;

}
