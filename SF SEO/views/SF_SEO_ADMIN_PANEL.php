<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SF_SEO_ADMIN_PANEL{
    
    
    public function __construct() {
         
    }
    
    public function add_plugin_menu(){
        add_menu_page( 'SF_SEO', 'SF_SEO', 'manage_options', 'SF_SEO', array($this, 'display_plugin_dashboard_page'));
        add_submenu_page( 'SF_SEO', 'Dashboard','Dashboard', 'manage_options', 'SF_SEO', array($this, 'display_plugin_dashboard_page')); 
        add_submenu_page( 'SF_SEO', '','States', 'edit_posts', 'edit-tags.php?taxonomy=sf_state&post_type=sf_city', null);
        add_submenu_page( 'SF_SEO', '','Services', 'edit_posts', 'edit-tags.php?taxonomy=sf_service&post_type=sf_city', false);
    }
     
   public function display_plugin_dashboard_page() {
       include_once ( dirname( dirname(__FILE__) ) . '/template/dashboard.php' );
   }

   function show_required_field_error_msg( $post ) {
	if ( 'sf_city' === get_post_type( $post ) && 'auto-draft' !== get_post_status( $post ) ) {
	    $rating = wp_get_object_terms( $post->ID, 'sf_state', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
            if ( is_wp_error( $rating ) || empty( $rating ) ) {
		printf(	'<div class="error below-h2"><p>%s</p></div>',esc_html__( 'sf_state is mandatory for creating a new SF City post' ));
            }
	}
    }
   
    public function remove_SF_Service_parent_category_in_taxonomy(){
        if ( 'sf_service' != $_GET['taxonomy'] )
            return;

        $parent = 'parent()';

        if ( isset( $_GET['action'] ) )
            $parent = 'parent().parent()';
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($)
            { $('label[for=parent]').<?php echo $parent ?>.remove();});
        </script>       
    <?php
    }
    
    public function remove_SF_Service_parent_category_in_page(){
        global $current_screen;
        if ( 'sf_city' != $current_screen->post_type)
         return;
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($)
            { $('#newsf_service_parent').remove();});
        </script>   
    <?php
    }
    
    # Set the submenu as active/current while anywhere in SF SEO
    public function sf_city_set_current_menu( $parent_file ) {
        global $submenu_file, $current_screen, $pagenow;         
        if ( $current_screen->post_type == 'sf_city' ) {
            if ( $pagenow == 'post.php' ) {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }
            if ( $pagenow == 'edit-tags.php' &&  $current_screen->taxonomy == "sf_state" ) {
                $submenu_file = 'edit-tags.php?taxonomy=sf_state&post_type=' . $current_screen->post_type;
            }
            if ( $pagenow == 'edit-tags.php' &&  $current_screen->taxonomy == "sf_service" ) {
                $submenu_file = 'edit-tags.php?taxonomy=sf_service&post_type=' . $current_screen->post_type;
            }
 
            $parent_file = 'SF_SEO';
        }
        return $parent_file;
    }
    
    
}
