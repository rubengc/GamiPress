<?php

/**
 * See https://github.com/rogerlos/cmb2-metatabs-options
 *
 * GamiPress Team updates:
 *
 * Removed Javascript file checking by using the get_headers() function.
 * Support to CMB2 2.7.0 by renaming CMB2_hookup instances to CMB2_Hookup.
 * Support for PHP 5.6:
 * - Moved auto-generated id into a object var.
 * - Removed all usages of [] to instantiate arrays.
 * - Moved all direct callbacks into a reference calls.
 * On multisite installs, fixed undefined 'hook' index.
 *
 * General Notes
 *
 * @since 1.3   Adds reset options button, thanks @rubengc https://github.com/rubengc
 * @since 1.1.2 Changed way empty string initially passed into filters
 * @since 1.1.1 Now in "WordPress" code style
 * @since 1.1.0 Discovered class did NOT handle multiple options pages; fixed by:
 *              - self::$props now keyed with random ID assigned on each __construct call
 *              - Many action callbacks now closures, to allow ID to be passed
 *              - Nearly every method now has ID as parameter
 * @since 1.1.0 Fixed javascript loading and found and squished a couple of JS bugs
 * @since 1.1.0 Made class a bit more flexible as to the types of pages it can display
 * @since 1.1.0 Stamped out as many static methods as possible
 *
 * Important note when adding an option page via WP action: You MUST add this class after CMB2. The earliest you can
 * add your page via action is:
 *
 * add_action( 'init', 'your_page_adding_callback_function', 9992 );
 *
 * CMB2 decremets the "priority" for each release, to avoid conflicts with various versions of their library;
 * it stands at 9991 at the moment (CMB2 version 2.2.1). To avoid all possible conflicts, set the priority above 10000.
 *
 * You must add your page before admin_init, as that action is referenced within this class.
 */
class Cmb2_Metatabs_Options {
	
	/**
	 * Prevents settings notices from being repeated
	 *
	 * @var bool
	 *
	 * @since  1.0.0
	 */
	protected static $once = FALSE;
	
	/**
	 * self::$props: Properties needed to create options page(s)
	 *                                                                   injected
	 * ['id']                     array        pay identification key             N      @since 1.1.0
	 *    ['page']                string       page slug                          N      @since 1.1.0
	 *    ['hook']                string       WP page hook                       N      @since 1.1.0
	 *    ['key']                 string       WP Options slug                    Y      @since 1.0.0
	 *    ['title']               string       Options page title                 Y      @since 1.0.0
	 *    ['topmenu']             string       See ['menuargs']['parent_slug']    Y      @since 1.0.0
	 *    ['postslug']            string       Option page to post menu           Y      @since 1.0.0
	 *    ['jsuri']               string       JS file for tab handling           Y      @since 1.0.0
	 *    ['resettxt']            string       reset text, empty removes button   Y      @since 1.0.0
	 *    ['savetxt']             string       Save text, empty removes button    Y      @since 1.0.0
	 *    ['class']               string       Class(es) added to wrapper         Y      @since 1.1.1
	 *    ['cols']                int          Columns; 1 or 2                    Y      @since 1.0.0
	 *    ['regkey']              bool         Register options key?              Y      @since 1.1.0
	 *    ['getboxes']            bool         False: no CMB2_Boxes::get_all      Y      @since 1.1.0
	 *    ['plugincss']           bool         False, no CSS, true, CSS           Y      @since 1.2
	 *    ['admincss']            bool|string  False, no CSS, string CSS to add   Y      @since 1.1.1
	 *    ['menuargs']            array        Used in WP add_[sub]menu_page      Y      @since 1.0.0
	 *       ['parent_slug']      string       Parent menu slug                   Y      @since 1.0.2
	 *       ['page_title']       string       Page Title                         Y      @since 1.0.2
	 *       ['menu_title']       string       Menu Title                         Y      @since 1.0.2
	 *       ['capability']       string       WordPress capability               Y      @since 1.0.2
	 *       ['menu_slug']        string       Menu page slug                     Y      @since 1.0.2
	 *       ['icon_url']         string       Top-level menu icon                Y      @since 1.0.2
	 *       ['position']         int          Top-level menu position            Y      @since 1.0.2
	 *       ['network']          bool         True, multisite network menu       Y      @since 1.1.0
	 *       ['view_capability']  string       WP capability to view page         Y      @since 1.2
	 *    ['boxes']               array        CMB2 metabox objects/IDs           Y      @since 1.0.0
	 *    ['tabs']                array        Array of tab config arrays         Y      @since 1.0.0
	 *       [                    array
	 *          ['id']            string       Tab ID
	 *          ['title']         string       Tab title
	 *          ['desc']          string       HTML shown above boxes on tab
	 *          ['boxes']         array        Plain array of CMB2 box ids
	 *       ]
	 *    ['load']                array        Allows WP actions on page load    Y      @since 1.1.0
	 *       [                    array
	 *          ['action']        string       WP action; allows use of [hook]
	 *          ['call']          callable     Callable function
	 *          ['priority']      int          Priority (10 is default)
	 *          ['args']          int          Number of arguments (1 default)
	 *       ]
	 *
	 * @var array
	 *
	 * @since 1.2    added 'plugincss' which if set to false removes plugin CSS, while still allowing 'admincss'
	 * @since 1.2    added 'view_capability': FALSE = do not check, '' = use 'capability', or WP capability string
	 * @since 1.1.1  added 'class', which allows extra class(es) to be added to wrapper div
	 *               added 'admincss', false: no CSS; string extra CSS to add to page
	 * @since 1.1.0  moved injectible params (with default values) to self::$defaults
	 *               - 'network' - can add page to multisite network menu
	 *               - 'regkey' - optional not registering options key on construct
	 *               - 'getboxes' - optional disabling CMB2_Boxes::get_all
	 *               - 'boxes' array can contain CMB2 box IDs as well as objects
	 *               - injection of page load actions (via 'load')
	 *               - actually works with multiple options pages (!)
	 * @since 1.0.2  turned menuargs into array to match WP functions
	 * @since 1.0.0
	 */
	private static $props = array();
	
	/**
	 * Properties which can be injected via constructor, a subset of self::$props
	 *
	 * @var array $defaults ( See above )
	 *
	 * @since 1.2   added 'view_capability', 'plugincss'
	 * @since 1.1.1 added 'class'
	 * @since 1.1.0 moved from self::$props to prevent problems with multiple options pages
	 */
	private $defaults = array(
		'key'       => 'my_options',
		'regkey'    => TRUE,
		'title'     => 'My Options',
		'topmenu'   => '',
		'postslug'  => '',
		'class'     => '',
		'menuargs'  => array(
			'parent_slug'     => '',
			'page_title'      => '',
			'menu_title'      => '',
			'capability'      => 'manage_options',
			'menu_slug'       => '',
			'icon_url'        => '',
			'position'        => NULL,
			'network'         => FALSE,
			'view_capability' => '',
		),
		'jsuri'     => '',
		'boxes'     => array(),
		'getboxes'  => TRUE,
		'plugincss' => TRUE,
		'admincss'  => '',
		'tabs'      => array(),
		'cols'      => 1,
		'resettxt'  => 'Reset',
		'savetxt'   => 'Save',
		'load'      => array(),
	);

	/**
	 * Auto-generated object ID
	 *
	 * @var string
	 *
	 * @since  1.3.1
	 */
	protected $id;
	
	/**
	 * Inject anything within the self::$defaults array by matching the argument keys.
	 *
	 * @param array $args Array of arguments, see self::$defaults
	 *
	 * @throws \Exception
	 *
	 * @since 1.1.0  only allowed within admin
	 *               - uses $this->defaults instead of self::$props when parsing args
	 *               - sets ID for self::$props
	 *               - no longer always uses option key for page identifier
	 *               - prevents double display if submenu page has same slug as parent
	 * @since 1.0.2  recurse the menuargs array with internal function instead of wp_parse_args()
	 * @since 1.0.0
	 */
	public function __construct( $args ) {

		// require CMB2
		if ( ! class_exists( 'CMB2' ) ) {
			throw new Exception( 'CMB2_Metatabs_Options: CMB2 is required to use this class.' );
		}
		
		// only allow within WP admin area;
		if ( ! is_admin() ) {
			return;
		}
		
		// set the ID
		$this->id = $this->set_ID();
		
		// parse any injected arguments and add to self::$props
		self::$props[ $this->id ] = $this->parse_args_r( $args, $this->defaults );
		
		// validate the properties we were sent
		$this->validate_props( $this->id );
		
		// if the menu_slug == parent_slug, set hide to true, prevents duplicate page display
		self::$props[ $this->id ]['hide'] =
			self::$props[ $this->id ]['menuargs']['parent_slug'] == self::$props[ $this->id ]['menuargs']['menu_slug'] &&
			self::$props[ $this->id ]['menuargs']['parent_slug'] != '';
		
		// add tabs: several actions depend on knowing if tabs are present
		self::$props[ $this->id ]['tabs'] = $this->add_tabs();
		
		// Add actions
		$this->add_wp_actions();
	}
	
	/**
	 * Returns an ID unlikely to be already set
	 *
	 * @return string
	 *
	 * @since 1.1.0
	 */
	private function set_ID() {
		
		return 'cmo' . rand( 1000, 9999 );
	}
	
	/**
	 * PARSE ARGUMENTS RECURSIVELY
	 * Allows us to merge multidimensional properties
	 *
	 * Thanks: https://gist.github.com/boonebgorges/5510970
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return array
	 *
	 * @since 1.0.2
	 */
	public function parse_args_r( &$a, $b ) {
		
		$a = (array) $a;
		$b = (array) $b;
		$r = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $r[ $k ] ) ) {
				$r[ $k ] = $this->parse_args_r( $v, $r[ $k ] );
			} else {
				$r[ $k ] = $v;
			}
		}
		
		return $r;
	}
	
	/**
	 * Checks the values of critical passed properties
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @throws \Exception
	 *
	 * @since 1.2   Added view_capability setting
	 * @since 1.1.0 Setting self::$props[$this->id][page]
	 * @since 1.0.2 Removed validation of menu args. Sending a plain array will no longer work!
	 * @since 1.0.1 Moved menuargs validation to within this method
	 * @since 1.0.0
	 */
	private function validate_props() {
		
		// if key or title do not exist, throw exception
		if ( ! self::$props[ $this->id ]['key'] ) {
			throw new Exception( 'CMB2_Metatabs_Options: Settings key missing.' );
		}
		
		// set JS url
		if ( ! self::$props[ $this->id ]['jsuri'] ) {
			self::$props[ $this->id ]['jsuri'] = plugin_dir_url( __FILE__ ) . 'cmb2multiopts.js';
		}
		
		// set columns to 1 if illegal value sent
		self::$props[ $this->id ]['cols'] = intval( self::$props[ $this->id ]['cols'] );
		if ( self::$props[ $this->id ]['cols'] > 2 || self::$props[ $this->id ]['cols'] < 1 ) {
			self::$props[ $this->id ]['cols'] = 1;
		}
		
		// if menuargs[menu_slug] is set, change the page prop to that
		self::$props[ $this->id ]['page'] = self::$props[ $this->id ]['menuargs']['menu_slug'] ?
			self::$props[ $this->id ]['menuargs']['menu_slug'] : self::$props[ $this->id ]['key'];
		
		// set page viewing capability; empty string = same as menuargs[capability], false = do not check
		if ( ! self::$props[ $this->id ]['menuargs']['view_capability'] ) {
			self::$props[ $this->id ]['menuargs']['view_capability'] =
				self::$props[ $this->id ]['menuargs']['view_capability'] === '' ?
					self::$props[ $this->id ]['menuargs']['capability'] : FALSE;
		}
	}
	
	/**
	 * Some additional actions are added elsewhere as they cannot be added this early.
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @since 1.1.0 menu can be added to multisite network menu
	 *              - page load actions allowed
	 *              - registering the options key optional
	 * @since 1.0.0
	 */
	private function add_wp_actions() {
		
		// Register setting
		if ( self::$props[ $this->id ]['regkey'] ) {
			add_action(
				'admin_init',
				array( $this, 'register_setting' )
			);
		}
		
		// Allow multisite network menu pages
		$net = ( is_multisite() && self::$props[ $this->id ]['menuargs']['network'] === TRUE ) ? 'network_' : '';

		// Adds page to admin
		add_action(
			$net . 'admin_menu',
			array( $this, 'add_options_page' ),
			12
		);
		
		// Include CSS for this options page as style tag in head, if tabs are configured
		add_action(
			'admin_head',
			array( $this, 'add_css' )
		);
		
		// Adds JS to foot
		add_action(
			'admin_enqueue_scripts',
			array( $this, 'add_scripts' )
		);
		
		// Adds custom save button field, allowing save button to be added to metaboxes
		add_action(
			'cmb2_render_options_save_button',
			array( $this, 'render_options_save_button' ),
			10, 1 );
	}
	
	/**
	 * Allows page to call WP actions on load
	 *
	 * @param string $this->id
	 *
	 * @since 1.1.0
	 */
	private function load_actions() {
		
		if ( empty( self::$props[ $this->id ]['load'] ) ) {
			return;
		}
		
		foreach ( self::$props[ $this->id ]['load'] as $load ) {
			
			// skip if no action or callback
			if ( ! isset( $load['action'] ) || ! isset( $load['callback'] ) ) {
				continue;
			}
			
			// skip if the [hook] token is not in the [action]
			if ( strpos( $load['action'], '-[hook]' ) === FALSE ) {
				continue;
			}
			
			// replace token with page hook
			$load['action'] = str_replace( '[hook]', self::$props[ $this->id ]['hook'], $load['action'] );
			
			// make sure if priority is int
			$pri = isset( $load['priority'] ) && intval( $load['priority'] ) > 0 ? intval( $load['priority'] ) : 10;
			
			// make sure args is int
			$arg = isset( $load['args'] ) && intval( $load['args'] ) > 0 ? intval( $load['args'] ) : 1;
			
			// add action
			add_action( $load['action'], $load['callback'], $pri, $arg );
		}
	}
	
	/**
	 * @param string $this->id @since 1.1.0
	 *
	 * @since 1.0.0
	 */
	public function register_setting() {
		
		register_setting( self::$props[ $this->id ]['key'], self::$props[ $this->id ]['key'] );
	}
	
	/**
	 * @param string $this->id @since 1.1.0
	 *
	 * @since 1.1.0 Changed some action callbacks to closures
	 *              - pages can now load actions
	 * @since 1.0.2 Moved the callback determination to build_menu_args()
	 * @since 1.0.0
	 */
	public function add_options_page() {
		
		// build arguments
		$args = $this->build_menu_args( $this->id );

		// this is kind of ugly, but so is the WP function!
		self::$props[ $this->id ]['hook'] =
			$args['cb']( $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6] );
		
		// Include CMB CSS in the head to avoid FOUC, called here as we need the screen ID
		add_action(
			'admin_print_styles-' . self::$props[ $this->id ]['hook'],
			array( 'CMB2_Hookup', 'enqueue_cmb_css' )
		);

		// Adds existing metaboxes, see note in function, called here as we need the screen ID
		add_action(
			'add_meta_boxes_' . self::$props[ $this->id ]['hook'],
			array( $this, 'add_metaboxes' )
		);
		
		// On page load, do "metaboxes" actions, called here as we need the screen ID
		add_action(
			'load-' . self::$props[ $this->id ]['hook'],
			array( $this, 'do_metaboxes' )
		);
		
		// Allows pages to call actions
		$this->load_actions();
	}
	
	/**
	 * Builds the arguments needed to add options page to admin menu if they are not injected.
	 *
	 * Including either self::$props['topmenu'] or self::$props['menuargs']['parent_slug'] will trigger the creation
	 * of a submenu, otherwise a new top menu will be added.
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @return array
	 *
	 * @since 1.0.2 Removed counting of menu arguments, function now uses menuargs keys
	 * @since 1.0.1 Removed menuargs validation to validate_props() method
	 * @since 1.0.0
	 */
	private function build_menu_args() {
		
		$args = array();
		
		// set the top menu if either topmenu or the menuargs parent_slug is set
		$parent = self::$props[ $this->id ]['topmenu'] ? self::$props[ $this->id ]['topmenu'] : '';
		$parent = self::$props[ $this->id ]['menuargs']['parent_slug'] ?
			self::$props[ $this->id ]['menuargs']['parent_slug'] : $parent;
		
		// set the page title; overrides 'title' with menuargs 'page_title' if set
		$pagetitle = self::$props[ $this->id ]['menuargs']['page_title'] ?
			self::$props[ $this->id ]['menuargs']['page_title'] : self::$props[ $this->id ]['title'];
		
		// sub[0] : parent slug
		if ( $parent ) {
			// add a post_type get variable, to allow post options pages, if set
			$add    = self::$props[ $this->id ]['postslug'] ? '?post_type=' . self::$props[ $this->id ]['postslug'] : '';
			$args[] = $parent . $add;
		}
		
		// top[0], sub[1] : page title
		$args[] = $pagetitle;
		
		// top[1], sub[2] : menu title, defaults to page title if not set
		$args[] = self::$props[ $this->id ]['menuargs']['menu_title'] ?
			self::$props[ $this->id ]['menuargs']['menu_title'] : $pagetitle;
		
		// top[2], sub[3] : capability
		$args[] = self::$props[ $this->id ]['menuargs']['capability'];
		
		// top[3], sub[4] : menu_slug, defaults to options slug if not set
		$args[] = self::$props[ $this->id ]['menuargs']['menu_slug'] ?
			self::$props[ $this->id ]['menuargs']['menu_slug'] : self::$props[ $this->id ]['key'];
		
		// top[4], sub[5] : callable function
		$args[] = array( $this, 'admin_page_display' );
		
		// top menu icon and menu position
		if ( ! $parent ) {
			
			// top[5] icon url
			$args[] = self::$props[ $this->id ]['menuargs']['icon_url'] ?
				self::$props[ $this->id ]['menuargs']['icon_url'] : '';
			
			// top[6] menu position
			$args[] = self::$props[ $this->id ]['menuargs']['position'] === NULL ?
				NULL : intval( self::$props[ $this->id ]['menuargs']['position'] );
		} // sub[6] : unused, but returns consistent array
		else {
			$args[] = NULL;
		}
		
		// set which WP function will be called based on $parent
		$args['cb'] = $parent ? 'add_submenu_page' : 'add_menu_page';
		
		return $args;
	}
	
	/**
	 * Add WP's metabox script, either by itself or as dependency of the tabs script. Added only to this options page.
	 * If you roll your own script, note the localized values being passed here.
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @throws \Exception
	 *
	 * @since 1.1.0 Added initial check for CMO page
	 *              - Fixed bug with localizing page; was sending wrong key if menu_slug was configured
	 * @since 1.0.1 Always add postbox toggle, removed toggle from tab handler JS
	 * @since 1.0.0
	 */
	public function add_scripts() {
		
		global $hook_suffix;

		if( ! isset( self::$props[ $this->id ]['hook'] ) ) {
			return;
		}
		
		// do not run if not a CMO page
		if ( $hook_suffix !== self::$props[ $this->id ]['hook'] ) {
			return;
		}
		
		// 'postboxes' needed for metaboxes to work properly
		wp_enqueue_script( 'postbox' );
		
		// toggle the postboxes
		add_action(
			'admin_print_footer_scripts',
			array( $this, 'toggle_postboxes' )
		);
		
		// only add the main script to the options page if there are tabs present
		if ( empty( self::$props[ $this->id ]['tabs'] ) ) {
			return;
		}
		
		// if self::$props['jsuri'] is empty, throw exception
		if ( ! self::$props[ $this->id ]['jsuri'] ) {
			throw new Exception( 'CMB2_Metatabs_Options: Tabs included but JS file not specified.' );
		}
		
		// enqueue the script
		wp_enqueue_script(
			self::$props[ $this->id ]['page'] . '-admin',
			self::$props[ $this->id ]['jsuri'],
			array( 'postbox' ),
			FALSE,
			TRUE
		);
		
		// localize script to give access to this page's slug
		wp_localize_script( self::$props[ $this->id ]['page'] . '-admin', 'cmb2OptTabs', array(
			'key'        => self::$props[ $this->id ]['page'],
			'posttype'   => self::$props[ $this->id ]['postslug'],
			'defaulttab' => self::$props[ $this->id ]['tabs'][0]['id'],
		) );
	}

	public function render_options_save_button() {
		global $hook_suffix;

		if ( $hook_suffix !== self::$props[ $this->id ]['hook'] || self::$props[ $this->id ]['hide'] ) {
			return;
		}

		$args = func_get_args();
		echo $this->render_save_button( $args[0]->args['desc'] );
	}
	
	/**
	 * Ensures boxes are toggleable on non tabs pages
	 *
	 * @since 1.0.0
	 */
	public function toggle_postboxes() {
		
		echo '<script>jQuery(document).ready(function(){postboxes.add_postbox_toggles("postbox-container");});</script>';
	}
	
	/**
	 * Adds a couple of rules to clean up WP styles if tabs are included
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @since 1.2   checks 'plugincss' and does not add plugincss if set to false
	 * @since 1.1.1 added check of 'admincss' option to allow user to not add these styles
	 *              same property allows user to add their own css to page
	 *              added IDs to style tags
	 * @since 1.0.0
	 */
	public function add_css() {
		
		// if tabs are not being used, return
		if ( empty( self::$props[ $this->id ]['tabs'] ) || self::$props[ $this->id ]['admincss'] === FALSE ) {
			return;
		}
		
		$css = '';
		
		// add css to clean up tab styles in admin when used in a postbox
		if ( self::$props[ $this->id ]['plugincss'] === TRUE ) {
			$css .= '<style type="text/css" id="CMO-cleanup-css">';
			$css .= '.' . self::$props[ $this->id ]['page'] . '.cmb2-options-page #poststuff h2.nav-tab-wrapper{padding-bottom:0;margin-bottom: 20px;}';
			$css .= '.' . self::$props[ $this->id ]['page'] . '.cmb2-options-page .opt-hidden{display:none;}';
			$css .= '.' . self::$props[ $this->id ]['page'] . '.cmb2-options-page #side-sortables{padding-top:22px;}';
			$css .= '</style>';
		}
		
		// add user-injected CSS; added as separate style tag in case it is malformed
		if ( ! empty( self::$props[ $this->id ]['admincss'] ) ) {
			$css = '<style type="text/css" id="CMO-exta-css">';
			$css .= self::$props[ $this->id ]['admincss'];
			$css .= '</style>';
		}
		
		echo $css;
	}
	
	/**
	 * Adds CMB2 metaboxes.
	 *
	 * @since 1.1.0  ok to have no metaboxes (to allow text-only pages)
	 *               - some action callbacks now closures
	 * @since 1.0.0
	 */
	public function add_metaboxes() {
		
		// get the metaboxes
		self::$props[ $this->id ]['boxes'] = $this->cmb2_metaboxes( $this->id );

		// exit this method if no metaboxes are present
		if ( empty( self::$props[ $this->id ]['boxes'] ) ) {
			return;
		}
		
		foreach ( self::$props[ $this->id ]['boxes'] as $box ) {
			
			// skip if this should not be shown
			if ( ! $this->should_show( $box, $this->id ) ) {
				continue;
			}
			
			$mid = $box->meta_box['id'];
			
			// add notice if settings are saved
			add_action( 'cmb2_save_options-page_fields_' . $mid,
				array( $this, 'settings_notices' ),
				10, 2 );
			
			// add callback if tabs are configured which hides metaboxes until moved into proper tabs if not in sidebar
			if ( ! empty( self::$props[ $this->id ]['tabs'] ) && $box->meta_box['context'] !== 'side' ) {
				add_filter( 'postbox_classes_' . self::$props[ $this->id ]['hook'] . '_' . $mid,
					array( $this, 'hide_metabox_class' ) );
			}
			
			// if boxes are closed by default...
			if ( $box->meta_box['closed'] ) {
				add_filter( 'postbox_classes_' . self::$props[ $this->id ]['hook'] . '_' . $mid,
					array( $this, 'close_metabox_class' ) );
			}
			
			// add meta box
			add_meta_box(
				$box->meta_box['id'],
				$box->meta_box['title'],
				array( $this, 'metabox_callback' ),
				self::$props[ $this->id ]['hook'],
				$box->meta_box['context'],
				$box->meta_box['priority']
			);
		}
	}
	
	/**
	 * Mimics the CMB2 "should show" function to prevent boxes which should not be shown on this options page from
	 * appearing.
	 *
	 * @param CMB2   $box
	 *
	 * @return bool
	 *
	 * @since 1.1.0 Fixed bug with key vs page
	 * @since 1.0.0
	 */
	private function should_show( $box ) {
		
		// if the show_on key is not set, don't show
		if ( ! isset( $box->meta_box['show_on']['key'] ) ) {
			return FALSE;
		}
		
		// if the key is set but is not set to options-page, don't show
		if ( $box->meta_box['show_on']['key'] != 'options-page' ) {
			return FALSE;
		}
		
		// if this options key is not in the show_on value, don't show
		if ( ! in_array( self::$props[ $this->id ]['page'], $box->meta_box['show_on']['value'] ) ) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * The "hidden" class hides metaboxes until they have been moved to appropriate tab, if tabs are used.
	 *
	 * @param array $classes
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function hide_metabox_class( $classes ) {
		
		$classes[] = 'opt-hidden';
		
		return $classes;
	}
	
	/**
	 * Adds class to closed-by-default metaboxes
	 *
	 * @param array $classes
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function close_metabox_class( $classes ) {
		
		$classes[] = 'closed';
		
		return $classes;
	}
	
	/**
	 * Triggers the loading of our metaboxes on this screen.
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @since 1.0.0
	 */
	public function do_metaboxes() {
		
		do_action( 'add_meta_boxes_' . self::$props[ $this->id ]['hook'], NULL );
		do_action( 'add_meta_boxes', self::$props[ $this->id ]['hook'], NULL );
	}
	
	/**
	 * Builds the fields and saves them.
	 *
	 * @param array  $args @since 1.1.0
	 *
	 * @since 1.0.1 Refactored the save tests to method should_save()
	 * @since 1.0.0
	 */
	public function metabox_callback( $post, $metabox ) {
		
		// get the metabox, fishing the ID out of the arguments array
		$cmb = cmb2_get_metabox( $metabox['id'], self::$props[ $this->id ]['key'] );
		
		if ( $this->should_save( $cmb ) ) {
			// save fields
			$cmb->save_fields( self::$props[ $this->id ]['key'], $cmb->mb_object_type(), $_POST );
		} else if ( $this->should_reset( $cmb ) ) {
			// Reset fields
			delete_option( self::$props[ $this->id ]['key'] );
		}
		
		// show the fields
		$cmb->show_form();
	}
	
	/**
	 * Determine whether the CMB2 object should be reset. All tests must be true, hence return false for
	 * any failure.
	 *
	 * @param string $this->id @since 1.1.0
	 * @param \CMB2  $cmb
	 *
	 * @return bool
	 *
	 * @since 1.1.0 static unclung
	 * @since 1.0.3 made static method
	 * @since 1.0.1
	 */
	private function should_reset( $cmb ) {
		
		// are these values set?
		if ( ! isset( $_POST['reset-cmb'], $_POST['object_id'], $_POST[ $cmb->nonce() ] ) ) {
			return FALSE;
		}
		
		// does the nonce match?
		if ( ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return FALSE;
		}
		
		// does the object_id equal the settings key?
		if ( ! $_POST['object_id'] == self::$props[ $this->id ]['key'] ) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Determine whether the CMB2 object should be saved. All tests must be true, hence return false for
	 * any failure.
	 *
	 * @param \CMB2  $cmb
	 *
	 * @return bool
	 *
	 * @since 1.1.0 static unclung
	 * @since 1.0.3 made static method
	 * @since 1.0.1
	 */
	private function should_save( $cmb ) {
		
		// was this flagged to save fields?
		if ( ! $cmb->prop( 'save_fields' ) ) {
			return FALSE;
		}
		
		// are these values set?
		if ( ! isset( $_POST['submit-cmb'], $_POST['object_id'], $_POST[ $cmb->nonce() ] ) ) {
			return FALSE;
		}
		
		// does the nonce match?
		if ( ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return FALSE;
		}
		
		// does the object_id equal the settings key?
		if ( ! $_POST['object_id'] == self::$props[ $this->id ]['key'] ) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Admin page markup.
	 *
	 * @since 1.2   Checks user's capabilities to view page
	 * @since 1.1.0 Added page identifier to filters for easier multiple-page use
	 *              - Moved page part compilers to separate functions for clarity
	 * @since 1.0.0
	 */
	public function admin_page_display() {
		
		// this is only set to true if a menu sub-item has the same slug as the parent
		if ( self::$props[ $this->id ]['hide'] ) {
			return;
		}
		
		// check page viewing capability
		if ( self::$props[ $this->id ]['menuargs']['view_capability'] !== FALSE ) {
			if ( ! current_user_can( self::$props[ $this->id ]['menuargs']['view_capability'] ) ) {
				return;
			}
		}
		
		// get top of page
		$page = $this->admin_page_top( $this->id );
		
		// if there are metaboxes to display, add form and boxes
		if ( ! empty( self::$props[ $this->id ]['boxes'] ) ) {
			$page .= $this->admin_page_form( $this->id );
		}
		
		// get bottom of page
		$page .= $this->admin_page_bottom( $this->id );
		
		echo $page;
		
		// reset the notices flag
		self::$once = FALSE;
	}
	
	/**
	 * Generates the top of the options page
	 *
	 * @return string
	 *
	 * @since 1.1.2 Instead of passing empty string directly in filter call, set it to var; allows cumulative filters
	 * @since 1.1.1  added ability to inject extra wrapper class(es)
	 * @since 1.1.0
	 */
	private function admin_page_top() {
		
		// standard classes, includes page id
		$classes    = 'wrap cmb2-options-page cmo-options-page ' . self::$props[ $this->id ]['page'];
		$filterable = '';
		
		// add any extra configured classes
		if ( ! empty( self::$props[ $this->id ]['class'] ) ) {
			$classes .= ' ' . self::$props[ $this->id ]['class'];
		}
		
		$ret = '<div class="' . $classes . '">';
		$ret .= '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';
		$ret .= '<div class="cmo-before-form">';
		
		// note this now passes the page slug as a second argument
		$ret .= apply_filters( 'cmb2metatabs_before_form', $filterable, self::$props[ $this->id ]['page'] );
		
		$ret .= '</div>';
		
		return $ret;
	}
	
	/**
	 * Generate bottom of options page
	 *
	 * @param $this->id
	 *
	 * @return string
	 *
	 * @since 1.1.2 Instead of passing empty string directly in filter call, set it to var; allows cumulative filters
	 * @since 1.1.0
	 */
	private function admin_page_bottom() {
		
		$filterable = '';
		
		$ret = '<div class="cmo-after-form">';
		
		// note this now passes the page slug as a second argument
		$ret .= apply_filters( 'cmb2metatabs_after_form', $filterable, self::$props[ $this->id ]['page'] );
		
		$ret .= '</div>';
		$ret .= '</div>';
		
		return $ret;
	}
	
	/**
	 * Metaboxes and tabs display on options page
	 *
	 * @return string
	 *
	 * @since 1.2   Added WP nonces for box order and closed boxes
	 * @since 1.1.0 Moved admin page form to separate function to allow text-only options pages
	 */
	private function admin_page_form() {
		
		// form wraps all tabs
		$ret = '<form class="cmb-form" method="post" id="cmo-options-form" '
		       . 'enctype="multipart/form-data" encoding="multipart/form-data">';
		
		// hidden object_id field
		$ret .= '<input type="hidden" name="object_id" value="' . self::$props[ $this->id ]['key'] . '">';
		
		// wp nonce fields
		$ret .= wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', FALSE, FALSE );
		$ret .= wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', FALSE, FALSE );
		
		// add postbox, which allows use of metaboxes
		$ret .= '<div id="poststuff">';
		
		// main column
		$ret .= '<div id="post-body" class="metabox-holder columns-' . self::$props[ $this->id ]['cols'] . '">';
		
		// if two columns are called for
		if ( self::$props[ $this->id ]['cols'] == 2 ) {
			
			// add markup for sidebar
			$ret .= '<div id="postbox-container-1" class="postbox-container">';
			$ret .= '<div id="side-sortables" class="meta-box-sortables ui-sortable">';
			
			ob_start();
			
			// add sidebar metaboxes
			do_meta_boxes( self::$props[ $this->id ]['hook'], 'side', NULL );
			
			$ret .= ob_get_clean();
			
			$ret .= '</div></div>';  // close sidebar
		}
		
		// open postbox container
		$ret .= '<div id="postbox-container-';
		$ret .= self::$props[ $this->id ]['cols'] == 2 ? '2' : '1';
		$ret .= '" class="postbox-container">';
		
		// add tabs; the sortables container is within each tab
		$ret .= $this->render_tabs( $this->id );
		
		ob_start();
		
		// place normal boxes, note that 'normal' and 'advanced' are rendered together when using tabs
		do_meta_boxes( self::$props[ $this->id ]['hook'], 'normal', NULL );
		do_meta_boxes( self::$props[ $this->id ]['hook'], 'advanced', NULL );
		
		$ret .= ob_get_clean();
		
		$ret .= '</div></div></div>';
		
		// add submit button if resettxt or savetxt was included
		if ( self::$props[ $this->id ]['resettxt'] || self::$props[ $this->id ]['savetxt'] ) {
			$ret .= '<div style="clear:both;">';
			$ret .= $this->render_reset_button( self::$props[ $this->id ]['resettxt'] );
			$ret .= $this->render_save_button( self::$props[ $this->id ]['savetxt'] );
			$ret .= '</div>';
		}
		
		$ret .= '</form>';
		
		return $ret;
	}
	
	/**
	 * Renders reset button
	 *
	 * @param string $text
	 *
	 * @since 1.3
	 * @return string
	 */
	public function render_reset_button( $text = '' ) {
		
		return $text ? '<input type="submit" name="reset-cmb" value="' . $text . '" class="button">' : '';
	}
	
	/**
	 * Renders save button
	 *
	 * @param string $text
	 *
	 * @since 1.1.0 CMB2 now invokes this from closure which already fished out the text string
	 * @since 1.0.0
	 * @return string
	 */
	public function render_save_button( $text = '' ) {
		
		return $text ? '<input type="submit" name="submit-cmb" value="' . $text . '" class="button-primary">' : '';
	}
	
	/**
	 * Added a check to make sure its only called once for the page...
	 *
	 * @since 1.0.1 updated text domain
	 * @since 1.0.0
	 */
	public function settings_notices( $object_id, $cmb_id ) {
		
		// bail if this isn't a notice for this page or we've already added a notice
		if ( $object_id !== self::$props[ $this->id ]['key'] || empty( $cmb_id ) || self::$once ) {
			return;
		}
		
		// add notifications
		add_settings_error( self::$props[ $this->id ]['key'] . '-notices', '', __( 'Settings updated.', 'cmb2' ), 'updated' );
		settings_errors( self::$props[ $this->id ]['key'] . '-notices' );
		
		// set the flag so we don't pile up notices
		self::$once = TRUE;
	}
	
	/**
	 * Returns tabs, if they've been configured.
	 *
	 * @return string $return
	 *
	 * @since 1.0.0
	 */
	private function render_tabs() {
		
		if ( empty( self::$props[ $this->id ]['tabs'] ) ) {
			return '';
		}
		
		$containers = '';
		$tabs       = '';
		
		foreach ( self::$props[ $this->id ]['tabs'] as $tab ) {
			
			// add tabs navigation
			$tabs .= '<a href="#" id="opt-tab-' . $tab['id'] . '" class="nav-tab opt-tab" ';
			$tabs .= 'data-optcontent="#opt-content-' . $tab['id'] . '">';
			$tabs .= $tab['title'];
			$tabs .= '</a>';
			
			// add tabs containers, javascript will use the data attribute to move metaboxes to within proper tab
			$contents = implode( ',', $tab['boxes'] );
			
			// tab container markup
			$containers .= '<div class="opt-content" id="opt-content-' . $tab['id'] . '" ';
			$containers .= ' data-boxes="' . $contents . '">';
			$containers .= $tab['desc'];
			$containers .= '<div class="meta-box-sortables ui-sortable">';
			$containers .= '</div>';
			$containers .= '</div>';
		}
		
		// add the tab structure to the page
		$return = '<h2 class="nav-tab-wrapper">';
		$return .= $tabs;
		$return .= '</h2>';
		$return .= $containers;
		
		return $return;
	}
	
	/**
	 * Allows three methods of adding metaboxes:
	 *
	 * 1) Injected boxes are added to the boxes array
	 * 2) Add additional boxes (or boxes if none were injected) the usual way within this function
	 * 3) If array is still empty and getboxes === true, call CMB2_Boxes::get_all();
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @return array|\CMB2[]
	 *
	 * @since 1.1.0 allow skipping of call to CMB2_Boxes::get_all();
	 *              - ok to pass CMB2 metabox ID or CMB2 object; previously only objects allowed
	 * @since 1.0.0
	 */
	private function cmb2_metaboxes() {
		
		// add any injected metaboxes
		$boxes = self::$props[ $this->id ]['boxes'];
		
		// if boxes is not empty, check to see if they're CMB2 objects, or strings
		if ( ! empty( $boxes ) ) {
			foreach ( $boxes as $key => $box ) {
				if ( ! is_object( $box ) ) {
					$boxes[ $key ] = CMB2_Boxes::get( $box );
				}
			}
		}
		
		// if $boxes is still empty and getboxes is true, try grabbing boxes from CMB2
		$boxes = ( empty( $boxes ) && self::$props[ $this->id ]['getboxes'] === TRUE ) ? CMB2_Boxes::get_all() : $boxes;
		
		return $boxes;
	}
	
	/**
	 * Add tabs to your options page. The array is empty by default. You can inject them into the constructor,
	 * or add them here, or leave empty for no tabs.
	 *
	 * @param string $this->id @since 1.1.0
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	private function add_tabs() {
		
		$tabs = self::$props[ $this->id ]['tabs'];
		
		return $tabs;
	}
}