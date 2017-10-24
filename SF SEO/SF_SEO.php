<?php

/**
 * Plugin Name:SF SEO Page
 */




require_once( dirname( __FILE__ ) . '/controller/class_SF_SEO.php' );
register_activation_hook(__FILE__,'my_custom_plugin_activate');
function my_custom_plugin_activate(){   
    flush_rewrite_rules();
}

//Add content editor and remove the existing description editor
add_action( "sf_service_edit_form_fields",'add_form_fields_example', 10, 2);    
function add_form_fields_example($term, $taxonomy){
 ?>
    <tr valign="top">
       <th scope="row">Content</th>
        <td>
          <?php wp_editor(html_entity_decode($term->description), 'description', array('media_buttons' => false)); ?>
          <script>
            jQuery(window).ready(function(){
            jQuery('label[for=description]').parent().parent().remove();
             });
          </script>
       </td>
    </tr>
 <?php
}

 /**Prevent access to sf_city page with URL sturcture www.example.com/sf_city
 * When the sf_city page is loaded from stucture www.example.com/sf_city, 
 * modify the link to invalid stucture so no page is found
 */
add_filter('post_type_link',  'sf_city_permalink', 10, 2);   
function sf_city_permalink($link, $post) {
  if ( $post->post_type == 'sf_city' ) {
        $link = ""; 
     }
 return $link;
}

add_action( 'created_term', 'insert_term_content',10,3);
  function insert_term_content($term, $tt,$taxonomy){
    wp_update_term($term, 'sf_service', array(
    'description' => 'In {{STATE}}  {{CITY}} we provide {{SERVICE}}.'
    ));
    }