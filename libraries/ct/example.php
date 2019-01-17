<?php
/**
 * Example script about how to setup custom tables with CT.
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 */

// Include the Custom Table (CT) lib
require_once __DIR__ . '/ct.php';

/* ----------------------------------
 * INITIALIZATION - Main example about how to add a custom table through CT
   ---------------------------------- */

function yourprefix_init() {

    $ct_table = ct_register_table( 'demo_logs', array(
        'singular'      => 'Log',
        'plural'        => 'Logs',
        'show_ui'       => true,        // Make custom table visible on admin area (check 'views' parameter)
        'show_in_rest'  => true,        // Make custom table visible on rest API
        //'rest_base'  => 'demo-logs',  // Rest base URL, if not defined will user the table name
        'version'       => 1,           // Change the version on schema changes to run the schema auto-updater
        //'primary_key' => 'log_id',    // If not defined will come from schema arg
        'schema'        => array(
            'log_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'title' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'status' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'date' => array(
                'type' => 'datetime',
            )
        ),
        // Also you can define schema as string
//        'schema' => '
//            log_id bigint(20) NOT NULL AUTO_INCREMENT,
//            title varchar(50) NOT NULL,
//            status varchar(50) NOT NULL,
//            date datetime NOT NULL,
//            PRIMARY KEY  (log_id)
//        ',
        'views' => array(
            'add' => array(
                // This will force to the add view just to one column, default is 2
                //'columns' => 1
            )
        ),
        'supports' => array(
            'meta',         // This support automatically generates a new DB table with {table_name}_meta with a similar structure like WP post meta
        )
    ) );

    // Let's to add some demo data
    //$ct_table->db->insert( array( 'title' => 'Log 1' ) );
    //$ct_table->db->insert( array( 'title' => 'Log 2' ) );
    //$ct_table->db->insert( array( 'title' => 'Log 3' ) );

}
add_action( 'ct_init', 'yourprefix_init' );

/* ----------------------------------
 * LIST VIEW -  Examples about some interesting hooks to use on list view
   ---------------------------------- */

// Columns on list view
function yourprefix_manage_demo_logs_columns( $columns = array() ) {

    $columns['title']   = __( 'Title' );
    $columns['status']  = __( 'Status' );
    $columns['date']    = __( 'Date' );

    return $columns;
}
add_filter( 'manage_demo_logs_columns', 'yourprefix_manage_demo_logs_columns' );

/* ----------------------------------
 * ADD/EDIT VIEW - Examples about some interesting hooks to use on add/edit views
   ---------------------------------- */

// Default data when creating a new item (similar to WP auto draft) see ct_insert_object()
function yourprefix_demo_logs_default_data( $default_data = array() ) {

    $default_data['title'] = 'Auto draft';
    $default_data['status'] = 'pending';
    $default_data['date'] = date( 'Y-m-d H:i:s' );

    return $default_data;
}
add_filter( 'ct_demo_logs_default_data', 'yourprefix_demo_logs_default_data' );

// Adding meta boxes to the edit screen
function yourprefix_add_meta_boxes() {

    add_meta_box(
        'demo-meta-box-id',
        __( 'Demo Meta Box', 'textdomain' ),
        'yourprefix_demo_meta_box_callback',
        'demo_logs',
        'normal'
    );

}
add_action( 'add_meta_boxes', 'yourprefix_add_meta_boxes' );

// Meta box render callback
function yourprefix_demo_meta_box_callback( $object ) {
    // Turn stdObject into an array
    $object_data = (array) $object; ?>

    <table class="form-table">

    <?php foreach( $object_data as $field => $value ) :

        // Prevent display the id field
        if( $field === 'log_id' ) {
            continue;
        } ?>

        <tr>
            <th>
                <?php echo ucfirst( $field ); ?>
            </th>
            <td>
                <input type="text" name="<?php echo $field; ?>" value="<?php echo $value; ?>">
            </td>
        </tr>

    <?php endforeach; ?>

    </table>

    <?php
}

/* ----------------------------------
 * CMB2 - Examples about CMB2 compatibility
   ---------------------------------- */

// CMB2 meta box initialization
function yourprefix_cmb2_meta_boxes() {

    $cmb = new_cmb2_box( array(
        'id'           	=> 'cmb-demo-meta-box-id',
        'title'        	=> __( 'CMB2 Demo Meta Box', 'textdomain' ),
        'object_types' 	=> array( 'demo_logs' ),
    ) );

    $cmb->add_field( array(
        'id'         => 'title',
        'name'       => esc_html__( 'Title', 'textdomain' ),
        'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
        'type'       => 'text',
    ) );

    $cmb->add_field( array(
        'id'         => 'status',
        'name'       => esc_html__( 'Status', 'textdomain' ),
        'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
        'type'       => 'text',
    ) );

    // This fields just work if you defined meta as supports on ct_register_table()
    $cmb->add_field( array(
        'id'         => 'yourprefix_meta_field',
        'name'       => esc_html__( 'Meta field', 'textdomain' ),
        'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
        'type'       => 'text',
    ) );

    $cmb->add_field( array(
        'id'         => 'yourprefix_meta_field_2',
        'name'       => esc_html__( 'Meta field 2', 'textdomain' ),
        'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
        'type'       => 'text',
    ) );

}
add_action( 'cmb2_admin_init', 'yourprefix_cmb2_meta_boxes' );

/* ----------------------------------
 * QUERY - As WP_Query, CT has a query class named CT_Query to apply (cached) searches on custom tables
   ---------------------------------- */

//  Fields to apply a search, used on searches ('s' query var)
function yourprefix_demo_logs_search_fields( $search_fields = array() ) {

    $search_fields[] = 'title';
    $search_fields[] = 'status';

    return $search_fields;

}
add_filter( 'ct_query_demo_logs_search_fields', 'yourprefix_demo_logs_search_fields' );

// Custom where, example adding support to 'log__in' and 'log__not_in' query vars
function demo_logs_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'demo_logs' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // Shorthand
    $qv = $ct_query->query_vars;

    // Include
    if( isset( $qv['log__in'] ) && ! empty( $qv['log__in'] ) ) {

        if( is_array( $qv['log__in'] ) ) {
            $include = implode( ", ", $qv['log__in'] );
        } else {
            $include = $qv['log__in'];
        }

        if( ! empty( $include ) ) {
            $where .= " AND {$table_name}.log_id IN ( {$include} )";
        }
    }

    // Exclude
    if( isset( $qv['log__not_in'] ) && ! empty( $qv['log__not_in'] ) ) {

        if( is_array( $qv['log__not_in'] ) ) {
            $exclude = implode( ", ", $qv['log__not_in'] );
        } else {
            $exclude = $qv['log__not_in'];
        }

        if( ! empty( $exclude ) ) {
            $where .= " AND {$table_name}.log_id NOT IN ( {$exclude} )";
        }
    }

    return $where;
}
add_filter( 'ct_query_where', 'demo_logs_query_where', 10, 2 );

/* ----------------------------------
 * REST API - Examples about some interesting hooks to use on rest API
   ---------------------------------- */

// Register the item schema properties (used on create and update endpoints)
function demo_logs_rest_item_schema( $schema ) {

    // Properties
    $schema['properties'] = array_merge( array(
        'log_id'            => array(
            'description'   => __( 'Unique identifier for the object.', 'textdomain' ),
            'type'          => 'integer',
            'context'       => array( 'view', 'edit', 'embed' ),
        ),
        'title'             => array(
            'description'   => __( 'The title for the object.', 'textdomain' ),
            'type'          => 'string',
            'context'       => array( 'view', 'edit', 'embed' ),
        ),
        'status'            => array(
            'description'   => __( 'Status of log for the object.', 'textdomain' ),
            'type'          => 'string',
            'context'       => array( 'view', 'edit', 'embed' ),
            'readonly'      => true,
        ),
        'date'              => array(
            'description'   => __( 'The date the object was created, in the site\'s timezone.', 'textdomain' ),
            'type'          => 'string',
            'format'        => 'date-time',
            'context'       => array( 'view', 'edit', 'embed' ),
        ),
    ), $schema['properties'] );

    return $schema;

}
add_filter( 'ct_rest_demo_logs_schema', 'demo_logs_rest_item_schema' );

// Custom collection params, to make them work check the demo_logs_query_where() example function
// Note: On this example, collection params are 'exclude' and 'include'
// On demo_logs_rest_parameter_mappings() example function will be map them to the real query vars
function demo_logs_rest_collection_params( $query_params, $ct_table ) {

    // Exclude
    $query_params['exclude'] = array(
        'description'        => __( 'Ensure result set excludes specific IDs.', 'textdomain' ),
        'type'               => 'array',
        'items'              => array(
            'type'           => 'integer',
        ),
        'default'            => array(),
    );

    // Include
    $query_params['include'] = array(
        'description'        => __( 'Limit result set to specific IDs.', 'textdomain' ),
        'type'               => 'array',
        'items'              => array(
            'type'           => 'integer',
        ),
        'default'            => array(),
    );


    return $query_params;
}
add_filter( 'ct_rest_demo_logs_collection_params', 'demo_logs_rest_collection_params', 10, 2 );

// Map custom parameters to real query var parameters (check the demo_logs_query_where() example function)
function demo_logs_rest_parameter_mappings( $parameter_mappings, $ct_table, $request ) {

    $parameter_mappings['exclude'] = 'log__not_in';
    $parameter_mappings['include'] = 'log__in';

    return $parameter_mappings;
}
add_filter( 'ct_rest_demo_logs_parameter_mappings', 'demo_logs_rest_parameter_mappings', 10, 3 );

// Custom field sanitization on rest API updates
function demo_logs_rest_sanitize_field_value( $value, $field, $request ) {

    switch( $field ) {
        case 'date':
            // Validate date.
            $mm = substr( $value, 5, 2 );
            $jj = substr( $value, 8, 2 );
            $aa = substr( $value, 0, 4 );
            $valid_date = wp_checkdate( $mm, $jj, $aa, $value );

            if ( ! $valid_date ) {
                return new WP_Error( 'rest_invalid_field', __( 'Invalid date.', 'textdomain' ), array( 'status' => 400 ) );
            }
            break;
    }

    return $value;
}
add_filter( 'ct_rest_demo_logs_sanitize_field_value', 'demo_logs_rest_sanitize_field_value', 10, 3 );