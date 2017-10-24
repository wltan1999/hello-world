<?php

/* 
 * Class name : SF_SERVICE
 * Description : Manage sf_service inlcuding rewrite sf_service URL
 */

class SF_SERVICE{
   private static $_instance;
   public static function factory() {
		static $instnace;
		if ( empty( $instance ) ) {$instance = new self();}
		return $instance;
    }
    
    public function __construct() {

    }
       
    /* Rewrite the URL for sf_service
     * Permalink strucure : www.example.com/ state / city /service
     * Link query : sf_service = service
     * 
     */
    public static function init_sf_service_urls( ) {
        //Collect all added sf_services
        $terms = get_terms( array(
                'taxonomy' => 'sf_service',
                'hide_empty' => false,
                ) );			
        foreach ( $terms as $term ) {
            $slug=$term->slug; 
            SF_SERVICE::add_sf_service_url($slug);
         }	        
    }   

    //Rewrite sf_service url
    public static function add_sf_service_url($service){
        //Collect all sf_cities that provide this sf_service
        $posts = get_posts(array(
                    'post_type' => 'sf_city',
                     'numberposts' => -1,
                     'tax_query' => array(
                                    array(
                                        'taxonomy' => 'sf_service',
                                        'field' => 'slug',
                                        'terms' => $service,
                                        'include_children' => false
                                         )
                                    )
                            ));
    
        foreach($posts as $post){
           if ( $cats = get_the_terms( $post->ID, 'sf_state' ) ) {
                $state= current( $cats )->slug; //Get the sf_state
            } 
         add_rewrite_rule( '^'. $state.'/'. $post->post_name.'/' . $service . '$', 'index.php?sf_service='. $service,'top'); 
        }
    }   
}

SF_SERVICE::factory();