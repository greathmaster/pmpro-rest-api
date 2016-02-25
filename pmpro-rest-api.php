<?php
/*
Plugin Name: PMPro REST API
Plugin URI: 
Description: REST Endpoints and Routes for PMPro
Version: 1.0
Author: Harsha Venkatesh
Author URI: 
*/

if ( ! class_exists( 'WP_REST_Controller' ) ) {
	require_once ABSPATH . '/wp-content/plugins/rest-api/lib/endpoints/class-wp-rest-controller.php';
}

class PMPro_REST_API_Routes extends WP_REST_Controller
{
	public function register_routes()
	{
		$namespace = 'wp/v2';
		register_rest_route( $namespace, '/users/(?P<id>\d+)'.'/pmpro_membership_level' , 
		array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_user_level' ),
				'permission_callback' => array( $this, 'get_permissions_check' ),
		),));
		
		register_rest_route( $namespace, '/posts/(?P<post_id>\d+)'.'/user_id/(?P<user_id>\d+)/pmpro_has_membership_access' , 
		array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_has_membership_access' ),
				'permission_callback' => array( $this, 'get_permissions_check' ),
		),));
	}
	
	//Ex:http://example.com/wp-json/wp/v2/users/2/pmpro_membership_level
	function get_user_level($request)
	{
		$params = $request->get_params();
		
		$user_id = $params['id'];
		
		$level = pmpro_getMembershipLevelForUser($user_id);
		return new WP_REST_Response((array)$level, 200 );
	}
	
	//Ex: http://example.com/wp-json/wp/v2/posts/58/user_id/2/pmpro_has_membership_access
	function get_has_membership_access($request)
	{
		$params = $request->get_params();
		$post_id = $params['post_id'];
		$user_id = $params['user_id'];
		
		$has_access = pmpro_has_membership_access($post_id, $user_id);
		return $has_access;
		
	}
	
	function get_permissions_check($request)
	{
		return current_user_can('edit_others_posts' );
	}
}

function register_custom_routes()
{
    $pmpro_members = new PMPro_REST_API_Routes;
    $pmpro_members->register_routes();
}

add_action( 'rest_api_init', 'register_custom_routes', 5 );