<?php
/**
 * Rest API Logs
 *
 * @package     GamiPress\Rest_API\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Logs item schema
 *
 * @since 1.6.5
 *
 * @param array $schema
 *
 * @return array
 */
function gamipress_logs_rest_item_schema( $schema ) {

    // Properties
    $schema['properties'] = array_merge( $schema['properties'], array(
        'title' => array(
            'description' => __( 'The title for the object.', 'gamipress' ),
            'type'        => 'string',
            'context'     => array( 'view', 'edit', 'embed' ),
        ),
        'type'            => array(
            'description' => __( 'Type of log for the object.', 'gamipress' ),
            'type'        => 'string',
            'context'     => array( 'view', 'edit', 'embed' ),
        ),
        'trigger_type'    => array(
            'description' => __( 'Trigger of log for the object.', 'gamipress' ),
            'type'        => 'string',
            'context'     => array( 'view', 'edit', 'embed' ),
        ),
        'access' => array(
            'description' => __( 'Access of log for the object.', 'gamipress' ),
            'type'        => 'string',
            'context'     => array( 'view', 'edit', 'embed' ),
        ),
        'user_id' => array(
            'description' => __( 'The ID for the user of the object.', 'gamipress' ),
            'type'        => 'integer',
            'context'     => array( 'view', 'edit', 'embed' ),
        ),
        'date'            => array(
            'description' => __( 'The date the object was created, in the site\'s timezone.', 'gamipress' ),
            'type'        => 'string',
            'format'      => 'date-time',
            'context'     => array( 'view', 'edit', 'embed' ),
        ),
    ) );

    return $schema;

}
add_filter( 'ct_rest_gamipress_logs_schema', 'gamipress_logs_rest_item_schema' );

/**
 * Logs collection params
 *
 * @since 1.6.5
 *
 * @param array     $query_params
 * @param CT_Table  $ct_table
 *
 * @return array
 */
function gamipress_logs_rest_collection_params( $query_params, $ct_table ) {

    // Type
    $log_types = gamipress_get_log_types();
    // Allow empty type
    $log_types = array_merge( array( '' ), array_keys( $log_types ) );

    $query_params['type'] = array(
        'description'        => __( 'Limit result set to logs with a specific type.', 'gamipress' ),
        'type'               => 'string',
        'default'            => '',
        'enum'               => $log_types,
    );

    // Trigger Type
    $query_params['trigger_type'] = array(
        'description'        => __( 'Limit result set  to logs with a specific trigger.' ),
        'type'               => 'array',
        'items'              => array(
            'type'           => 'string',
        ),
        'default'            => array(),
    );

    // Access
    $query_params['access'] = array(
        'description'        => __( 'Limit result set to logs with a specific access.', 'gamipress' ),
        'type'               => 'string',
        'default'            => '',
        'enum'               => array(
            '',
            'public',
            'private'
        ),
    );

    // User ID
    $query_params['user_id'] = array(
        'description'         => __( 'Limit result set to logs assigned to specific users.', 'gamipress' ),
        'type'                => 'array',
        'items'               => array(
            'type'            => 'integer',
        ),
        'default'             => array(),
    );

    // Exclude ( exclude => log__not_in )
    $query_params['exclude'] = array(
        'description'        => __( 'Ensure result set excludes specific IDs.', 'gamipress' ),
        'type'               => 'array',
        'items'              => array(
            'type'           => 'integer',
        ),
        'default'            => array(),
    );

     // Include ( include => log__in )
    $query_params['include'] = array(
        'description'        => __( 'Limit result set to specific IDs.', 'gamipress' ),
        'type'               => 'array',
        'items'              => array(
            'type'           => 'integer',
        ),
        'default'            => array(),
    );

    // After
    $query_params['after'] = array(
        'description'        => __( 'Limit response to logs after a given ISO8601 compliant date.', 'gamipress' ),
        'type'               => 'string',
        'format'             => 'date-time',
    );

    // Before
    $query_params['before'] = array(
        'description'        => __( 'Limit response to logs before a given ISO8601 compliant date.', 'gamipress' ),
        'type'               => 'string',
        'format'             => 'date-time',
    );


    return $query_params;
}
add_filter( 'ct_rest_gamipress_logs_collection_params', 'gamipress_logs_rest_collection_params', 10, 2 );

/**
 * Set custom parameters mapping
 *
 * @since 1.6.5
 *
 * @param array             $parameter_mappings Array of parameters to map.
 * @param CT_Table          $ct_table           Table object.
 * @param WP_REST_Request   $request            The request given.
 *
 * @return array
 */
function gamipress_logs_rest_parameter_mappings( $parameter_mappings, $ct_table, $request ) {

    // Fields
    $parameter_mappings['type'] = 'type';
    $parameter_mappings['trigger_type'] = 'trigger_type';
    $parameter_mappings['access'] = 'access';
    $parameter_mappings['user_id'] = 'user_id';

    // Mappings
    $parameter_mappings['exclude'] = 'log__not_in';
    $parameter_mappings['include'] = 'log__in';

    // Date
    $parameter_mappings['after'] = 'after';
    $parameter_mappings['before'] = 'before';

    return $parameter_mappings;
}
add_filter( 'ct_rest_gamipress_logs_parameter_mappings', 'gamipress_logs_rest_parameter_mappings', 10, 3 );

/**
 * Fields sanitization
 *
 * @since 1.6.5
 *
 * @param mixed             $value      The field value given.
 * @param string            $field      The field name.
 * @param WP_REST_Request   $request    The request object.
 *
 * @return mixed|WP_Error
 */
function gamipress_logs_rest_sanitize_field_value( $value, $field, $request ) {

    switch( $field ) {
        case 'user_id':
            // Validate user ID
            $value = absint( $value );
            $user_obj = get_userdata( $value );

            if ( ! $user_obj ) {
                return new WP_Error( 'rest_invalid_field', __( 'Invalid user ID.', 'gamipress' ), array( 'status' => 400 ) );
            }
            break;
        case 'date':
            // Validate date.
            $mm = substr( $value, 5, 2 );
            $jj = substr( $value, 8, 2 );
            $aa = substr( $value, 0, 4 );
            $valid_date = wp_checkdate( $mm, $jj, $aa, $value );

            if ( ! $valid_date ) {
                return new WP_Error( 'rest_invalid_field', __( 'Invalid date.', 'gamipress' ), array( 'status' => 400 ) );
            }
            break;
    }

    return $value;
}
add_filter( 'ct_rest_gamipress_logs_sanitize_field_value', 'gamipress_logs_rest_sanitize_field_value', 10, 3 );

