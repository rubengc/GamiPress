<?php
/**
 * Example script about how to setup custom tables with CT.
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 */

/* ----------------------------------
 * INITIALIZATION
   ---------------------------------- */

function yourprefix_init() {

    require_once __DIR__ . '/ct.php';

    ct_register_table( 'demo_logs', array(
        'singular' => 'Log',
        'plural' => 'Logs',
        'show_ui' => true,
        //'primary_key' => 'log_id', // If not defined will come from schema arg
        'version' => 1,
        'schema' => array(
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

}
add_action( 'ct_init', 'yourprefix_init' );

/* ----------------------------------
 * LIST VIEW HOOKS
   ---------------------------------- */

// Columns on list view
function yourprefix_manage_demo_logs_columns( $columns = array() ) {

    $columns['title']   = __( 'Title' );
    $columns['status']  = __( 'Status' );
    $columns['date']    = __( 'Date' );

    return $columns;
}
add_filter( 'manage_demo_logs_columns', 'yourprefix_manage_demo_logs_columns' );


// Fields to apply a search
function yourprefix_demo_logs_search_fields( $search_fields = array() ) {

    $search_fields[] = 'title';
    $search_fields[] = 'status';

    return $search_fields;

}
add_filter( 'ct_query_demo_logs_search_fields', 'yourprefix_demo_logs_search_fields' );

/* ----------------------------------
 * ADD/EDIT VIEW HOOKS
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

/**
 * @param stdClass $object
 */
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