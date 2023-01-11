<?php
/**
 * CT Functions
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register a custom table
 *
 * @since 1.0.0
 *
 * @param string    $name
 * @param array     $args
 *
 * @return CT_Table
 */
function ct_register_table( $name, $args ) {

    global $ct_registered_tables;

    if ( ! is_array( $ct_registered_tables ) ) {
        $ct_registered_tables = array();
    }

    $name = sanitize_key( $name );

    if( isset( $ct_registered_tables[$name] ) ) {
        return $ct_registered_tables[$name];
    }

    $ct_table = new CT_Table( $name, $args );

    $ct_registered_tables[$name] = $ct_table;

    return $ct_table;

}

/**
 * Setup the global table
 *
 * @since 1.0.0
 *
 * @param CT_Table|string $object CT_Table object or CT_Table name
 *
 * @return CT_Table $ct_table
 */
function ct_setup_table( $object ) {

    global $ct_registered_tables, $ct_previous_table, $ct_table;

    if( is_object( $ct_table ) ) {
        $ct_previous_table = $ct_table;
    }

    if( is_object( $object ) ) {
        $ct_table = $object;
    } else if( gettype( $object ) === 'string' && isset( $ct_registered_tables[$object] ) ) {
        $ct_table = $ct_registered_tables[$object];
    }

    return $ct_table;

}

/**
 * Setup the global table
 *
 * @since 1.0.0
 *
 * @return CT_Table $ct_table
 */
function ct_reset_setup_table() {

    global $ct_registered_tables, $ct_previous_table, $ct_table;

    if( is_object( $ct_previous_table ) ) {
        $ct_table = $ct_previous_table;
    }

    return $ct_table;

}

/**
 * get the CT_Table object of given table name
 *
 * @param CT_Table|string $name CT_Table object or CT_Table name
 *
 * @since 1.0.0
 *
 * @return CT_Table
 */
function ct_get_table_object( $name ) {

    global $ct_registered_tables;

    if( is_object( $name ) ) {
        $ct_table = $name;
    } else if( gettype( $name ) === 'string' && isset( $ct_registered_tables[$name] ) ) {
        $ct_table = $ct_registered_tables[$name];
    }

    return $ct_table;
}

/**
 * Get the table labels
 *
 * @since 1.0.0
 *
 * @param CT_Table $ct_table
 *
 * @return array
 */
function ct_get_table_labels( $ct_table ) {

    $default_labels = array(
        'name' => __('%1$s', 'ct'),
        'singular_name' => __('%1$s', 'ct'),
        'plural_name' => __('%2$s', 'ct'),
        'add_new' => __('Add New', 'ct'),
        'add_new_item' => __('Add New %1$s', 'ct'),
        'edit_item' => __('Edit %1$s', 'ct'),
        'new_item' => __('New %1$s', 'ct'),
        'view_item' => __('View %1$s', 'ct'),
        'view_items' => __('View %2$s', 'ct'),
        'search_items' => __( 'Search %2$s', 'ct' ),
        'not_found' => __( 'No %2$s found.', 'ct' ),
        'not_found_in_trash' => __( 'No %2$s found in Trash.', 'ct' ),
        'parent_item_colon' => __( 'Parent %1$s:', 'ct' ),
        'all_items' => __( 'All %2$s', 'ct' ),
        'archives' => __( '%1$s Archives', 'ct' ),
        'attributes' => __( '%1$s Attributes', 'ct' ),
        'insert_into_item' => __( 'Insert into %1$s', 'ct' ),
        'uploaded_to_this_item' => __( 'Uploaded to this post', 'ct' ),
        'featured_image' => __( 'Featured Image', 'ct' ),
        'set_featured_image' => __( 'Set featured image', 'ct' ),
        'remove_featured_image' => __( 'Remove featured image', 'ct' ),
        'use_featured_image' => __( 'Use as featured image', 'ct' ),
        'filter_items_list' => __( 'Filter posts list', 'ct' ),
        'items_list_navigation' => __( '%2$s list navigation', 'ct' ),
        'items_list' => __( '%2$s list', 'ct' ),
    );

    foreach( $default_labels as $label => $pattern ) {
        $default_labels[$label] = sprintf( $pattern, $ct_table->singular, $ct_table->plural );
    }

    return (object) $default_labels;

}

/**
 * Get the table fields
 *
 * @since 1.0.0
 *
 * @param CT_Table $ct_table
 *
 * @return array
 */
function ct_get_table_fields( $ct_table ) {

    return $ct_table->db->schema->fields;

}

/**
 * Execute CT role creation.
 *
 * @since 1.0.0
 */
function ct_populate_roles() {

    // Add caps for Administrator role
    $role = get_role( 'administrator' );

    // Bail if administrator role is not setup
    if( ! $role ) {
        return;
    }

    $args = (object) array(
        'capabilities' => array(),
        'capability_type' => 'item',
        'map_meta_cap' => null
    );

    $capabilities = ct_get_table_capabilities( $args );

    foreach( $capabilities as $cap ) {
        $role->add_cap( $cap );
    }

}

/**
 * Build an object with all tables capabilities out of a table object
 *
 * Post type capabilities use the 'capability_type' argument as a base, if the
 * capability is not set in the 'capabilities' argument array or if the
 * 'capabilities' argument is not supplied.
 *
 * The capability_type argument can optionally be registered as an array, with
 * the first value being singular and the second plural, e.g. array('story, 'stories')
 * Otherwise, an 's' will be added to the value for the plural form. After
 * registration, capability_type will always be a string of the singular value.
 *
 * By default, seven keys are accepted as part of the capabilities array:
 *
 * - edit_item, read_item, and delete_item are meta capabilities, which are then
 *   generally mapped to corresponding primitive capabilities depending on the
 *   context, which would be the item being edited/read/deleted and the user or
 *   role being checked. Thus these capabilities would generally not be granted
 *   directly to users or roles.
 *
 * - edit_items - Controls whether objects of this item type can be edited.
 * - edit_others_items - Controls whether objects of this type owned by other users
 *   can be edited. If the item type does not support an author, then this will
 *   behave like edit_items.
 * - publish_items - Controls publishing objects of this item type.
 * - read_private_items - Controls whether private objects can be read.
 *
 * These four primitive capabilities are checked in core in various locations.
 * There are also seven other primitive capabilities which are not referenced
 * directly in core, except in map_meta_cap(), which takes the three aforementioned
 * meta capabilities and translates them into one or more primitive capabilities
 * that must then be checked against the user or role, depending on the context.
 *
 * - read - Controls whether objects of this item type can be read.
 * - delete_items - Controls whether objects of this item type can be deleted.
 * - delete_private_items - Controls whether private objects can be deleted.
 * - delete_published_items - Controls whether published objects can be deleted.
 * - delete_others_items - Controls whether objects owned by other users can be
 *   can be deleted. If the item type does not support an author, then this will
 *   behave like delete_items.
 * - edit_private_items - Controls whether private objects can be edited.
 * - edit_published_items - Controls whether published objects can be edited.
 *
 * These additional capabilities are only used in map_meta_cap(). Thus, they are
 * only assigned by default if the item type is registered with the 'map_meta_cap'
 * argument set to true (default is false).
 *
 * @since 1.0.0
 *
 * @see register_post_type()
 * @see map_meta_cap()
 *
 * @param object $args Post type registration arguments.
 * @return object object with all the capabilities as member variables.
 */
function ct_get_table_capabilities( $args ) {
    if ( ! is_array( $args->capability_type ) ) {
        $args->capability_type = array( $args->capability_type, $args->capability_type . 's' );
    }

    // Singular base for meta capabilities, plural base for primitive capabilities.
    list( $singular_base, $plural_base ) = $args->capability_type;

    $default_capabilities = array(
        // Meta capabilities
        'edit_item'          => 'edit_'         . $singular_base,
        'read_item'          => 'read_'         . $singular_base,
        'delete_item'        => 'delete_'       . $singular_base,
        'delete_items'       => 'delete_'       . $plural_base,
        // Primitive capabilities used outside of map_meta_cap():
        'edit_items'         => 'edit_'         . $plural_base,
        'edit_others_items'  => 'edit_others_'  . $plural_base,
        'publish_items'      => 'publish_'      . $plural_base,
        'read_private_items' => 'read_private_' . $plural_base,
    );

    // Primitive capabilities used within map_meta_cap():
    if ( $args->map_meta_cap ) {
        $default_capabilities_for_mapping = array(
            'read'                   => 'read',
            'delete_items'           => 'delete_'           . $plural_base,
            'delete_private_items'   => 'delete_private_'   . $plural_base,
            'delete_published_items' => 'delete_published_' . $plural_base,
            'delete_others_items'    => 'delete_others_'    . $plural_base,
            'edit_private_items'     => 'edit_private_'     . $plural_base,
            'edit_published_items'   => 'edit_published_'   . $plural_base,
            'add_post_meta'          => 'add_'   . $singular_base . '_meta',
            'edit_post_meta'         => 'edit_'   . $singular_base . '_meta',
            'delete_post_meta'       => 'delete_'   . $singular_base . '_meta',
        );
        $default_capabilities = array_merge( $default_capabilities, $default_capabilities_for_mapping );
    }

    $capabilities = array_merge( $default_capabilities, $args->capabilities );

    // Post creation capability simply maps to edit_items by default:
    if ( ! isset( $capabilities['create_items'] ) ) {
        $capabilities['create_items'] = $capabilities['edit_items'];
    }

    // Remember meta capabilities for future reference.
    if ( $args->map_meta_cap ) {
        _ct_meta_capabilities( $capabilities );
    }

    // Shortcut to override all capabilities by a specific capability
    if( property_exists( $args, 'capability' ) && ! empty( $args->capability ) ) {
        foreach ( $capabilities as $key => $value ) {
            $capabilities[$key] = $args->capability;
        }
    }

    return (object) $capabilities;
}

/**
 * Store or return a list of table meta caps for map_meta_cap().
 *
 * @since 1.0.0
 * @access private
 *
 * @global array $post_type_meta_caps Used to store meta capabilities.
 *
 * @param array $capabilities Post type meta capabilities.
 */
function _ct_meta_capabilities( $capabilities = null ) {
    global $ct_meta_caps;

    foreach ( $capabilities as $core => $custom ) {
        if ( in_array( $core, array( 'read_item', 'delete_item', 'edit_item' ) ) ) {
            $ct_meta_caps[ $custom ] = $core;
        }
    }
}

/**
 * Retrieves object data given a object primary key or object object.
 *
 * @since 1.0.0
 *
 * @param int|stdClass|null $object   Optional. Object primary key or object object.
 * @param string           $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                 a WP_Post object, an associative array, or a numeric array, respectively. Default OBJECT.
 * @param string           $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db',
 *                                 or 'display'. Default 'raw'.
 * @return stdClass|array|null Type corresponding to $output on success or null on failure.
 *                            When $output is OBJECT, a `stdClass` instance is returned.
 */
function ct_get_object( $object = null, $output = OBJECT, $filter = 'raw' ) {

    global $wpdb, $ct_table;

    if ( is_object( $object ) ) {
        $primary_key = $ct_table->db->primary_key;

        $object = ct_get_object_instance( $object->$primary_key );
    } else {
        $object = ct_get_object_instance( $object );
    }

    if ( ! $object )
        return null;

    if ( $output == ARRAY_A )
        return (array) $object;
    elseif ( $output == ARRAY_N )
        return array_values( (array) $object );

    return $object;

}

/**
 * Retrieve the object instance.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $object_id Object primary key.
 * @return stdClass|false Object std object, false otherwise.
 */
function ct_get_object_instance( $object_id = null ) {

    global $wpdb, $ct_table;

    $object_id = (int) $object_id;
    if ( ! $object_id ) {
        return false;
    }

    $object = wp_cache_get( $object_id, $ct_table->name );

    if ( ! $object ) {
        $object = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ct_table->db->table_name} WHERE {$ct_table->db->primary_key} = %d LIMIT 1", $object_id ) );

        if ( ! $object )
            return false;

        wp_cache_add( $object_id, $object, $ct_table->name );
    }

    return $object;

}

/**
 * Will clean the object in the cache.
 *
 * Cleaning means delete from the cache of the object.
 *
 * This function not run if $_wp_suspend_cache_invalidation is not empty. See wp_suspend_cache_invalidation().
 *
 * @since 1.0.0
 *
 * @global bool $_wp_suspend_cache_invalidation
 *
 * @param int|Object $object Post ID or post object to remove from the cache.
 */
function ct_clean_object_cache( $object ) {
    global $_wp_suspend_cache_invalidation, $ct_table;

    if ( ! empty( $_wp_suspend_cache_invalidation ) )
        return;

    $object = ct_get_object( $object );
    if ( empty( $object ) )
        return;

    $primary_key = $ct_table->db->primary_key;

    wp_cache_delete( $object->$primary_key, $ct_table->name );

    //wp_cache_delete( 'wp_get_archives', 'general' );

    /**
     * Fires immediately after the given object's cache is cleaned.
     *
     * @since 1.0.0
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     */
    do_action( 'ct_clean_object_cache', $object->$primary_key, $object );

    wp_cache_set( 'last_changed', microtime(), $ct_table->name );
}

/**
 * Update an object with new data.
 *
 * The date does not have to be set for drafts. You can set the date and it will
 * not be overridden.
 *
 * @since 1.0.0
 *
 * @param array|object $object_data  Optional. Object data. Arrays are expected to be escaped, objects are not. Default array.
 * @param bool         $wp_error Optional. Allow return of WP_Error on failure. Default false.
 * @return int|WP_Error The value 0 or WP_Error on failure. The post ID on success.
 */
function ct_update_object( $object_data = array(), $wp_error = false ) {

    global $ct_table;

    if ( is_object( $object_data ) ) {
        // Non-escaped post was passed.
        $object_data = get_object_vars( $object_data );
        $object_data = wp_slash( $object_data );
    }

    $primary_key = $ct_table->db->primary_key;

    // First, get all of the original fields.
    $object = ct_get_object( $object_data[$primary_key], ARRAY_A );

    if ( is_null( $object ) ) {
        if ( $wp_error )
            return new WP_Error( 'invalid_object', __( 'Invalid object ID.' ) );
        return 0;
    }

    // Escape data pulled from DB.
    $object = wp_slash( $object );

    // Merge old and new fields with new fields overwriting old ones.
    $object_data = array_merge( $object, $object_data );

    return ct_insert_object( $object_data, $wp_error );

}

/**
 * Insert or update an object.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @global CT_Table $ct_table Custom Tables CT_Table object type.
 *
 * @param array $object_data An array of elements that make up a post to update or insert.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @return int|WP_Error The post ID on success. The value 0 or WP_Error on failure.
 */
function ct_insert_object( $object_data, $wp_error = false ) {

    global $wpdb, $ct_table;

    if( ! is_a( $ct_table, 'CT_Table' ) ) {
        return new WP_Error( 'invalid_ct_table', __( 'Invalid CT Table object.' ) );
    }

    /**
     * Setup the default object for the add new view.
     *
     * The dynamic portion of the hook, `$this->name`, refers to the object type of the object.
     *
     * @since 1.0.0
     *
     * @param array $default_data Default data to be filtered.
     */
    $defaults = apply_filters( "ct_{$ct_table->name}_default_data", array() );

    $object_data = wp_parse_args( $object_data, $defaults );

    // Are we updating or creating?
    $object_id = 0;
    $original_object_data = array();
    $update = false;
    $primary_key = $ct_table->db->primary_key;

    if ( ! empty( $object_data[$primary_key] ) ) {
        $update = true;

        // Get the object ID.
        $object_id = $object_data[$primary_key];
        $object_before = ct_get_object( $object_id );
        $original_object_data = ct_get_object( $object_id, ARRAY_A );

        if ( is_null( $original_object_data ) ) {

            if ( $wp_error ) {
                return new WP_Error( 'invalid_object', __( 'Invalid object ID.' ) );
            }

            return 0;
        }
    }

    /**
     * Filters slashed post data just before it is inserted into the database.
     *
     * @since 1.0.0
     *
     * @param array $object_data    An array with new object data.
     * @param array $original_object_data An array with the original object data.
     */
    $object_data = apply_filters( 'ct_insert_object_data', $object_data, $original_object_data );

    $object_data = wp_unslash( $object_data );

    $where = array( $primary_key => $object_id );

    if ( $update ) {

        /**
         * Fires immediately before an existing object is updated in the database.
         *
         * @since 1.0.0
         *
         * @param int   $object_id  Object ID.
         * @param array $data       Array of unslashed object data.
         */
        do_action( 'pre_object_update', $object_id, $object_data );

        if ( false === $ct_table->db->update( $object_data, $where ) ) {
            if ( $wp_error ) {
                return new WP_Error('db_update_error', __('Could not update object in the database'), $wpdb->last_error);
            } else {
                return 0;
            }
        }
    } else {

        $import_id = isset( $object_data['import_id'] ) ? $object_data['import_id'] : 0;

        // If there is a suggested ID, use it if not already present.
        if ( ! empty( $import_id ) ) {

            $import_id = (int) $import_id;
            if ( ! $wpdb->get_var( $wpdb->prepare("SELECT {$primary_key} FROM {$ct_table->db->table_name} WHERE {$primary_key} = %d", $import_id) ) ) {
                $object_data[$primary_key] = $import_id;
            }
        }

        if ( false === $wpdb->insert( $ct_table->db->table_name, $object_data ) ) {

            if ( $wp_error ) {
                return new WP_Error('db_insert_error', __('Could not insert object into the database'), $wpdb->last_error);
            } else {
                return 0;
            }

        }

        $object_id = (int) $wpdb->insert_id;
    }

    // If isset meta_input and object supports meta, then add meta data
    if ( ! empty( $object_data['meta_input'] ) && $ct_table->meta ) {
        foreach ( $object_data['meta_input'] as $field => $value ) {
            ct_update_object_meta( $object_id, $field, $value );
        }
    }

    ct_clean_object_cache( $object_id );

    $object = ct_get_object( $object_id );

    if ( $update ) {
        /**
         * Fires once an existing post has been updated.
         *
         * @since 1.0.0
         *
         * @param int     $post_ID Post ID.
         * @param WP_Post $post    Post object.
         */
        do_action( 'ct_edit_object', $object_id, $object );

        $object_after = ct_get_object( $object_id );

        /**
         * Fires once an existing post has been updated.
         *
         * @since 1.0.0
         *
         * @param int     $object_id      Object ID.
         * @param WP_Post $object_after   Object following the update.
         * @param WP_Post $object_before  Object before the update.
         */
        do_action( 'ct_object_updated', $object_id, $object_after, $object_before);
    }

    /**
     * Fires once a object has been saved.
     *
     * The dynamic portion of the hook name, `{$ct_table->name}`, refers to the object type.
     *
     * @since 1.0.0
     *
     * @param int       $object_id    Object ID.
     * @param stdClass  $object       Object.
     * @param bool      $update       Whether this is an existing object being updated or not.
     */
    do_action( "ct_save_object_{$ct_table->name}", $object_id, $object, $update );

    /**
     * Fires once a post has been saved.
     *
     * @since 1.0.0
     *
     * @param int       $object_id    Object ID.
     * @param stdClass  $object       Object.
     * @param bool      $update       Whether this is an existing object being updated or not.
     */
    do_action( 'ct_save_object', $object_id, $object, $update );

    /**
     * Fires once a post has been saved.
     *
     * @since 1.0.0
     *
     * @param int       $object_id    Object ID.
     * @param stdClass  $object       Object.
     * @param bool      $update       Whether this is an existing object being updated or not.
     */
    do_action( 'ct_insert_object', $object_id, $object, $update );

    return $object_id;
}

/**
 * Trash or delete an object.
 *
 * When the post and page is permanently deleted, everything that is tied to
 * it is deleted also. This includes comments, post meta fields, and terms
 * associated with the post.
 *
 * The post or page is moved to trash instead of permanently deleted unless
 * trash is disabled, item is already in the trash, or $force_delete is true.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @see wp_delete_attachment()
 * @see wp_trash_post()
 *
 * @param int  $object_id    Optional. Object ID. Default 0.
 * @param bool $force_delete Optional. Whether to bypass trash and force deletion.
 *                           Default false.
 * @return WP_Post|false|null Post data on success, false or null on failure.
 */
function ct_delete_object( $object_id = 0, $force_delete = false ) {

    global $wpdb, $ct_table;

    if( ! is_a( $ct_table, 'CT_Table' ) ) {
        return new WP_Error( 'invalid_ct_table', __( 'Invalid CT Table object.' ) );
    }

    $ct_object = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ct_table->db->table_name} WHERE {$ct_table->db->primary_key} = %d", $object_id ) );

    if ( ! $ct_object ) {
        return $ct_object;
    }

    $ct_object = ct_get_object( $ct_object );

    // TODO: Add support for trash functionality
    //if ( ! $force_delete && ( 'post' === $post->post_type || 'page' === $post->post_type ) && 'trash' !== get_post_status( $postid ) && EMPTY_TRASH_DAYS ) {
        //return ct_trash_object( $object_id );
    //}

    /**
     * Filters whether a post deletion should take place.
     *
     * @since 1.0.0
     *
     * @param bool    $delete       Whether to go forward with deletion.
     * @param WP_Post $post         Post object.
     * @param bool    $force_delete Whether to bypass the trash.
     */
    $check = apply_filters( 'pre_delete_object', null, $ct_object, $force_delete );
    if ( null !== $check ) {
        return $check;
    }

    /**
     * Fires before a post is deleted, at the start of ct_delete_object().
     *
     * @since 1.0.0
     *
     * @see ct_delete_object()
     *
     * @param int $object_id Object ID.
     */
    do_action( 'before_delete_object', $object_id );

    if( $ct_table->meta ) {
        ct_delete_object_meta( $object_id, '_wp_trash_meta_status' );
        ct_delete_object_meta( $object_id, '_wp_trash_meta_time' );

        $object_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT {$ct_table->meta->db->primary_key} FROM {$ct_table->meta->db->table_name} WHERE {$ct_table->db->primary_key} = %d ", $object_id ));

        foreach ( $object_meta_ids as $mid ) {
            ct_delete_metadata_by_mid( 'post', $mid );
        }
    }

    /**
     * Fires immediately before a post is deleted from the database.
     *
     * @since 1.0.0
     *
     * @param int $object_id Object ID.
     */
    do_action( 'delete_object', $object_id );

    $result = $ct_table->db->delete( $object_id );

    if ( ! $result ) {
        return false;
    }

    /**
     * Fires immediately after a post is deleted from the database.
     *
     * @since 1.0.0
     *
     * @param int $object_id Object ID.
     */
    do_action( 'deleted_object', $object_id );

    ct_clean_object_cache( $ct_object );

    /**
     * Fires after a post is deleted, at the conclusion of ct_delete_object().
     *
     * @since 1.0.0
     *
     * @see ct_delete_object()
     *
     * @param int $object_id Object ID.
     */
    do_action( 'after_delete_object', $object_id );

    return $ct_object;
}

//
// Object meta functions
//

/**
 * Add meta data field to an object.
 *
 * Object meta data is called "Custom Fields" on the Administration Screen.
 *
 * @since 1.0.0
 *
 * @param int    $object_id  Object ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added. Default false.
 * @return int|false Meta ID on success, false on failure.
 */
function ct_add_object_meta( $object_id, $meta_key, $meta_value, $unique = false ) {

    global $wpdb, $ct_table;

    // Bail if CT_Table not supports meta data
    if( ! $ct_table->meta ) {
        return false;
    }

    $object_id = absint( $object_id );
    if ( ! $object_id ) {
        return false;
    }

    $primary_key = $ct_table->db->primary_key;
    $meta_primary_key = $ct_table->meta->db->primary_key;
    $meta_table_name = $ct_table->meta->db->table_name;

    // expected_slashed ($meta_key)
    $meta_key = wp_unslash($meta_key);
    $meta_value = wp_unslash($meta_value);
    $meta_value = sanitize_meta( $meta_key, $meta_value, $ct_table->name );

    /**
     * Filters whether to add metadata of a specific type.
     *
     * The dynamic portion of the hook, `$meta_type`, refers to the meta
     * object type (comment, post, or user). Returning a non-null value
     * will effectively short-circuit the function.
     *
     * @since 1.0.0
     *
     * @param null|bool $check      Whether to allow adding metadata for the given type.
     * @param int       $object_id  Object ID.
     * @param string    $meta_key   Meta key.
     * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
     * @param bool      $unique     Whether the specified meta key should be unique
     *                              for the object. Optional. Default false.
     */
    $check = apply_filters( "add_{$ct_table->name}_metadata", null, $object_id, $meta_key, $meta_value, $unique );
    if ( null !== $check )
        return $check;

    if ( $unique && $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $meta_table_name WHERE meta_key = %s AND $primary_key = %d",
            $meta_key, $object_id ) ) )
        return false;

    $_meta_value = $meta_value;
    $meta_value = maybe_serialize( $meta_value );

    /**
     * Fires immediately before meta of a specific type is added.
     *
     * The dynamic portion of the hook, `$meta_type`, refers to the meta
     * object type (comment, post, or user).
     *
     * @since 1.0.0
     *
     * @param int    $object_id  Object ID.
     * @param string $meta_key   Meta key.
     * @param mixed  $meta_value Meta value.
     */
    do_action( "add_{$ct_table->name}_meta", $object_id, $meta_key, $_meta_value );

    $result = $wpdb->insert( $meta_table_name, array(
        $primary_key => $object_id,
        'meta_key' => $meta_key,
        'meta_value' => $meta_value
    ) );

    if ( ! $result )
        return false;

    $mid = (int) $wpdb->insert_id;

    wp_cache_delete( $object_id, $ct_table->meta->name );

    /**
     * Fires immediately after meta of a specific type is added.
     *
     * The dynamic portion of the hook, `$meta_type`, refers to the meta
     * object type (comment, post, or user).
     *
     * @since 1.0.0
     *
     * @param int    $mid        The meta ID after successful update.
     * @param int    $object_id  Object ID.
     * @param string $meta_key   Meta key.
     * @param mixed  $meta_value Meta value.
     */
    do_action( "added_{$ct_table->name}_meta", $mid, $object_id, $meta_key, $_meta_value );

    return $mid;
}

/**
 * Remove metadata matching criteria from a post.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @since 1.0.0
 *
 * @param int    $object_id    Post ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if
 *                           non-scalar. Default empty.
 * @return bool True on success, false on failure.
 */
function ct_delete_object_meta( $object_id, $meta_key, $meta_value = '' ) {

    global $wpdb, $ct_table;

    // Bail if CT_Table not supports meta data
    if( ! $ct_table->meta ) {
        return false;
    }

    $object_id = absint( $object_id );
    if ( ! $object_id ) {
        return false;
    }

    $primary_key = $ct_table->db->primary_key;
    $meta_primary_key = $ct_table->meta->db->primary_key;
    $meta_table_name = $ct_table->meta->db->table_name;

    // expected_slashed ($meta_key)
    $meta_key = wp_unslash($meta_key);
    $meta_value = wp_unslash($meta_value);
    $delete_all = false;

    /**
     * Filters whether to delete metadata of a specific type.
     *
     * The dynamic portion of the hook, `$meta_type`, refers to the meta
     * object type (comment, post, or user). Returning a non-null value
     * will effectively short-circuit the function.
     *
     * @since 1.0.0
     *
     * @param null|bool $delete     Whether to allow metadata deletion of the given type.
     * @param int       $object_id  Object ID.
     * @param string    $meta_key   Meta key.
     * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
     * @param bool      $delete_all Whether to delete the matching metadata entries
     *                              for all objects, ignoring the specified $object_id.
     *                              Default false.
     */
    $check = apply_filters( "delete_{$ct_table->name}_metadata", null, $object_id, $meta_key, $meta_value, $delete_all );
    if ( null !== $check )
        return (bool) $check;

    $_meta_value = $meta_value;
    $meta_value = maybe_serialize( $meta_value );

    $query = $wpdb->prepare( "SELECT $meta_primary_key FROM $meta_table_name WHERE meta_key = %s", $meta_key );

    if ( !$delete_all )
        $query .= $wpdb->prepare(" AND $primary_key = %d", $object_id );

    if ( '' !== $meta_value && null !== $meta_value && false !== $meta_value )
        $query .= $wpdb->prepare(" AND meta_value = %s", $meta_value );

    $meta_ids = $wpdb->get_col( $query );
    if ( !count( $meta_ids ) )
        return false;

    if ( $delete_all ) {
        $value_clause = '';
        if ( '' !== $meta_value && null !== $meta_value && false !== $meta_value ) {
            $value_clause = $wpdb->prepare( " AND meta_value = %s", $meta_value );
        }

        $object_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $meta_primary_key FROM $meta_table_name WHERE meta_key = %s $value_clause", $meta_key ) );
    }

    /**
     * Fires immediately before deleting metadata of a specific type.
     *
     * The dynamic portion of the hook, `$meta_type`, refers to the meta
     * object type (comment, post, or user).
     *
     * @since 1.0.0
     *
     * @param array  $meta_ids   An array of metadata entry IDs to delete.
     * @param int    $object_id  Object ID.
     * @param string $meta_key   Meta key.
     * @param mixed  $meta_value Meta value.
     */
    do_action( "delete_{$ct_table->name}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

    $query = "DELETE FROM $meta_table_name WHERE $meta_primary_key IN( " . implode( ',', $meta_ids ) . " )";

    $count = $wpdb->query($query);

    if ( !$count )
        return false;

    if ( $delete_all ) {
        foreach ( (array) $object_ids as $o_id ) {
            wp_cache_delete( $o_id, $ct_table->meta->name );
        }
    } else {
        wp_cache_delete( $object_id, $ct_table->meta->name );
    }

    /**
     * Fires immediately after deleting metadata of a specific type.
     *
     * The dynamic portion of the hook name, `$meta_type`, refers to the meta
     * object type (comment, post, or user).
     *
     * @since 1.0.0
     *
     * @param array  $meta_ids   An array of deleted metadata entry IDs.
     * @param int    $object_id  Object ID.
     * @param string $meta_key   Meta key.
     * @param mixed  $meta_value Meta value.
     */
    do_action( "deleted_{$ct_table->name}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

    return true;
}

/**
 * Retrieve object meta field for an object.
 *
 * @since 1.0.0
 *
 * @param int    $object_id Post ID.
 * @param string $meta_key     Optional. The meta key to retrieve. By default, returns
 *                        data for all keys. Default empty.
 * @param bool   $single  Optional. Whether to return a single value. Default false.
 * @return mixed Will be an array if $single is false. Will be value of meta data
 *               field if $single is true.
 */
function ct_get_object_meta( $object_id, $meta_key = '', $single = false ) {

    global $wpdb, $ct_table;

    // Bail if CT_Table not supports meta data
    if( ! $ct_table->meta ) {
        return false;
    }

    $object_id = absint( $object_id );
    if ( ! $object_id ) {
        return false;
    }

    /**
     * Filters whether to retrieve metadata of a specific type.
     *
     * The dynamic portion of the hook, `$meta_type`, refers to the meta
     * object type (comment, post, or user). Returning a non-null value
     * will effectively short-circuit the function.
     *
     * @since 1.0.0
     *
     * @param null|array|string $value     The value get_metadata() should return - a single metadata value,
     *                                     or an array of values.
     * @param int               $object_id Object ID.
     * @param string            $meta_key  Meta key.
     * @param bool              $single    Whether to return only the first value of the specified $meta_key.
     */
    $check = apply_filters( "get_{$ct_table->name}_metadata", null, $object_id, $meta_key, $single );
    if ( null !== $check ) {
        if ( $single && is_array( $check ) )
            return $check[0];
        else
            return $check;
    }

    $meta_cache = wp_cache_get( $object_id, $ct_table->meta->name );

    if ( !$meta_cache ) {
        $meta_cache = ct_update_meta_cache( $ct_table->meta->name, array( $object_id ) );
        $meta_cache = $meta_cache[$object_id];
    }

    if ( ! $meta_key ) {
        return $meta_cache;
    }

    if ( isset($meta_cache[$meta_key]) ) {
        if ( $single )
            return maybe_unserialize( $meta_cache[$meta_key][0] );
        else
            return array_map('maybe_unserialize', $meta_cache[$meta_key]);
    }

    if ( $single )
        return '';
    else
        return array();
}

/**
 * Update post meta field based on post ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and post ID.
 *
 * If the meta field for the post does not exist, it will be added.
 *
 * @since 1.0.0
 *
 * @param int    $object_id    Object ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 *                           Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure.
 */
function ct_update_object_meta( $object_id, $meta_key, $meta_value, $prev_value = '' ) {

    global $wpdb, $ct_table;

    if( ! is_a( $ct_table, 'CT_Table' ) ) {
        return false;
    }

    // Bail if CT_Table not supports meta data
    if( ! $ct_table->meta ) {
        return false;
    }

    $object_id = absint( $object_id );
    if ( ! $object_id ) {
        return false;
    }

    $primary_key = $ct_table->db->primary_key;
    $meta_primary_key = $ct_table->meta->db->primary_key;
    $meta_table_name = $ct_table->meta->db->table_name;

    // Keep original values
    $raw_meta_key = $meta_key;
    $passed_value = $meta_value;

    // Sanitize vars
    $meta_key = wp_unslash( $meta_key );
    $meta_value = wp_unslash( $meta_value );
    $meta_value = sanitize_meta( $meta_key, $meta_value, $ct_table->name );

    /**
     * Filters whether to update metadata of a specific type.
     *
     * The dynamic portion of the hook, `$ct_table->name`, refers to the meta object type.
     * Returning a non-null value will effectively short-circuit the function.
     *
     * @since 1.0.0
     *
     * @param null|bool $check      Whether to allow updating metadata for the given type.
     * @param int       $object_id  Object ID.
     * @param string    $meta_key   Meta key.
     * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
     * @param mixed     $prev_value Optional. If specified, only update existing
     *                              metadata entries with the specified value.
     *                              Otherwise, update all entries.
     */
    $check = apply_filters( "update_{$ct_table->name}_metadata", null, $object_id, $meta_key, $meta_value, $prev_value );
    if ( null !== $check )
        return (bool) $check;

    // Compare existing value to new value if no prev value given and the key exists only once.
    if ( empty($prev_value) ) {
        $old_value = ct_get_object_meta( $object_id, $meta_key );
        if ( count( $old_value ) == 1 ) {
            if ( $old_value[0] === $meta_value )
                return false;
        }
    }

    $meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT {$meta_primary_key} FROM $meta_table_name WHERE meta_key = %s AND $primary_key = %d", $meta_key, $object_id ) );
    if ( empty( $meta_ids ) ) {
        return ct_add_object_meta( $object_id, $raw_meta_key, $passed_value );
    }

    $_meta_value = $meta_value;
    $meta_value = maybe_serialize( $meta_value );

    $data  = compact( 'meta_value' );
    $where = array(
        $primary_key => $object_id,
        'meta_key' => $meta_key
    );

    if ( ! empty( $prev_value ) ) {
        $prev_value = maybe_serialize( $prev_value );
        $where['meta_value'] = $prev_value;
    }

    foreach ( $meta_ids as $meta_id ) {
        /**
         * Fires immediately before updating metadata of a specific type.
         *
         * The dynamic portion of the hook, `$meta_type`, refers to the meta
         * object type (comment, post, or user).
         *
         * @since 1.0.0
         *
         * @param int    $meta_id    ID of the metadata entry to update.
         * @param int    $object_id  Object ID.
         * @param string $meta_key   Meta key.
         * @param mixed  $meta_value Meta value.
         */
        do_action( "update_{$ct_table->name}_meta", $meta_id, $object_id, $meta_key, $_meta_value );
    }

    $result = $ct_table->meta->db->update( $data, $where );

    if ( ! $result )
        return false;

    wp_cache_delete( $object_id, $ct_table->meta->name );

    foreach ( $meta_ids as $meta_id ) {
        /**
         * Fires immediately after updating metadata of a specific type.
         *
         * The dynamic portion of the hook, `$meta_type`, refers to the meta
         * object type (comment, post, or user).
         *
         * @since 1.0.0
         *
         * @param int    $meta_id    ID of updated metadata entry.
         * @param int    $object_id  Object ID.
         * @param string $meta_key   Meta key.
         * @param mixed  $meta_value Meta value.
         */
        do_action( "updated_{$ct_table->name}_meta", $meta_id, $object_id, $meta_key, $_meta_value );
    }

    return true;
}

/**
 * Update the metadata cache for the specified objects.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string    $meta_type  Type of object metadata is for (e.g., comment, post, or user)
 * @param int|array $object_ids Array or comma delimited list of object IDs to update cache for
 * @return array|false Metadata cache for the specified objects, or false on failure.
 */
function ct_update_meta_cache( $meta_type, $object_ids) {

    global $wpdb, $ct_table;

    if ( ! $meta_type || ! $object_ids ) {
        return false;
    }

    // Bail if CT_Table not supports meta data
    if( ! $ct_table->meta ) {
        return false;
    }

    // Setup vars
    $primary_key = $ct_table->db->primary_key;
    $meta_primary_key = $ct_table->meta->db->primary_key;
    $meta_table_name = $ct_table->meta->db->table_name;

    if ( !is_array($object_ids) ) {
        $object_ids = preg_replace('|[^0-9,]|', '', $object_ids);
        $object_ids = explode(',', $object_ids);
    }

    $object_ids = array_map('intval', $object_ids);

    $cache_key = $meta_table_name;
    $ids = array();
    $cache = array();
    foreach ( $object_ids as $id ) {
        $cached_object = wp_cache_get( $id, $cache_key );
        if ( false === $cached_object )
            $ids[] = $id;
        else
            $cache[$id] = $cached_object;
    }

    if ( empty( $ids ) )
        return $cache;

    // Get meta info
    $id_list = join( ',', $ids );

    $meta_list = $wpdb->get_results( "SELECT {$primary_key}, meta_key, meta_value FROM {$meta_table_name} WHERE {$primary_key} IN ($id_list) ORDER BY {$meta_primary_key} ASC", ARRAY_A );

    if ( !empty($meta_list) ) {
        foreach ( $meta_list as $metarow) {
            $mpid = intval($metarow[$primary_key]);
            $mkey = $metarow['meta_key'];
            $mval = $metarow['meta_value'];

            // Force subkeys to be array type:
            if ( !isset($cache[$mpid]) || !is_array($cache[$mpid]) )
                $cache[$mpid] = array();
            if ( !isset($cache[$mpid][$mkey]) || !is_array($cache[$mpid][$mkey]) )
                $cache[$mpid][$mkey] = array();

            // Add a value to the current pid/key:
            $cache[$mpid][$mkey][] = $mval;
        }
    }

    foreach ( $ids as $id ) {
        if ( ! isset($cache[$id]) )
            $cache[$id] = array();
        wp_cache_add( $id, $cache[$id], $cache_key );
    }

    return $cache;
}

/**
 * Get meta data by meta ID
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $meta_id   ID for a specific meta row
 * @return object|false Meta object or false.
 */
function ct_get_metadata_by_mid( $meta_type, $meta_id ) {

    global $wpdb, $ct_table;

    if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
        return false;
    }

    $meta_id = intval( $meta_id );

    if ( $meta_id <= 0 ) {
        return false;
    }

    // Bail if CT_Table not supports meta data
    if( ! $ct_table->meta ) {
        return false;
    }

    // Setup vars
    $meta_primary_key = $ct_table->meta->db->primary_key;
    $meta_table_name = $ct_table->meta->db->table_name;

    $meta = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $meta_table_name WHERE $meta_primary_key = %d", $meta_id ) );

    if ( empty( $meta ) )
        return false;

    if ( isset( $meta->meta_value ) )
        $meta->meta_value = maybe_unserialize( $meta->meta_value );

    return $meta;

}

/**
 * Delete meta data by meta ID
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $meta_id   ID for a specific meta row
 * @return bool True on successful delete, false on failure.
 */
function ct_delete_metadata_by_mid( $meta_type, $meta_id ) {

    global $wpdb, $ct_table;

    // Make sure everything is valid.
    if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
        return false;
    }

    $meta_id = intval( $meta_id );

    if ( $meta_id <= 0 ) {
        return false;
    }

    // Bail if CT_Table not supports meta data
    if( ! $ct_table->meta ) {
        return false;
    }

    // Setup vars
    $meta_primary_key = $ct_table->meta->db->primary_key;
    $meta_table_name = $ct_table->meta->db->table_name;

    // Fetch the meta and go on if it's found.
    if ( $meta = ct_get_metadata_by_mid( $meta_type, $meta_id ) ) {
        $object_id = $meta->{$meta_primary_key};

        /** This action is documented in wp-includes/meta.php */
        do_action( "delete_{$meta_type}_meta", (array) $meta_id, $object_id, $meta->meta_key, $meta->meta_value );

        // Run the query, will return true if deleted, false otherwise
        $result = (bool) $wpdb->delete( $meta_table_name, array( $meta_primary_key => $meta_id ) );

        // Clear the caches.
        wp_cache_delete($object_id, $meta_type . '_meta');

        /** This action is documented in wp-includes/meta.php */
        do_action( "deleted_{$meta_type}_meta", (array) $meta_id, $object_id, $meta->meta_key, $meta->meta_value );

        return $result;

    }

    // Meta id was not found.
    return false;
}

/**
 * Get meta data for the given object ID.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $object_id
 * @return mixed
 */
function ct_has_meta( $object_id ) {

    global $wpdb, $ct_table;

    $primary_key = $ct_table->db->primary_key;
    $meta_primary_key = $ct_table->meta->db->primary_key;
    $meta_table_name = $ct_table->meta->db->table_name;

    return $wpdb->get_results( $wpdb->prepare(
        "SELECT meta_key, meta_value, meta_id, {$primary_key}
         FROM {$meta_table_name} WHERE {$primary_key} = %d
		 ORDER BY meta_key,meta_id",
        $object_id ), ARRAY_A );
}

/**
 * Prints the form in the Custom Fields meta box.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param stdClass $object Optional. The post being edited.
 */
function ct_meta_form( $object = null ) {

    global $wpdb, $ct_table;

    $primary_key = $ct_table->db->primary_key;
    $object = ct_get_object( $object );

    /**
     * Filters values for the meta key dropdown in the Custom Fields meta box.
     *
     * Returning a non-null value will effectively short-circuit and avoid a
     * potentially expensive query against postmeta.
     *
     * @since 1.0.0
     *
     * @param array|null $keys Pre-defined meta keys to be used in place of a postmeta query. Default null.
     * @param stdClass    $object The current object.
     */
    $keys = apply_filters( "{$ct_table->name}_meta_form_keys", null, $object );

    if ( null === $keys ) {
        /**
         * Filters the number of custom fields to retrieve for the drop-down
         * in the Custom Fields meta box.
         *
         * @since 1.0.0
         *
         * @param int $limit Number of custom fields to retrieve. Default 30.
         */
        $limit = apply_filters( "{$ct_table->name}_meta_form_limit", 30 );
        $sql = "SELECT DISTINCT meta_key
			FROM {$ct_table->meta->db->table_name}
			WHERE meta_key NOT BETWEEN '_' AND '_z'
			HAVING meta_key NOT LIKE %s
			ORDER BY meta_key
			LIMIT %d";
        $keys = $wpdb->get_col( $wpdb->prepare( $sql, $wpdb->esc_like( '_' ) . '%', $limit ) );
    }

    if ( $keys ) {
        natcasesort( $keys );
        $meta_key_input_id = 'metakeyselect';
    } else {
        $meta_key_input_id = 'metakeyinput';
    }
    ?>
    <p><strong><?php _e( 'Add New Custom Field:' ) ?></strong></p>
    <table id="newmeta">
        <thead>
        <tr>
            <th class="left"><label for="<?php echo $meta_key_input_id; ?>"><?php _ex( 'Name', 'meta name' ) ?></label></th>
            <th><label for="metavalue"><?php _e( 'Value' ) ?></label></th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td id="newmetaleft" class="left">
                <?php if ( $keys ) { ?>
                    <select id="metakeyselect" name="metakeyselect">
                        <option value="#NONE#"><?php _e( '&mdash; Select &mdash;' ); ?></option>
                        <?php

                        foreach ( $keys as $key ) {
                            if ( is_protected_meta( $key, 'post' ) || ! current_user_can( $ct_table->cap->add_post_meta, $object->$primary_key, $key ) )
                                continue;
                            echo "\n<option value='" . esc_attr($key) . "'>" . esc_html($key) . "</option>";
                        }
                        ?>
                    </select>
                    <input class="hide-if-js" type="text" id="metakeyinput" name="metakeyinput" value="" />
                    <a href="#postcustomstuff" class="hide-if-no-js" onclick="jQuery('#metakeyinput, #metakeyselect, #enternew, #cancelnew').toggle();return false;">
                        <span id="enternew"><?php _e('Enter new'); ?></span>
                        <span id="cancelnew" class="hidden"><?php _e('Cancel'); ?></span></a>
                <?php } else { ?>
                    <input type="text" id="metakeyinput" name="metakeyinput" value="" />
                <?php } ?>
            </td>
            <td><textarea id="metavalue" name="metavalue" rows="2" cols="25"></textarea></td>
        </tr>

        <tr><td colspan="2">
                <div class="submit">
                    <?php submit_button( __( 'Add Custom Field' ), '', 'addmeta', false, array( 'id' => 'newmeta-submit', 'data-wp-lists' => 'add:the-list:newmeta' ) ); ?>
                </div>
                <?php wp_nonce_field( 'add-meta', '_ajax_nonce-add-meta', false ); ?>
            </td></tr>
        </tbody>
    </table>
    <?php

}

// ---------------------------------------
// Helpers
// ---------------------------------------

/**
 * Helper function to get the list link of an given object type name
 *
 * @param string $name
 * @return string $url
 */
function ct_get_list_link( $name ) {
    global $ct_registered_tables;

    // Check if table exists
    if( ! isset( $ct_registered_tables[$name] ) ) {
        return '';
    }

    // Check if table has list view
    if( ! isset( $ct_registered_tables[$name]->views->list ) ) {
        return '';
    }

    return $ct_registered_tables[$name]->views->list->get_link();
}

/**
 * Helper function to get the edit link of an given object type name and object id
 *
 * @param string $name
 * @param int $object_id
 *
 * @return string
 */
function ct_get_edit_link( $name, $object_id = 0 ) {
    global $ct_registered_tables;

    // Check if table exists
    if( ! isset( $ct_registered_tables[$name] ) || $object_id === 0 ) {
        return '';
    }

    // Check if table has edit view
    if( ! isset( $ct_registered_tables[$name]->views->edit ) ) {
        return '';
    }

    // Check if user has edit permissions
    if( ! current_user_can( $ct_registered_tables[$name]->cap->edit_item, $object_id ) ) {
        return '';
    }

    $primary_key = $ct_registered_tables[$name]->db->primary_key;

    // Edit link + object ID
    return add_query_arg( array( $primary_key => $object_id ), $ct_registered_tables[$name]->views->edit->get_link() );
}

/**
 * Helper function to get the delete link of an given object type name and object id
 *
 * @param string $name
 * @param int $object_id
 *
 * @return string
 */
function ct_get_delete_link( $name, $object_id = 0 ) {
    global $ct_registered_tables;

    // Check if table exists
    if( ! isset( $ct_registered_tables[$name] ) || $object_id === 0 ) {
        return '';
    }

    // Check if table has list view
    if( ! isset( $ct_registered_tables[$name]->views->list ) ) {
        return '';
    }

    // Check if user has delete permissions
    if( ! current_user_can( $ct_registered_tables[$name]->cap->delete_item, $object_id ) ) {
        return '';
    }

    $primary_key = $ct_registered_tables[$name]->db->primary_key;

    // List link + object ID + action delete
    $url = $ct_registered_tables[$name]->views->list->get_link();
    $url = add_query_arg( array( $primary_key => $object_id ), $url );
    $url = add_query_arg( array( 'ct-action' => 'delete' ), $url );
    $url = add_query_arg( '_wpnonce', wp_create_nonce( 'ct_delete_' . $object_id ), $url );

    return $url;
}

/**
 * Helper function to get the tables registered in a group of custom tables
 *
 * @param string $group
 *
 * @return array
 */
function ct_get_tables_in_group( $group ) {

    global $ct_tables_groups, $wpdb;

    if( ! is_array( $ct_tables_groups ) ) {
        $ct_tables_groups = array();
    }

    if( isset( $ct_tables_groups[$group] ) ) {
        return $ct_tables_groups[$group];
    }

    $ct_tables_groups[$group] = $wpdb->get_col( $wpdb->prepare(
        "SHOW TABLES LIKE %s",
        "%" . $wpdb->esc_like( $group ) . "%"
    ) );

    // Ensure that group tables is an array
    if( ! is_array( $ct_tables_groups[$group] ) ) {
        $ct_tables_groups[$group] = array();
    }

    return $ct_tables_groups[$group];

}

/**
 * Add the table to the group of tables
 *
 * @param string $group
 * @param string $table
 */
function ct_add_table_to_group( $group, $table ) {

    global $ct_tables_groups;

    if( ! is_array( $ct_tables_groups ) ) {
        $ct_tables_groups = array();
    }

    if( ! isset( $ct_tables_groups[$group] ) ) {
        $ct_tables_groups[$group] = array();
    }

    $ct_tables_groups[$group][] = $table;

}

/**
 * Reset the tables in a group
 *
 * @param string $group
 */
function ct_reset_tables_in_group( $group ) {

    global $ct_tables_groups;

    if( ! is_array( $ct_tables_groups ) ) {
        $ct_tables_groups = array();
    }

    if( isset( $ct_tables_groups[$group] ) ) {
        unset( $ct_tables_groups[$group] );
    }

}