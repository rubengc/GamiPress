<?php
/**
 * Database
 *
 * @package     GamiPress\Classes\Database
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Database {

    /**
     * Posts table name
     *
     * @since 1.0.0
     *
     * @var string $posts
     */
    public $posts = '';

    /**
     * Post meta table name
     *
     * @since 1.0.0
     *
     * @var string $postmeta
     */
    public $postmeta = '';

    /**
     * Users table name
     *
     * @since 1.0.0
     *
     * @var string $users
     */
    public $users = '';

    /**
     * User meta table name
     *
     * @since 1.0.0
     *
     * @var string $user
     */
    public $user = '';

    /**
     * Logs table name
     *
     * @since 1.0.0
     *
     * @var string $logs
     */
    public $logs = '';

    /**
     * Logs meta table name
     *
     * @since 1.0.0
     *
     * @var string $logs_meta
     */
    public $logs_meta = '';

    /**
     * User earnings table name
     *
     * @since 1.0.0
     *
     * @var string $user_earnings
     */
    public $user_earnings = '';

    /**
     * User earnings meta table name
     *
     * @since 1.0.0
     *
     * @var string $user_earnings_meta
     */
    public $user_earnings_meta = '';

}