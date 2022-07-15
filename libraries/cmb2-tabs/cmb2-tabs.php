<?php
/**
 * @package      RGC\CMB2\Tabs
 * @author       Ruben Garcia (RubenGC) <rubengcdev@gmail.com>, GamiPress <contact@gamipress.com>, AutomatorWP <contact@automatorwp.com>
 * @copyright    Copyright (c) Ruben Garcia
 *
 * Plugin Name: CMB2 Tabs
 * Plugin URI: https://github.com/rubengc/cmb2-tabs
 * GitHub Plugin URI: https://github.com/rubengc/cmb2-tabs
 * Description: Tabs for CMB2 boxes.
 * Version: 1.0.4
 * Author: Ruben Garcia
 * Author URI: http://rubengc.com/
 * License: GPLv2+
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Prevent CMB2 autoload adding "RGC_" at start
if( !class_exists( 'RGC_CMB2_Tabs' ) ) {
    /**
     * Class RGC_CMB2_Tabs
     */
    class RGC_CMB2_Tabs {

        /**
         * Current version number
         */
        const VERSION = '1.0.4';

        /**
         * Initialize the plugin by hooking into CMB2
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
            add_action( 'doing_dark_mode', array( $this, 'setup_dark_mode' ) );
            add_action( 'cmb2_before_form', array( $this, 'before_form' ), 10, 4 );
            add_action( 'cmb2_after_form', array( $this, 'after_form' ), 10, 4 );
        }

        /**
         * Render tabs
         *
         * @param array  $cmb_id      The current box ID
         * @param int    $object_id   The ID of the current object
         * @param string $object_type The type of object you are working with.
         * @param array  $cmb         This CMB2 object
         */
        public function before_form( $cmb_id, $object_id, $object_type, $cmb ) {
            if( $cmb->prop( 'tabs' ) && is_array( $cmb->prop( 'tabs' ) ) ) : ?>
                <div class="cmb-tabs-wrap cmb-tabs-<?php echo ( ( $cmb->prop( 'vertical_tabs' ) ) ? 'vertical' : 'horizontal' ) ?>">
                    <div class="cmb-tabs">

                        <?php foreach( $cmb->prop( 'tabs' ) as $tab ) :
                            $fields_selector = array();

                            if( ! isset( $tab['id'] ) ) {
                                continue;
                            }

                            if( ! isset( $tab['fields'] ) ) {
                                $tab['fields'] = array();
                            }

                            if( ! is_array( $tab['fields'] ) ) {
                                $tab['fields'] = array();
                            }

                            foreach( $tab['fields'] as $tab_field )  :
                                $fields_selector[] = '.' . 'cmb2-id-' . str_replace( '_', '-', sanitize_html_class( $tab_field ) ) . ':not(.cmb2-tab-ignore)';
                            endforeach;

                            $fields_selector = apply_filters( 'cmb2_tabs_tab_fields_selector', $fields_selector, $tab, $cmb_id, $object_id, $object_type, $cmb );
                            $fields_selector = apply_filters( 'cmb2_tabs_tab_' . $tab['id'] . '_fields_selector', $fields_selector, $tab, $cmb_id, $object_id, $object_type, $cmb );
                            ?>

                            <div id="<?php echo $cmb_id . '-tab-' . $tab['id']; ?>" class="cmb-tab" data-fields="<?php echo implode( ', ', $fields_selector ); ?>">

                                <?php if( isset( $tab['icon'] ) && ! empty( $tab['icon'] ) ) :
                                    $tab['icon'] = strpos($tab['icon'], 'dashicons') !== false ? 'dashicons ' . $tab['icon'] : $tab['icon']?>
                                    <span class="cmb-tab-icon"><i class="<?php echo $tab['icon']; ?>"></i></span>
                                <?php endif; ?>

                                <?php if( isset( $tab['title'] ) && ! empty( $tab['title'] ) ) : ?>
                                    <span class="cmb-tab-title"><?php echo $tab['title']; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                    </div> <!-- .cmb-tabs -->
            <?php endif;
        }

        /**
         * Close tabs
         *
         * @param array  $cmb_id      The current box ID
         * @param int    $object_id   The ID of the current object
         * @param string $object_type The type of object you are working with.
         * @param array  $cmb         This CMB2 object
         */
        public function after_form( $cmb_id, $object_id, $object_type, $cmb ) {
            if( $cmb->prop( 'tabs' ) && is_array( $cmb->prop( 'tabs' ) ) ) : ?>
                </div> <!-- .cmb-tabs-wrap -->
            <?php endif;
        }

        /**
         * Enqueue scripts and styles
         */
        public function setup_admin_scripts() {
            wp_register_script( 'cmb-tabs', plugins_url( 'js/tabs.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
            wp_enqueue_script( 'cmb-tabs' );

            wp_enqueue_style( 'cmb-tabs', plugins_url( 'css/tabs.css', __FILE__ ), array(), self::VERSION );
            wp_enqueue_style( 'cmb-tabs' );

        }

        /**
         * Enqueue dark mode styles
         */
        public function setup_dark_mode() {
            wp_enqueue_style( 'cmb-tabs-dark-mode', plugins_url( 'css/dark-mode.css', __FILE__ ), array(), self::VERSION );
            wp_enqueue_style( 'cmb-tabs-dark-mode' );

        }

    }

    $cmb2_tabs = new RGC_CMB2_Tabs();
}
