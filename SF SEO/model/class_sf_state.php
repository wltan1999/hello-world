<?php

/* 
 * Class name : SF_STATE
 * Description : Manage the operation of sf_state including rewrite sf_state url
 */

class SF_STATE{
   private static $_instance;
   // public $SF_STATE;
   public static function factory() {
		static $instnace;
		if ( empty( $instance ) ) {$instance = new self();}
		return $instance;
    }
    
    public function __construct() {

    }
    
    /* Rewrite the URL for sf_state
     * Permalink strucure : www.example.com/state
     * Link query : sf_service = state
     * 
     */  
    public static function init_sf_state_urls( ) {     	 
        //Get all sf_states
        $terms = get_terms( array(
                    'taxonomy' => 'sf_state',
                    'hide_empty' => false,
        ) );			
        foreach ( $terms as $term ) {
            $slug=$term->slug;
            add_rewrite_rule( '^' .  $slug . '$', 'index.php?sf_state='. $slug,'top');   
        }
    }
}

SF_STATE::factory();