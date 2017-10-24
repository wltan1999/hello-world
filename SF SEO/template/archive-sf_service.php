<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">
	<div>
	<h2><Strong>
		
		<?php	
			$url = home_url(add_query_arg(array(),$wp->request));
			$url = explode('/',$url);
			
			$state_slug=	array_slice($url, -3)[0];
			$state = get_term_by('slug',$state_slug,'sf_state');
			$state_name = $state->name;
			
			$city_slug =  array_slice($url, -2)[0];
			$city = get_posts(array(
                    'post_type' => 'sf_city',
                    'numberposts' => -1,
					'name' => $city_slug,
					'post_status' => 'publish',
                     ));
			$city_name = $city[0]->post_title;	
			
			$service_slug=	array_slice($url, -1)[0];
			$service = get_term_by('slug',$service_slug,'sf_service');
			$service_name = $service->name;
			//$service_description = term_description($service->id,'sf_service');	
			$service_description = $service->description;
			$service_description = str_replace("{{STATE}}",$state_name,$service_description);
			$service_description = str_replace("{{CITY}}",$city_name,$service_description);
			$service_description = str_replace("{{SERVICE}}",$service_name,$service_description);
			$service->description = $service_description;
			echo $service_description;
			
		?>
	</strong></h2>
	</div><!-- #primary -->
	<?php //get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
