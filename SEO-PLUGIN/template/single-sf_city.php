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
		
		<?php
		$url = home_url(add_query_arg(array(),$wp->request));
		$url = explode('/',$url);
		$city_slug =  array_slice($url, -1)[0];
		$state_slug=	array_slice($url, -2)[0];
		$state = get_term_by('slug',$state_slug,'sf_state');
		//echo "<h1> <a href='".home_url()."/".$state_slug."'>".$state->name."</a></h1>";
		$city = get_posts(array(
                    'post_type' => 'sf_city',
                    'numberposts' => -1,
					'name' => $city_slug,
					'post_status' => 'publish',
                     ));
		echo "<h1>". $city[0]->post_title."</h1>";			 
		//echo '<h2> In '.$city[0]->post_title. ', we have the following services : </h2>';
		$services = get_the_terms( $city[0]->ID, 'sf_service' );		
		foreach($services as $service)	{			
			echo "<li><strong> <a href='".home_url()."/".$state->slug."/".$city[0]->post_name."/".$service->slug."'>".
					$service->name." services in ".$city[0]->post_title."
			</a></strong></li>";			
		}
				
		
		?>

	</div><!-- #primary -->
	<?php //get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
