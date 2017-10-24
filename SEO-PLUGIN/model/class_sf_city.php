<?php

/* 
 * Class name : SF_CITY
 * Description : Used to manage sf_city including save the updated deails, rewrite new urls
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SF_CITY{
   private static $_instance;

   public static function factory() {
		static $instnace;
		if ( empty( $instance ) ) {$instance = new self();}
		return $instance;
    }
    
    public function __construct() {

    }
    
    // Function to save the sf_state and update the new slug if title is changed
    public static function save_post( $post_id ) {
	
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) || 'revision' == get_post_type( $post_id ) ) {
            return;
	}

	$sf_state = sanitize_text_field( $_POST['sf_state'] ); //get sf_state
	if ( !empty( $sf_state ) ) {		
		$term = get_term_by( 'name', $sf_state, 'sf_state' ); //get sf_state
		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
			wp_set_object_terms( $post_id, $term->term_id, 'sf_state', false ); //set the sf_state to sf_city
		}
	}
 
        $post = wp_get_single_post($post_id);
        $new_slug=sanitize_title($post->post_title); //get new slug from title
        if($post->post_name!=$new_slug){ //if the slug is changed , update the new slug
            if (!wp_is_post_revision($post_id)){
               remove_action('save_post',array($this,'save_post')); //prevent the infinite loop in save_post
                wp_update_post(array(
                            'ID'=>$post_id,
                            'post_name'=>$new_slug,
                ));
                add_action('save_post',array($this,'save_post')); //re-add save_post
            }
          }      
    }
    
    /* Rewrite the URL for sf_city
     * Permalink strucure : www.example.com/ state / city
     * Link query : sf_city = city
     * 
     */
    public static function rewrite_sf_city_urls($post_id="",$post="",$update=false, $init=true ) {    
        $posts = get_posts(array(
                'post_type' => 'sf_city',
                ));        
        foreach($posts as $post){
            if ( $cats = get_the_terms( $post->ID, 'sf_state' ) ) //Get the sf_state from sf_city post 
                {$state= current( $cats )->slug;}      
            add_rewrite_rule( '^'.$state.'/' .$post->post_name. '$', 'index.php?sf_city='. $post->post_name,'top');
        }       
    }     
}

SF_CITY::factory();