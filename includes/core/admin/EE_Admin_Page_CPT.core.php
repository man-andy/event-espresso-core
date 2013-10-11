<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author				Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			{@link http://eventespresso.com/support/terms-conditions/}   * see Plugin Licensing *
 * @ link				{@link http://www.eventespresso.com}
 * @ since		 		4.0
 *
 * ------------------------------------------------------------------------
 */
	


/**
 * EE_Admin_Page_CPT class
 *
 * This class is for child classes that utilize core WP CPT views for add/edit pages.  All you have to do is extend this class instead of the usual EE_Admin_Page class for your child.
 *
 * Please not the following caveats:
 *
 * 1. When using add_meta_box() - it must use $this->wp_page_slug as the screen_id for the page NOT $this->_current_screen->id.  This is b/c there is a bug with how WP renders its custom post type pages that doesn't accept the default current_screen for metaboxes.
 *
 * 2. the same is true for any help_tabs or screen_options you want to add to custom post type views.
 *
 * 3. it is EXPECTED that $this->page_slug will be IDENTICAL to what slug/id was used when doing register_post_type().  So for instance, if you registered a "books" post type then $this->page_slug = 'espresso_books'  would NOT be valid.  So the correct id for the new post type would be "espresso_books".  Remember, you can still use something totally different for front-end rewrite slugs in your configuration array for the register post type.
 * 
 * @package		Event Espresso
 * @subpackage 	includes/core/admin/EE_Admin_Page.core.php
 * @abstract
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
abstract class EE_Admin_Page_CPT extends EE_Admin_Page {
	

	/**
	 * This gets set in _setup_cpt
	 * It will contain the object for the custom post type.
	 * @var object
	 */
	protected $_cpt_object;



	/**
	 * a boolean flag to set whether the current route is a cpt route or not.
	 * @var bool
	 */
	protected $_cpt_route = FALSE;



	/**
	 * This property allows cpt classes to define multiple routes as cpt routes.
	 * //in this array we define what the custom post type for this route is.
	 * array(
	 * 'route_name' => 'custom_post_type_slug'
	 * )
	 * @var array
	 */
	protected $_cpt_routes = array();




	/**
	 * This simply defines what the corresponding routes WP will be redirected to after completeing a post save/update.
	 *
	 * in this format:
	 * array(
	 * 'post_type_slug' => 'edit_route'
	 * )
	 * 
	 * @var array
	 */
	protected $_cpt_edit_routes = array();




	/**
	 * If child classes set the name of their main model via the $_cpt_obj_models property, EE_Admin_Page_CPT will attempt to retrieve the related object model for the edit pages and assign it to _cpt_page_object.
	 * the _cpt_model_names property should be in the following format:
	 * array(
	 * 'route_defined_by_action_param' => 'Model_Name')
	 * 
	 * @var boolean
	 */
	protected $_cpt_model_names = array();
	protected $_cpt_model_obj = FALSE;




	/**
	 * This will hold an array of autosave containers that will be used to obtain input values and hook into the WP autosave so we can save our inputs on the save_post hook!  Children classes should add to this array by using the _register_autosave_containers() method so that we don't override any other containers already registered.  Registration of containers should be done before load_page_dependencies() is run.
	 * 
	 * @var array()
	 */
	protected $_autosave_containers = array();




	/**
	 * This is hooked into the WordPress do_action('save_post') hook and runs after the custom post type has been saved.  Child classes are required to declare this method.  Typically you would use this to save any additional data.
	 *
	 * Keep in mind also that "save_post" runs on EVERY post update to the database.  
	 * ALSO very important.  When a post transitions from scheduled to published, the save_post action is fired but you will NOT have any _POST data containing any extra info you may have from other meta saves.  So MAKE sure that you handle this accordingly.
	 *
	 * @access protected
	 * @abstract
	 * @param  string $post_id The ID of the cpt that was saved (so you can link relationally)
	 * @param  object $post    The post object of the cpt that was saved. 
	 * @return void          
	 */
	abstract protected function _insert_update_cpt_item( $post_id, $post );




	/**
	 * This is hooked into the WordPress do_action('trashed_post') hook and runs after a cpt has been trashed.
	 *
	 * @abstract
	 * @access public
	 * @param  string $post_id The ID of the cpt that was trashed
	 * @return void
	 */
	abstract public function trash_cpt_item( $post_id );





	/**
	 * This is hooked into the WordPress do_action('untrashed_post') hook and runs after a cpt has been untrashed
	 * @param  string $post_id theID of the cpt that was untrashed
	 * @return void
	 */
	abstract public function restore_cpt_item( $post_id );





	/**
	 * This is hooked into the WordPress do_action('delete_cpt_item') hook and runs after a cpt has been fully deleted from the db
	 * @param  string $post_id the ID of the cpt that was deleted
	 * @return void
	 */
	abstract public function delete_cpt_item( $post_id );




	/**
	 * Just utilizing the method EE_Admin exposes for doing things before page setup.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _before_page_setup() {

		$page = isset( $this->_req_data['page'] ) ? $this->_req_data['page'] : $this->page_slug;

		$this->_cpt_routes = array_merge(array('create_new'=>$this->page_slug, 'edit' => $this->page_slug), $this->_cpt_routes );

		//let's see if the current route has a value for cpt_object_slug if it does we use that instead of the page
		$this->_cpt_object = isset($this->_req_data['action']) && isset( $this->_cpt_routes[$this->_req_data['action']] ) ? get_post_type_object($this->_cpt_routes[$this->_req_data['action']]) : get_post_type_object( $page );

		//TODO the below will need to be reworked to account for the cpt routes that are NOT based off of page but action param.
		//get current page from autosave
		$current_page = isset( $this->_req_data['ee_autosave_data']['ee-cpt-hidden-inputs']['current_page'] ) ? $this->_req_data['ee_autosave_data']['ee-cpt-hidden-inputs']['current_page'] : NULL;
		$this->_current_page = isset( $this->_req_data['current_page'] ) ? $this->_req_data['current_page'] : $current_page;
		
		//autosave... make sure its only for the correct page
		if ( !empty($this->_current_page ) && $this->_current_page == $this->page_slug ) {
			//setup autosave ajax hook
			//add_action('wp_ajax_ee-autosave', array( $this, 'do_extra_autosave_stuff' ), 10 ); //TODO reactivate when 4.2 autosave is implemented
		}

	}




	/**
	 * This method is used to register additional autosave containers to the _autosave_containers property. 
	 *
	 * @todo We should automate this at some point by creating a wrapper for add_post_metabox and in our wrapper we automatically register the id for the post metabox as a container.
	 * @param  array $ids an array of ids for containers that hold form inputs we want autosave to pickup.  Typically you would send along the id of a metabox container.
	 * @return void
	 */
	protected function _register_autosave_containers( $ids ) {
		$this->_autosave_containers = array_merge( $this->_autosave_fields, (array) $ids );
	}





	/**
	 * Something nifty.  We're going to loop through all the registered metaboxes and if the CALLBACK is an instance of EE_Admin_Page OR EE_Admin_Hooks, then we'll add the id to our _autosave_containers array.
	 */
	protected function _set_autosave_containers() {
		global $wp_meta_boxes;

		$containers = array();

		if ( empty( $wp_meta_boxes ) )
			return;

		$current_metaboxes = isset( $wp_meta_boxes[$this->page_slug] ) ? $wp_meta_boxes[$this->page_slug] : array();

		foreach ( $current_metaboxes as $box_context ) {
			foreach ( $box_context as $box_details ) {
				foreach ( $box_details as $box ) {
					if ( is_array( $box['callback'] ) && ( $box['callback'][0] instanceof EE_Admin_Page || $box['callback'][0] instanceof EE_Admin_Hooks ) ){
						$containers[] = $box['id'];
					}
				}
			}
		}

		$this->_autosave_containers = array_merge( $this->_autosave_containers, $containers );

		//add hidden inputs container
		$this->_autosave_containers[] = 'ee-cpt-hidden-inputs';
	}







	protected function _load_autosave_scripts_styles() {
		/*wp_register_script('cpt-autosave', EE_CORE_ADMIN_URL . 'assets/ee-cpt-autosave.js', array('ee-serialize-full-array', 'event_editor_js'), EVENT_ESPRESSO_VERSION, TRUE );
		wp_enqueue_script('cpt-autosave');/**/ //todo re-enable when we start doing autosave again in 4.2

		//filter _autosave_containers
		$containers = apply_filters('FHEE__EE_Admin_Page_CPT_setup_autosave_js_containers', $this->_autosave_containers, $this );
		$containers = apply_filters('FHEE__EE_Admin_Page_CPT_' . get_class($this) . '_setup_autosave_js_containers', $containers, $this );

		wp_localize_script('event_editor_js', 'EE_AUTOSAVE_IDS', $containers ); //todo once we enable autosaves, this needs to be switched to localize with "cpt-autosave"

		$unsaved_data_msg = array(
			'eventmsg' => sprintf( __("The changes you made to this %s will be lost if you navigate away from this page.", 'event_espresso'), $this->_cpt_object->labels->singular_name),
			'inputChanged' => 0
			);

		wp_localize_script('event_editor_js', 'UNSAVED_DATA_MSG', $unsaved_data_msg);
	}




	/**
	 * overloading the EE_Admin_Page parent load_page_dependencies so we can get the cpt stuff added in appropriately
	 *
	 * @access public
	 * @return void
	 */
	public function load_page_dependencies() {

		//we only add stuff if this is a cpt_route!
		if ( !$this->_cpt_route ) {
			parent::load_page_dependencies();
			return;
		}


		//now let's do some automatic filters into the wp_system and we'll check to make sure the CHILD class automatically has the required methods in place.
		
		//the following filters are for setting all the redirects on DEFAULT WP custom post type actions	
		//let's add a hidden input to the post-edit form so we know when we have to trigger our custom redirects!  Otherwise the redirects will happen on ALL post saves which wouldn't be good of course!
		add_action('edit_form_after_title', array( $this, 'cpt_post_form_hidden_input') );

		//inject our Admin page nav tabs...
		//let's make sure the nav tabs are set if they aren't already
		//if ( empty( $this->_nav_tabs ) ) $this->_set_nav_tabs();
		add_action('post_edit_form_tag', array( $this, 'inject_nav_tabs' ) );

		//modify the post_updated messages array
		add_action('post_updated_messages', array( $this, 'post_update_messages' ), 10 );

		//add shortlink button to cpt edit screens.  We can do this as a universal thing BECAUSE, cpts use the same format for shortlinks as posts!
		add_filter( 'get_shortlink', array( $this, 'add_shortlink_button_to_editor' ), 10, 4 );

		//This basically allows us to change the title of the "publish" metabox area on CPT pages by setting a 'publishbox' value in the $_labels property array in the child class.
		if ( !empty($this->_labels['publishbox'] ) ) {

			$box_label = is_array( $this->_labels['publishbox'] ) && isset( $this->_labels['publishbox'][$this->_req_action]) ? $this->_labels['publishbox'][$this->_req_action] : $this->_labels['publishbox'];

			remove_meta_box( 'submitdiv', __( 'Publish' ), 'post_submit_meta_box', $this->_cpt_routes[$this->_req_action], 'side', 'core' );
			add_meta_box( 'submitdiv', $box_label, 'post_submit_meta_box', $this->_cpt_routes[$this->_req_action], 'side', 'core' );
		}

		//this is a filter that allows the addition of extra html after the permalink field on the wp post edit-form
		if ( method_exists( $this, 'extra_permalink_field_buttons' ) )
			add_filter('get_sample_permalink_html', array( $this, 'extra_permalink_field_buttons' ), 10, 4 );

		//insert our own post_stati dropdown
		add_action('post_submitbox_misc_actions', array($this, 'custom_post_stati_dropdown' ), 10 );

		//This allows adding additional information to the publish post submitbox on the wp post edit form
		if ( method_exists( $this, 'extra_misc_actions_publish_box' ) )
			add_action('post_submitbox_misc_actions', array( $this, 'extra_misc_actions_publish_box' ), 10 );

		//This allows for adding additional stuff after the title field on the wp post edit form.  This is also before the wp_editor for post description field.
		if ( method_exists( $this, 'edit_form_after_title' ) )
			add_action('edit_form_after_title', array( $this, 'edit_form_after_title' ), 10 );


		parent::load_page_dependencies();
		$this->modify_current_screen();
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_autosave_hooks'), 30 );
		//we route REALLY early.
		try {
			$this->_route_admin_request();
		} catch ( EE_Error $e ) {
			$e->get_error();
		}
	}




	/**
	 * add our custom post stati dropdown on the wp post page for this cpt
	 * @return string html for dropdown
	 */
	public function custom_post_stati_dropdown() {
		global $post;
		$statuses = EEM_CPT_Base::get_custom_post_statuses();
		$template_args = array(
			'cur_status' =>  $post->post_status,
			'statuses' => $statuses,
			'cur_status_label' => array_key_exists($post->post_status, $statuses) ? $statuses[$post->post_status] : ''
			);
		$template = EE_CORE_ADMIN_TEMPLATE . 'status_dropdown.template.php';
		EEH_Template::display_template( $template, $template_args );
	}





	public function setup_autosave_hooks() {
		$this->_set_autosave_containers();
		$this->_load_autosave_scripts_styles();
	}



	/**
	 * This is run on all WordPress autosaves AFTER the autosave is complete and sends along a $_POST object (available in $this->_req_data) containing:
	 * post_ID of the saved post
	 * autosavenonce for the saved post
	 *
	 * We'll do the check for the nonce in here, but then this method looks for two things:
	 * 1. Execute a method (if exists) matching 'ee_autosave_' and appended with the given route. OR
	 * 2. do_actions() for global or class specific actions that have been registered (for plugins/addons not in an EE_Admin_Page class.
	 *
	 *	PLEASE NOTE:
	 *	Data will be returned using the _return_json() object and so the $_template_args property should be used to hold the $data array.  We're expecting the following things set in template args.
	 *	1. $template_args['error'] = IF there is an error you can add the message in here.
	 *	2. $template_args['data']['items'] = an array of items that are setup in key index pairs of 'where_values_go' => 'values_to_add'.  In other words, for the datetime metabox we'll have something like
	 *	$this->_template_args['data']['items'] = array(
	 *		'event-datetime-ids' => '1,2,3';
	 *	);
	 *	Keep in mind the following things:
	 *	- "where" index is for the input with the id as that string.
	 *	- "what" index is what will be used for the value of that input.
	 *	
	 * 
	 * @return JSON object 
	 */
	public function do_extra_autosave_stuff() {

		//next let's check for the autosave nonce (we'll use _verify_nonce )		
		$nonce = isset( $this->_req_data['autosavenonce'] ) ? $this->_req_data['autosavenonce'] : NULL;
		$this->_verify_nonce( $nonce, 'autosave' );


		//make sure we define doing autosave (cause WP isn't triggering this we want to make sure we define it)
		if ( !defined('DOING_AUTOSAVE') ) define('DOING_AUTOSAVE', true);

		//if we made it here then the nonce checked out.  Let's run our methods and actions
		if ( method_exists( $this, '_ee_autosave_' . $this->_current_view ) ) {
			call_user_func( array( $this, '_ee_autosave_' . $this->_current_view ) );
		} else {
			$this->_template_args['success'] = TRUE;
		}

		do_action('AHEE__EE_Admin_Page_CPT_core_do_extra_autosave_stuff', $this );
		do_action('AHEE__EE_Admin_Page_CPT_core_do_extra_autosave_stuff_' . get_class( $this ), $this );

		//now let's return json
		$this->_return_json();		
	}





	/**
	 * This takes care of setting up default routes and pages that utilize the core WP admin pages.  Child classes can override the defaults (in cases for adding metaboxes etc.) but take care that you include the defaults here otherwise your core WP admin pages for the cpt won't work!
	 *
	 * @access protected
	 * @return void
	 */
	protected function _extend_page_config_for_cpt() {
		//before doing anything we need to make sure this runs ONLY when the loaded page matches the set page_slug
		if ( ( isset( $this->_req_data['page'] ) && $this->_req_data['page'] != $this->page_slug ) )
			return;

		//set page routes and page config but ONLY if we're not viewing a custom setup cpt route as defined in _cpt_routes
		if ( !empty( $this->_cpt_object ) ) {
			$this->_page_routes = array_merge( array(
				'create_new' => '_create_new_cpt_item',
				'edit' => '_edit_cpt_item'
				), $this->_page_routes );


			$this->_page_config = array_merge( array(
				'create_new' => array(
					'nav' => array(
						'label' => $this->_cpt_object->labels->add_new_item,
						'order' => 5
						),
					),
				'edit' => array(
					'nav' => array(
						'label' => $this->_cpt_object->labels->edit_item,
						'order' => 5,
						'persistent' => false,
						'url' => ''
						)
					) ),
				$this->_page_config
			);
		}

		//load the next section only if this is a matching cpt route as set in the cpt routes array.
		if ( !isset( $this->_cpt_routes[$this->_req_action] ) ) 
			return;

				
		$this->_cpt_route = isset( $this->_cpt_routes[$this->_req_action] ) ? TRUE : FALSE;
		//add_action('FHEE_admin_load_page_dependencies', array( $this, 'modify_current_screen') );


		if ( empty( $this->_cpt_object ) ) {
			$msg = sprintf( __('This page has been set as being related to a registered custom post type, however, the custom post type object could not be retrieved. There are two possible reasons for this:  1. The "%s" does not match a registered post type. or 2. The custom post type is not registered for the "%s" action as indexed in the "$_cpt_routes" property on this class (%s).'), $this->page_slug, $this->_req_action, get_class($this) );
			throw new EE_Error( $msg );
		}


		if ( $this->_cpt_route ) {
			$id = isset( $this->_req_data['post'] ) ? $this->_req_data['post'] : NULL;
			$this->_set_model_object( $id );
		}
		
	}





	/**
	 * Sets the _cpt_model_object property using what has been set for the _cpt_model_name and a given id.
	 *
	 * @access protected
	 * @param int $id The id to retrieve the model object for. If empty we set a default object.
	 * @return void
	 */
	protected function _set_model_object( $id = NULL ) {

		if ( empty( $this->_cpt_model_names ) || !isset( $this->_cpt_routes[$this->_req_action] ) || ( is_object( $this->_cpt_model_obj ) && $this->_cpt_model_obj->ID() == $id ) ) 
			return; //get out we either don't have a model name OR the object has already been set and it has the same id as what has been sent. 

		require_once( EE_MODELS . $this->_cpt_model_names[$this->_req_action] . '.model.php' );
		$model = call_user_func( array( $this->_cpt_model_names[$this->_req_action] , 'instance' ) );
		$this->_cpt_model_obj = !empty( $id ) ? $model->get_one_by_ID( $id ) : $model->create_default_object();
	}


	/**
	 * admin_init_global
	 * This runs all the code that we want executed within the WP admin_init hook.
	 * This method executes for ALL EE Admin pages.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_init_global() {

		$post = isset( $this->_req_data['post'] ) ? get_post( $this->_req_data['post'] ) : NULL;

		//its possible this is a new save so let's catch that instead
		$post = isset( $this->_req_data['post_ID'] ) ? get_post( $this->_req_data['post_ID'] ) : $post;
		$post_type = $post ? $post->post_type : false;

		$current_route = isset($this->_req_data['current_route']) ? $this->_req_data['current_route'] : 'shouldneverwork';

		$route_to_check = $post_type && isset( $this->_cpt_routes[$current_route]) ? $this->_cpt_routes[$current_route] : '';


		if ( $post_type === $route_to_check )
			add_filter('redirect_post_location', array( $this, 'cpt_post_location_redirect'), 10, 2 );

		//now let's filter redirect if we're on a revision page and the revision is for an event CPT.
		$revision = isset( $this->_req_data['revision'] ) ? $this->_req_data['revision'] : NULL;

		/**var_dump($this->_req_data);
		exit();/**/
		
		if ( !empty( $revision ) ) {
			$action = isset( $this->_req_data['action'] ) ? $this->_req_data['action'] : NULL;

			//doing a restore?
			if ( !empty( $action ) && $action == 'restore' ) {

				//get post for revision
				$rev_post = get_post( $revision );
				$rev_parent = get_post( $rev_post->post_parent );

				//only do our redirect filter AND our restore revision action if the post_type for the parent is one of our cpts.
				if ( $rev_parent && $rev_parent->post_type == $this->page_slug ) {
					add_filter('wp_redirect', array($this, 'revision_redirect'), 10, 2 );
					//restores of revisions
					add_action('wp_restore_post_revision', array($this, 'restore_revision'), 10, 2 );
				}
			}

		}

		//NOTE we ONLY want to run these hooks if we're on the right class for the given post type.  Otherwise we could see some really freaky things happen!


		if ( $post_type && $post_type === $route_to_check ) {
			//$post_id, $post
			add_action('save_post', array( $this, 'insert_update'), 10, 2 );

			//$post_id
			add_action('trashed_post', array( $this, 'trash_cpt_item' ), 10 );
			add_action('untrashed_post', array( $this, 'restore_cpt_item'), 10 );
			add_action('after_delete_post', array( $this, 'delete_cpt_item'), 10 );
		}

	}





	/**
	 * admin_footer_scripts_global
	 * Anything triggered by the 'admin_print_footer_scripts' WP hook should be put in here. This particular method will apply on ALL EE_Admin pages.
	 *
	 * @access public
	 * @return void 
	 */
	public function admin_footer_scripts_global() {
		$this->_add_admin_page_ajax_loading_img();
		$this->_add_admin_page_overlay();
	}	




	/**
	 * This is a wrapper for the insert/update routes for cpt items so we can add things that are common to ALL insert/updates
	 * @param  int    $post_id ID of post being updated
	 * @param  object $post    Post object from WP
	 * @return void          
	 */
	public function insert_update( $post_id, $post ) {

		//make sure that if this is a revision action that we don't do any updates!
		if ( isset( $this->_req_data['action'] ) && $this->_req_data['action'] == 'restore' )
			return;

		//check for autosave and update our req_data property accordingly.
		/*if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && isset( $this->_req_data['ee_autosave_data'] ) ) {
			foreach( (array) $this->_req_data['ee_autosave_data'] as $id => $values ) {

				foreach ( (array) $values as $key => $value ) {
					$this->_req_data[$key] = $value;
				}
			}
			
		}/**/ //TODO reactivate after autosave is implemented in 4.2

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return; //TODO we'll remove this after reimplementing autosave in 4.2
		$this->_insert_update_cpt_item( $post_id, $post );
	}







	/**
	 * This is a wrapper for the restore_cpt_revision route for cpt items so we can make sure that when a revision is triggered that we restore related items.  In order to work cpt classes MUST have a restore_cpt_revision method in them.  We also have our OWN action in here so addons can hook into the restore process easily.
	 * @param  int    $post_id     ID of cpt item
	 * @param  int    $revision_id ID of revision being restored
	 * @return void 	            
	 */
	public function restore_revision( $post_id, $revision_id ) {
		$this->_restore_cpt_item( $post_id, $revision_id );
		
		//global action
		do_action( 'AHEE_EE_Admin_Page_CPT__restore_revision', $post_id, $revision_id);

		//class specific action so you can limit hooking into a specific page.
		do_action( 'AHEE_EE_Admin_Page_CPT_' . get_class($this) . '__restore_revision', $post_id, $revision_id );
	}



	/**
	 * @see restore_revision() for details
	 * @param  int $post_id     ID of cpt item
	 * @param  int $revision_id ID of revision for item
	 * @return void              
	 */
	abstract protected function _restore_cpt_item( $post_id, $revision_id );




	/**
	 * Execution of this method is added to the end of the load_page_dependencies method in the parent, so that we can fix a bug where default core metaboxes weren't being called in the sidebar.  To fix we have to reset the current_screen using the page_slug (which is identical - or should be - to our registered_post_type id.)
	 *
	 * Also, since the core WP file loads the admin_header.php for WP (and there are a bunch of other things edit-form-advanced.php loads that need to happen really early) we need to load it NOW, hence our _route_admin_request in here. (Otherwise screen options won't be set).
	 * 
	 * @return void
	 */
	public function modify_current_screen() {
		//ONLY do this if the current page_route IS a cpt route
		if ( !$this->_cpt_route ) return;
		//routeing things REALLY early b/c this is a cpt admin page
		set_current_screen( $this->_cpt_routes[$this->_req_action]);
		$this->_current_screen = get_current_screen();
		$this->_current_screen->base = 'event-espresso'; 
		$this->_add_help_tabs(); //we make sure we add any help tabs back in!
		/*try {
			$this->_route_admin_request();
		} catch ( EE_Error $e ) {
			$e->get_error();
		}/**/
	}




	/**
	 * This allows child classes to modify the default editor title that appears when people add a new or edit an existing CPT item.
	 *
	 * This uses the _labels property set by the child class via _define_page_props.  Just make sure you have a key in _labels property that equals 'editor_title' and the value can be whatever you want the default to be.
	 * @param string $title The new title (or existing if there is no editor_title defined)
	 */
	public function add_custom_editor_default_title( $title ) {
		return isset( $this->_labels['editor_title'][$this->_cpt_routes[$this->_req_action]] ) ? $this->_labels['editor_title'][$this->_cpt_routes[$this->_req_action]] : $title;
	}



	/**
	 * hooks into the wp_get_shortlink button and makes sure that the shortlink gets generated
	 * @param string $shortlink   The already generated shortlink
	 * @param int    $id          Post ID for this item
	 * @param string $context     The context for the link
	 * @param bool   $allow_slugs Whether to allow post slugs in the shortlink.
	 */
	public function add_shortlink_button_to_editor( $shortlink, $id, $context, $allow_slugs ) {
		if ( !empty( $id ) && '' != get_option('permalink_structure') ) {
			$post = get_post( $id );
			if ( isset($post->post_type) && $this->page_slug == $post->post_type )
			$shortlink = home_url('?p=' . $post->ID);
		}
		return $shortlink;
	}




	/**
	 * overriding the parent route_admin_request method so we DON'T run the route twice on cpt core page loads (it's already run in modify_current_screen())
	 * @return void
	 */
	public function route_admin_request() {
		if ( $this->_cpt_route ) return;
		try {
			$this->_route_admin_request();
		} catch ( EE_Error $e ) {
			$e->get_error();
		}
	}




	/**
	 * Add a hidden form input to cpt core pages so that we know to do redirects to our routes on saves
	 * @return string html
	 */
	public function cpt_post_form_hidden_input() {
		echo '<input type="hidden" name="ee_cpt_item_redirect_url" value="' . $this->_admin_base_url . '" />';

		//we're also going to add the route value and the current page so we can direct autosave parsing correctly
		echo '<div id="ee-cpt-hidden-inputs">';
		echo '<input type="hidden" id="current_route" name="current_route" value="' . $this->_current_view . '" />';
		echo '<input type="hidden" id="current_page" name="current_page" value="' . $this->page_slug . '" />';
		echo '</div>';
	}




	/**
	 * This allows us to redirect the location of revision restores when they happen so it goes to our CPT routes.
	 * @param  string $location Original location url
	 * @param  int    $status   Status for http header
	 * @return string           new (or original) url to redirect to.
	 */
	public function revision_redirect( $location, $status ) {
		//get revision
		$rev_id = isset($this->_req_data['revision']) ? $this->_req_data['revision'] : NULL;

		//can't do anything without revision so let's get out if not present
		if ( empty( $rev_id ) )
			return $location;

		//get rev_post_data
		$rev = get_post($rev_id);

		$admin_url = $this->_admin_base_url;
		$query_args = array(
				'action' => 'edit',
				'post' => $rev->post_parent,
				'revision' => $rev_id,
				'message' => 5
			);

		$this->_process_notices( $query_args, TRUE );
		return self::add_query_args_and_nonce( $query_args, $admin_url );
	}




	/**
	 * This is the callback for the 'redirect_post_location' filter in wp-admin/post.php so that we can hijack the default redirect locations for wp custom post types that WE'RE using and send back to OUR routes.  This should only be hooked in on the right route.
	 * @param  string $location This is the incoming currently set redirect location
	 * @param  string $post_id  This is the 'ID' value of the wp_posts table
	 * @return string           the new location to redirect to
	 */
	public function cpt_post_location_redirect( $location, $post_id ) {
		//we DO have a match so let's setup the url
		
		//we have to get the post to determine our route
		$post = get_post($post_id);
		$edit_route = $this->_cpt_edit_routes[$post->post_type];

		//shared query_args
		$query_args = array( 'action' => $edit_route, 'post' => $post_id );
		$admin_url = $this->_admin_base_url;
		$message = '';
		$append = '';

		if ( isset( $this->_req_data['save'] ) || isset( $this->_req_data['publish'] ) ) {
			$status = get_post_status( $post_id );
			if ( isset( $this->_req_data['publish'] ) ) {
				switch ( $status ) {
					case 'pending':
						$message = 8;
						break;
					case 'future':
						$message = 9;
						break;
					default:
						$message = 6;
				}
			} else {
					$message = 'draft' == $status ? 10 : 1;
			}
		} else if ( isset( $this->_req_data['addmeta']) && $this->_req_data['addmeta'] ) {
			$message = 2;
			$append = '#postcustom';
		} else if ( isset( $this->_req_data['deletemeta']) && $this->_req_data['deletemeta'] ) {
			$message = 3;
			$append = '#postcustom';
		} elseif ( $this->_req_data['action'] == 'post-quickpress-save-cont' ) {
			$message = 7;
		} else {
			$message = 4;
		}

		//change the message if the post type is not viewable on the frontend
		$this->_cpt_object = get_post_type_object($post->post_type);
		$message = $message === 1 && !$this->_cpt_object->publicly_queryable ? 4 : $message;

		$query_args = array_merge( array( 'message' => $message ), $query_args );

		$this->_process_notices( $query_args, TRUE );
		return self::add_query_args_and_nonce( $query_args, $admin_url );
	}



	/**
	 * This method is called to inject nav tabs on core WP cpt pages
	 *
	 * @access public
	 * @return string html
	 */
	public function inject_nav_tabs() {
		//can we hijack and insert the nav_tabs?
		$nav_tabs = $this->_get_main_nav_tabs();
		//first close off existing form tag
		$html = '>';
		$html .= $nav_tabs;
		//now let's handle the remaining tag
		$html .= '<span></span';
		echo $html;
	}




	/**
	 * This just sets up the post update messages when an update form is loaded
	 *
	 * @access public
	 * @param  array $messages the original messages array
	 * @return array           the new messages array
	 */
	public function post_update_messages( $messages ) {
		global $post;
		$id = isset( $this->_req_data['post'] ) ? $this->_req_data['post'] : NULL;
		$id = empty( $id ) && is_object( $post ) ? $post->ID : NULL;

		$post_type = $post ? $post->post_type : false;

		/*$current_route = isset($this->_req_data['current_route']) ? $this->_req_data['current_route'] : 'shouldneverwork';

		$route_to_check = $post_type && isset( $this->_cpt_routes[$current_route]) ? $this->_cpt_routes[$current_route] : '';/**/

		$messages[$post->post_type] = array(
			0 => '', //Unused. Messages start at index 1.
			1 => sprintf( __( '%1$s updated. <a href="%2$s">View %1$s</a>', 'event_espresso'), $this->_cpt_object->labels->singular_name, esc_url( get_permalink( $id ) ) ),
			2 => __('Custom field updated'),
			3 => __('Custom field deleted.'),
			4 => sprintf( __( '%1$s updated.', 'event_espresso'), $this->_cpt_object->labels->singular_name ),
			5 => isset( $_GET['revision'] ) ? sprintf( __('%s restored to revision from %s', 'event_espresso'), $this->_cpt_object->labels->singular_name, wp_post_revision_title( (int) $_GET['revision'], FALSE ) ) : FALSE,
			6 => sprintf( __( '%1$s published. <a href="%2$s">View %1$s</a>', 'event_espresso'), $this->_cpt_object->labels->singular_name, esc_url( get_permalink( $id ) ) ),
			7 => sprintf( __( '%1$s saved.', 'event_espresso'), $this->_cpt_object->labels->singular_name ),
			8 => sprintf( __('%1$s submitted. <a target="_blank" href="%s">Preview %1$s</a>'), $this->_cpt_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink($id) ) ) ),
			9 => sprintf( __('%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %1$s</a>'), $this->_cpt_object->labels->singular_name, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($id) ) ),
			10 => sprintf( __('%1$s draft updated. <a target="_blank" href="%s">Preview page</a>'), $this->_cpt_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink($id) ) ) )
			);

		return $messages;
	}







	/**
	 * default method for the 'create_new' route for cpt admin pages.
	 * For reference what to include in here, see wp-admin/post-new.php
	 *
	 * @access  protected
	 * @return string template for add new cpt form
	 */
	protected function _create_new_cpt_item() {
		global $post, $title;
		$this->_template_args['post_type'] = $this->_cpt_routes[$this->_req_action];
		$this->_template_args['post_type_object'] = $this->_cpt_object;
		$title = $this->_template_args['post_type_object']->labels->add_new_item;
		$this->_template_args['editing'] = TRUE;
		wp_enqueue_script( 'autosave' );
		$this->_template_args['post'] = $post = get_default_post_to_edit( $this->_cpt_routes[$this->_req_action], TRUE );
		$this->_template_args['post_ID'] = $this->_template_args['post']->ID;
		$template = WP_ADMIN_PATH . 'edit-form-advanced.php';

		//modify the default editor title field with default title.
		add_filter('enter_title_here', array( $this, 'add_custom_editor_default_title' ), 10 );

		EEH_Template::display_template( $template, $this->_template_args );
	}





	/**
	 * default method for the 'edit' route for cpt admin pages
	 *
	 * For reference on what to put in here, refer to wp-admin/post.php
	 *
	 * @access protected
	 * @return string   template for edit cpt form
	 */			
	protected function _edit_cpt_item() {
		global $post, $title;
		$post_id = isset( $this->_req_data['post'] ) ? $this->_req_data['post'] : NULL;
		$post = !empty( $post_id ) ? get_post( $post_id, OBJECT, 'edit' ) : NULL;

		if ( empty ( $post ) ) {
			wp_die( __('You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?') );
		}


		$this->_template_args['editing'] = TRUE;
		$this->_template_args['post_ID'] = $post_id;
		$this->_template_args['post'] = $post;
		$this->_template_args['post_type'] = $this->_cpt_routes[$this->_req_action];
		$this->_template_args['post_type_object'] = $this->_cpt_object;
		if ( $last = wp_check_post_lock( $post->ID ) ) {
			add_action('admin_notices', '_admin_notice_post_locked' );
		} else {
			$this->_template_args['active_post_lock'] = wp_set_post_lock( $post->ID );
			wp_enqueue_script('autosave');
		}

		$title = $this->_cpt_object->labels->edit_item;

		if ( isset( $this->_cpt_routes[$this->_req_data['action']] ) && !isset( $this->_labels['hide_add_button_on_cpt_route']['edit_attendee'] ) ) {

			$this->_template_args['post_new_file'] = EE_Admin_Page::add_query_args_and_nonce( array('action' => 'create_new'), $this->_admin_base_url );
		}

		if ( post_type_supports($this->_cpt_routes[$this->_req_action], 'comments') ) {
			wp_enqueue_script('admin-comments');
			enqueue_comment_hotkeys_js();
		}

		$template = WP_ADMIN_PATH . 'edit-form-advanced.php';
		EEH_Template::display_template( $template, $this->_template_args );

	}



	/**
	 * some getters
	 */
	

	/**
	 * This returns the protected _cpt_model_obj property
	 * @return EE_Base_CPT object.
	 */
	public function get_cpt_model_obj() {
		return $this->_cpt_model_obj;
	}

}
