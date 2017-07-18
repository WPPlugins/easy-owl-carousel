<?php
/**
 * Plugin Name: Easy Owl Carousel
 * Description: This plugin helps to add carousel items in your wordpress website. This plugin is based upon Owl Carousel 2. It is very easy to use with a simple shortcode.Just use this code [owl_carousel] to publish your owl carousel.
 * Version: 1.1.0
 * Author: Bhaskar Biswas
 * License: GPL2
 */
 
function owl_theme_setup() {
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'owl_theme_setup' );

// Filter for widget
add_filter('widget_text', 'do_shortcode');

//Latest jquery callback from wordpress

function wp_latest_jquery_setup() {
	wp_enqueue_script('jquery');
}
add_action('init', 'wp_latest_jquery_setup');



//Owl Carousel style and script set up

function owl_carousel_style_script_setup() {
	wp_enqueue_style('owl-carusel-main-css', plugins_url('/owlcarousel/css/owl.carousel.min.css', __FILE__ ));
	
	wp_enqueue_style('owl-carusel-theme-css', plugins_url('/owlcarousel/css/owl.theme.default.min.css', __FILE__ ));
	
    wp_enqueue_script('owl-carousel-js', plugins_url('/owlcarousel/js/owl.carousel.min.js', __FILE__ ),  array('jquery'));
}
add_action('wp_enqueue_scripts', 'owl_carousel_style_script_setup');



//Owl Carousel active js script set up

function owl_plugin_js_setup(){?>
<script type="text/javascript">
jQuery(document).ready(function(){
  jQuery(".owl-carousel").owlCarousel({
    loop:true,
    autoplay:true,
    autoplayTimeout:2000,
    margin:10,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:5
        }
    }
});
});

</script>

<?php	
}
add_action('wp_head','owl_plugin_js_setup');



//Owl Carousel Custom Post and Query

add_action( 'init', 'owl_carousel_custom_post' );
function owl_carousel_custom_post() {
	register_post_type( 'owl-carousel-items',
		array(
			'labels' => array(
				'name' => __( 'Owl Carousels' ),
				'singular_name' => __( 'Owl Carousel' ),
				'add_new_item' => __( 'Add New Owl Carousel' ),
				'edit_item' => __( 'Edit Owl Carousel' ),
				'view_item' => __( 'View Owl Carousel' ),
				'featured_image' => __( 'Select Your Image For Carousel' ),
				'set_featured_image' => __( 'Set Carousel Image' ),
				'remove_featured_image' => __( 'Remove Carousel Image' ),
				'use_featured_image' => __( 'Use as Carousel Image' )
			),
			'public' => true,
			'supports' => array( 'title', 'thumbnail' ),
			'has_archive' => true,
			'rewrite' => array('slug' => 'owl-carousel-item'),
		)
	);
	register_taxonomy(
		'owl_carousel_cat',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'owl-carousel-items',                  //post type name
		array(
			'hierarchical'      => true,
			'label'             => 'Owl Carousel Category',  //Display name
			'query_var'         => true,
			'rewrite'           => array(
			'slug'              => 'owl-carousel-category', // This controls the base slug that will display before each term
			'with_front'        => true, // Don't display the category base before
 
				),
			'show_admin_column' => true
				
			)
	);
}

//Owl Carousel Shortcode Query

function owl_carousel_shortcode($atts){
	extract( shortcode_atts( array(
		'category' => '',
		'count' => '',
	), $atts, 'wishlist' ) );
	
    $q = new WP_Query(
        array('posts_per_page' => $count, 'post_type' => 'owl-carousel-items', 'owl_carousel_cat' => $category)
        );		
		
		
	$list = '<div class="owl-carousel owl-theme">';
	while($q->have_posts()) : $q->the_post();
		$idd = get_the_ID();
		$list .= '

			<div class="item">'.get_the_post_thumbnail().'</div>

		
		';        
	endwhile;
	$list.= '</div>';
	wp_reset_query();
	return $list;
}
add_shortcode('owl_carousel', 'owl_carousel_shortcode');

//featured image metabox customise

add_action('do_meta_boxes', 'owl_carousel_image_box');
function owl_carousel_image_box(){
	remove_meta_box( 'postimagediv', 'owl-carousel-items', 'side' );
	add_meta_box('postimagediv', __('Select Your Image For Carousel'), 'post_thumbnail_meta_box', 'owl-carousel-items', 'normal', 'high');
	}


?>