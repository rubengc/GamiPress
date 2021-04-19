<?php
/**
 * Rest Meta Fields class
 *
 * Based on WP_REST_Post_Meta_Fields class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Core class used to manage meta values for posts via the REST API.
 *
 * @since 1.0.0
 *
 * @see WP_REST_Meta_Fields
 */
class CT_REST_Meta_Fields extends WP_REST_Meta_Fields {

    /**
     * Table name.
     *
     * @since 1.0.0
     * @var string
     */
    protected $name;

    /**
     * Table Meta table object.
     *
     * @since 1.0.0
     * @access public
     * @var CT_Table $table
     */
    public $table;

    /**
     * Constructor.
     *
     * @since 1.0.0
     *
     * @param string $name Table name to register fields for.
     */
    public function __construct( $name ) {
        $this->name = $name;
        $this->table = ct_get_table_object( $name );

    }

    /**
     * Retrieves the object meta type.
     *
     * @since 1.0.0
     *
     * @return string The meta type.
     */
    protected function get_meta_type() {
        return $this->table->meta->name;
    }

    /**
     * Retrieves the object meta subtype.
     *
     * @since 1.0.0
     *
     * @return string Subtype for the meta type, or empty string if no specific subtype.
     */
    protected function get_meta_subtype() {
        return $this->name;
    }

    /**
     * Retrieves the type for register_rest_field().
     *
     * @since 1.0.0
     *
     * @see register_rest_field()
     *
     * @return string The REST field type.
     */
    public function get_rest_field_type() {
        return $this->name;
    }

    /**
     * Retrieves the meta field value.
     *
     * @since 1.0.0
     *
     * @param int             $object_id Object ID to fetch meta for.
     * @param WP_REST_Request $request   Full details about the request.
     * @return WP_Error|object Object containing the meta values by name, otherwise WP_Error object.
     */
    public function get_value( $object_id, $request ) {
        $fields   = $this->get_registered_fields();
        $response = array();

        foreach ( $fields as $meta_key => $args ) {
            $name = $args['name'];
            $all_values = ct_get_object_meta( $object_id, $meta_key, false );
            if ( $args['single'] ) {
                if ( empty( $all_values ) ) {
                    $value = $args['schema']['default'];
                } else {
                    $value = $all_values[0];
                }
                $value = $this->prepare_value_for_response( $value, $request, $args );
            } else {
                $value = array();
                foreach ( $all_values as $row ) {
                    $value[] = $this->prepare_value_for_response( $row, $request, $args );
                }
            }

            $response[ $name ] = $value;
        }

        return $response;
    }

    /**
     * Updates a meta value for an object.
     *
     * @since 4.7.0
     *
     * @param int    $object_id Object ID to update.
     * @param string $meta_key  Key for the custom field.
     * @param string $name      Name for the field that is exposed in the REST API.
     * @param mixed  $value     Updated value.
     * @return bool|WP_Error True if the meta field was updated, WP_Error otherwise.
     */
    protected function update_meta_value( $object_id, $meta_key, $name, $value ) {
        $meta_type = $this->get_meta_type();
        if ( ! current_user_can( $this->table->cap->edit_post_meta, $object_id, $meta_key ) ) {
            return new WP_Error(
                'rest_cannot_update',
                /* translators: %s: custom field key */
                sprintf( __( 'Sorry, you are not allowed to edit the %s custom field.' ), $name ),
                array( 'key' => $name, 'status' => rest_authorization_required_code() )
            );
        }

        // Do the exact same check for a duplicate value as in update_metadata() to avoid update_metadata() returning false.
        $old_value = ct_get_object_meta( $object_id, $meta_key );
        $subtype   = get_object_subtype( $meta_type, $object_id );

        if ( 1 === count( $old_value ) ) {
            if ( (string) sanitize_meta( $meta_key, $value, $meta_type, $subtype ) === $old_value[0] ) {
                return true;
            }
        }

        if ( ! ct_update_object_meta( $object_id, wp_slash( $meta_key ), wp_slash( $value ) ) ) {
            return new WP_Error(
                'rest_meta_database_error',
                __( 'Could not update meta value in database.' ),
                array( 'key' => $name, 'status' => WP_Http::INTERNAL_SERVER_ERROR )
            );
        }

        return true;
    }
}
