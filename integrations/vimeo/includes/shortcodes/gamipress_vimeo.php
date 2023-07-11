<?php
/**
 * GamiPress Vimeo Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Vimeo
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_vimeo] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_vimeo_shortcode() {

    gamipress_register_shortcode( 'gamipress_vimeo', array(
        'name'            => __( 'Vimeo', 'gamipress' ),
        'description'     => __( 'Render a vimeo video.', 'gamipress' ),
        'output_callback' => 'gamipress_vimeo_shortcode',
        'fields'      => array(
            'url' => array(
                'name'        => __( 'URL or ID', 'gamipress' ),
                'description' => __( 'The Vimeo video URL or ID.', 'gamipress' ),
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
            'from_url' => array(
                'name'              => __( 'Load video from URL', 'gamipress' ),
                'description'       => __( 'By default, video is loaded from ID. Check this option if video is private or if you can not get it loaded correctly.', 'gamipress' ),
                'type' 		        => 'checkbox',
                'classes' 	        => 'gamipress-switch',
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_vimeo_shortcode' );

/**
 * Vimeo Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_vimeo_shortcode( $atts = array() ) {

    // Get the received shortcode attributes
    $atts = shortcode_atts( array(
        'url'       => '',
        'width'     => '640',
        'height'    => '360',
        'from_url'  => 'no',
    ), $atts, 'gamipress_vimeo' );

    $video_id = gamipress_vimeo_get_video_id_from_url( $atts['url'] );
    $thumbnail_url = GAMIPRESS_VIMEO_URL . 'assets/img/vimeo-preview.svg';

    // Show thumbnail only if is an admin preview
    $show_thumbnail = ( defined( 'REST_REQUEST' ) && REST_REQUEST );

    // Show thumbnail only for blocks
    if( gamipress_get_renderer() !== 'block' ) {
        $show_thumbnail = false;
    }

    if( ! empty( $video_id ) && $show_thumbnail ) {
        $response = wp_remote_get( "http://vimeo.com/api/v2/video/{$video_id}.json" );
        $response_body = wp_remote_retrieve_body( $response );

        $data = json_decode( $response_body, true );

        if( isset( $data[0] ) && isset( $data[0]['thumbnail_large'] ) ) {
            $thumbnail_url = $data[0]['thumbnail_large'];
        }
    }

    // Enqueue scripts
    gamipress_vimeo_enqueue_scripts();

    ob_start(); ?>
    <div id="<?php echo esc_attr( $video_id ); ?>" class="gamipress-vimeo-video"
         data-id="<?php echo esc_attr( $video_id ); ?>"
         data-url="<?php echo esc_attr( $atts['url'] ); ?>"
         data-width="<?php echo esc_attr( $atts['width'] ); ?>"
         data-height="<?php echo esc_attr( $atts['height'] ); ?>"
         data-from-url="<?php echo esc_attr( $atts['from_url'] ); ?>"
         ><?php if( $show_thumbnail ) : ?><img src="<?php echo esc_attr( $thumbnail_url ); ?>" width="<?php echo esc_attr( $atts['width'] ); ?>"/><?php endif; ?></div>
    <?php $output = ob_get_clean();

    // Return our rendered vimeo video
    return $output;

}
