<?php
/*
	Plugin Name: VU Protect
	Description: Protect Pages/Posts from access without VUnetID.
	Author: JTG
	Version: 1.0.0
*/
class VU_Protect_Plugin {

    public function __construct() {

        add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
        add_action( 'admin_init', array( $this, 'add_acf_variables' ) );

        add_filter( 'acf/settings/path', array( $this, 'update_acf_settings_path' ) );
        add_filter( 'acf/settings/dir', array( $this, 'update_acf_settings_dir' ) );

        include_once( plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields/acf.php' );

        $this->vu_protect_site_options();

        add_action( 'admin_init', array( $this, 'vu_protect_page_options' ) );
        add_action( 'admin_init', array( $this, 'vu_protect_post_options' ) );

        add_action( 'template_redirect', array( $this, 'vu_protect_redirect' ) );

    }

    public function update_acf_settings_path( $path ) {
        $path = plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields/';
        return $path;
    }

    public function update_acf_settings_dir( $dir ) {
        $dir = plugin_dir_url( __FILE__ ) . 'vendor/advanced-custom-fields/';
        return $dir;
    }

    public function create_plugin_settings_page() {

    	$page_title = 'VU Protect Settings';
    	$menu_title = 'VU Protect';
    	$capability = 'manage_options';
    	$slug = 'vu_protect';
    	$callback = array( $this, 'plugin_settings_page_content' );
    	$icon = 'dashicons-lock';
    	$position = 100;

    	add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }

    public function plugin_settings_page_content() {
        do_action('acf/input/admin_head');
        do_action('acf/input/admin_enqueue_scripts');

        $options = array(
        	'id' => 'acf-form',
        	'post_id' => 'options',
        	'new_post' => false,
        	'field_groups' => array( 'acf_vu-protect-site' ),
        	'return' => admin_url('admin.php?page=vu_protect'),
        	'submit_value' => 'Update',
        );
        acf_form( $options );
    }

    public function add_acf_variables() {
        acf_form_head();
    }

    public function vu_protect_site_options() {

	    // Protect Site
		if(function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_vu-protect-site',
				'title' => 'VU Protect Site',
				'fields' => array (
					array (
						'key' => 'field_5734deb2bef50',
						'label' => 'Protect Site',
						'name' => 'protect_site',
						'type' => 'true_false',
						'instructions' => 'Should this whole site be accessed only by users signed in with their VUnetID?',
						'message' => '',
						'default_value' => 0,
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'vu_protect',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'normal',
					'layout' => 'default',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
		}

    }

    public function vu_protect_page_options() {
	    if(function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_vu-protect-pages',
				'title' => 'VU Protect Pages',
				'fields' => array (
					array (
						'key' => 'field_5734dcfd27ba8',
						'label' => 'Protect Page',
						'name' => 'protect_page',
						'type' => 'true_false',
						'instructions' => 'Should this page be accessed only by users signed in with their VUnetID?',
						'message' => 'Yes - Protect this page.',
						'default_value' => 0,
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'page',
							'order_no' => 1,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'side',
					'layout' => 'default',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
		}

    }

    public function vu_protect_post_options() {
	    if(function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_vu-protect-posts',
				'title' => 'VU Protect Posts',
				'fields' => array (
					array (
						'key' => 'field_5734dda7e3ae1',
						'label' => 'Protect Post',
						'name' => 'protect_post',
						'type' => 'true_false',
						'instructions' => 'Should this post be accessed only by users signed in with their VUnetID?',
						'message' => 'Yes - Protect this post.',
						'default_value' => 0,
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'post',
							'order_no' => 1,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'side',
					'layout' => 'default',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
		}

    }

    public function vu_protect_redirect() {

		// Protect Page
	    if ( is_page() ) {
		    if ( get_field( 'protect_page' ) == true ) {
		    	if ( !is_user_logged_in() ) {
			    	wp_redirect( wp_login_url() ); exit;
				}
			}
		}

		// Protect Post
		if ( is_single() ) {
		    if ( get_field( 'protect_post' ) == true ) {
		    	if ( !is_user_logged_in() ) {
					wp_redirect( wp_login_url() ); exit;
				}
			}
		}

		// Protect Site
		if ( get_field( 'protect_site' ) == true ) {
		    if ( !is_user_logged_in() ) {
				wp_redirect( wp_login_url() ); exit;
			}
		}

	}

}
new VU_Protect_Plugin();