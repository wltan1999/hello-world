<?php
/* 
 * Class Name : SF_SEO
 * Description : Control the operation of SF SEO, inlcuding the WP hook action, create sf_city post type,
 * sf_state & sf_service taxonomies, prevent duplicate post slug
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//Load dependecies
require_once( dirname( dirname(__FILE__ )) . '/views/SF_SEO_ADMIN_PANEL.php' );
require_once( dirname( dirname(__FILE__ )) . '/model/class_sf_state.php' );
require_once( dirname( dirname(__FILE__ )) . '/model/class_sf_city.php' );
require_once( dirname( dirname(__FILE__ )) . '/model/class_sf_service.php' );

class SF_SEO{
    private static $_instance;
    public $admin_panel;
   public static function factory() {
		static $instnace;
		if ( empty( $instance ) ) {$instance = new self();}
		return $instance;
    }
              
    public function __construct() {
	$admin_panel = new SF_SEO_ADMIN_PANEL();
        add_action( 'init', array($this,'register_sf_city_cpt') );
        add_action( 'save_post', array( $this, 'save_post' ) );        
        add_action( 'init', array($this,'create_sf_taxonomy') );
        add_filter( 'wp_unique_post_slug', array($this,'prevent_slug_duplicates'), 10, 6 );
        add_filter( 'template_include', array($this,'template_loader' ));
        add_action( 'admin_init', array($this,'init_url')); 
       // add_action( 'create_sf_service', array($this,'insert_term_content'),10,2);
        //add_action( 'init', array($this,'init_sf_ciy_url'));   
        //add_action( 'create_term', array($this,'init_url'));
       // add_action( 'edited_' . 'sf_state', array($this,'init_url') );
        // add_action( 'edited_' . 'sf_service', array($this,'init_url') );
        add_action( 'edit_form_top', array($admin_panel,'show_required_field_error_msg' ));
        add_action( 'admin_menu', array($admin_panel,'add_plugin_menu'),9 );
        add_filter( 'parent_file', array($admin_panel,'sf_city_set_current_menu' ));
        add_action( 'admin_head-edit-tags.php', array($admin_panel,'remove_SF_Service_parent_category_in_taxonomy' ));               
        add_action( 'admin_head-post.php', array($admin_panel,'remove_SF_Service_parent_category_in_page' ));
        
  
	}
        
    // Register custom post type sf_city    
    public function register_sf_city_cpt() {
	$labels = array(	
       'name' => esc_html__( 'City', 'sf_city' ),
	'singular_name' => esc_html__( 'SF_City', 'sf_city'  ),
	'add_new' => esc_html__( 'Add New',  'sf_city' ),
	'add_new_item' => esc_html__( 'Add New City',  'sf_city' ),
	'edit_item' => esc_html__( 'Edit City', 'sf_city' ),
	'new_item' => esc_html__( 'New City', 'sf_city' ),
	'all_items' => esc_html__( 'Cities', 'sf_city' ),
	'view_item' => esc_html__( 'View City', 'sf_city' ),
	'search_items' => esc_html__( 'Search City', 'sf_city' ),
	'not_found' => esc_html__( 'No city found', 'sf_city' ),
	'not_found_in_trash' => esc_html__( 'No csity found in trash', 'sf_city' ),
	'parent_item_colon' => '',
	'menu_name' => esc_html__( 'sf_city', 'sf_city' )
		);
	$args = array(
            "labels" => $labels,
            "description" => "sf_city",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "has_archive" => true,
            "show_in_menu" => 'SF_SEO',
            "exclude_from_search" => false,
            "capability_type" => "page",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => false,
            "query_var" => true,
            "supports" => array( "title", "editor" ),
            "taxonomies" => array( "SF_States" ),
	);
	register_post_type( "sf_city", $args );    
    }
    
    //Create two taxonomy : sf_state & sf_service
    public function create_SF_taxonomy() {
        //sf_state
        register_taxonomy(
            'sf_state',
            'sf_city',
            array(
                'label'  => __( 'State' ),
                'labels' => array(
                    'menu_name' => __( 'sf_state', 'SF_SEO' )
                    ),
                'rewrite' => false,
                'hierarchical' => false,
                "show_in_menu" => false,
                'show_ui'      => true,
		'show_admin_column' => true,
                'meta_box_cb'       => Array($this,'SF_State_meta_box'), //Load metabox from SF_State_meta_box function
            )
        ); 
    
        //sf_service
        register_taxonomy(
            'sf_service',
            'sf_city',
            array('label'        => __( 'Service' ),
                  'labels'       => array(
                        'menu_name'    => __( 'Service', 'SF_SEO' )
                        ),
                  'rewrite' => false,
                  'hierarchical' => true,
                  'parent_item'  => null,
                  'parent_item_colon' => null,
                  "show_in_menu" => 'SF_SEO',
            )
        );    
    }
    
    //Prevent duplicate post slug, if found duplicate, append '-duplicate' behind the new slug
    public function prevent_slug_duplicates( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {
        $check_post_types = array(
            'post',
            'page',
            'sf_city'
        );
        if ( ! in_array( $post_type, $check_post_types ) ) {
            return $slug;
        }

        if ( 'sf_city' == $post_type ) {
        // Saving a custom_post_type post, check for duplicates in POST or PAGE post types
            $post_match = get_page_by_path( $slug, 'OBJECT', 'post' );
            $page_match = get_page_by_path( $slug, 'OBJECT', 'page' );

            if ( $post_match || $page_match ) {$slug .= '-duplicate';}
        } else {
        // Saving a POST or PAGE, check for duplicates in custom_post_type post type
            $custom_post_type_match = get_page_by_path( $slug, 'OBJECT', 'sf_city' );
                if ( $custom_post_type_match ) {$slug .= '-duplicate';}
        }
    return $slug;
    }

    //Custom metabox for sf_state, radio input is used to ensure only one state can be chosen.
    function SF_State_meta_box( $post ) {
	$terms = get_terms( 'sf_state', array( 'hide_empty' => false ) );
	$post  = get_post();
	$sf_states = wp_get_object_terms( $post->ID, 'sf_state', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
	$name  = '';
        if ( ! is_wp_error( $sf_states ) ) {
            if ( isset( $sf_states[0] ) && isset( $sf_states[0]->name ) ) {
                $name = $sf_states[0]->name;
            }
        }
	foreach ( $terms as $term ) {
    ?>
        <label title='<?php esc_attr_e( $term->name ); ?>'>
	<input type="radio" name="sf_state" value="<?php esc_attr_e( $term->name ); ?>" <?php checked( $term->name, $name ); ?>>
            <span><?php esc_html_e( $term->name ); ?></span>
        </label><br>
    <?php
        }
    }
    
    //Call function that register rewrited URLs for sf_state, sf_service and sf_city
    public function init_url(){
        SF_STATE::init_sf_state_urls();
        SF_SERVICE::init_sf_service_urls();
        SF_CITY::rewrite_sf_city_urls();
        flush_rewrite_rules();
    }
    
    // When new/old sf_city post is saved/updated, save the information and register new rewrite URL
    function save_post($post_id){
        SF_CITY::save_post($post_id);
        SF_SEO::init_url();
    }
    
    //Load custom template for sf_state, sf_servic, sf_city
    function template_loader( $template) {   
        if(is_single()&&get_post_type()=="sf_city"){
           return  dirname(dirname( __FILE__) ) . '/template/single-sf_city.php';
        }
        if(is_archive()){
            $tax = get_queried_object();
            if($tax->taxonomy=='sf_state')
                return  dirname(dirname( __FILE__) ) . '/template/archive-sf_state.php';
            if($tax->taxonomy=='sf_service')
               return  dirname(dirname( __FILE__) ) . '/template/archive-sf_service.php';
        }  
        return $template;
    }
    
  

    

    
}
SF_SEO::factory();
