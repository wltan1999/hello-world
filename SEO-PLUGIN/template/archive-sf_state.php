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
	<div class='row'>
	<div style="float:left">
		
		<?php
		$url = home_url(add_query_arg(array(),$wp->request));
		$url = explode('/',$url);
		$state_slug =  array_slice($url, -1)[0];
		$state = get_term_by('slug',$state_slug,'sf_state');
		echo '<h1>'.$state->name.'</h1>';
		$cities = get_posts(array(
                    'post_type' => 'sf_city',
                       'numberposts' => -1,
                        'tax_query' => array(
                                    array(
                                        'taxonomy' => 'sf_state',
                                        'field' => 'slug',
                                        'terms' => $state->slug, // Where term_id of Term 1 is "1".
                                        'include_children' => false
                                        )
                                            )
                        ));
		foreach($cities as $city){
			//print_r($cities);
			//echo "<h2> In <a href='".home_url()."/".$state_slug."/".$city->post_name."'>".$city->post_title."</a>, we have the following services : </h2>";
			$services = get_the_terms( $city->ID, 'sf_service' );
			//echo '<ul>';
			foreach($services as $service)	{			
				echo "<li><strong> <a href='".home_url()."/".$state_slug."/".$city->post_name."/".$service->slug."'>".
							"In ".$city->post_name.", we have " .$service->name." serevice.
						</a></strong></li>";
			}
			//echo '</ul>';	
		}
		
		?>

	</div><!-- #primary -->
	<div style="float:right; font-size:20px">
	<?php
	foreach($cities as $city){				
		echo "<li><strong><a href='".home_url()."/".$state_slug."/".$city->post_name."'>".$city->post_title."</a><strong></li>";				
	}	
	?>
	</div>
	</div> <!-- row -->
</div><!-- .wrap -->

<?php get_footer();
