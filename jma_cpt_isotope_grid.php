<?php
/*
Plugin Name: JMA Custom Post Type Isotope Displays
Description: This plugin creates a grid with isotope filtering from a custom taxonomy the default is the portfolio category of the portfolio_item cpt
Version: 1.0
Author: John Antonacci
Author URI: http://cleansupersites.com
License: GPL2
*/
function jma_iso_gallery_files() {
	    wp_enqueue_style( 'jma_isotope_css', plugins_url('/jma_isotope_css.css', __FILE__) );
	    /* I would suggest just styling in your child theme to save a document load */
	    //wp_enqueue_script( 'jma_gallery_isotope', plugins_url('/isotope.pkgd.min.js', __FILE__), array( 'jquery' ) );
	    /* if for some reason isotope is not loading you can uncomment above and change dependency below from isotope to jma_gallery_isotope */
	    wp_enqueue_script( 'jma_isotope_js', plugins_url('/jma_isotope.js', __FILE__), array( 'jquery', 'isotope' ) );
}
add_action( 'wp_enqueue_scripts', 'jma_iso_gallery_files' );

function jma_gallery_display($ids, $thumb_size = 'tb_grid' ){

	$return = '';
	foreach($ids as $i => $id){
		$full_array = wp_get_attachment_image_src($id, 'full');
		$image_string = $i? get_the_title($id): wp_get_attachment_image($id, $thumb_size);
		$return .= '<a href=' . $full_array[0] . '>' . $image_string . '</a>';

	}
	return $return;
}

function jma_iso_display($atts){
	ob_start();
	$x = '';
	extract( shortcode_atts( array(
		'post_type' => 'portfolio_item',
		'taxonomy' => 'portfolio',
		'orderby' => 'title',//date, name (slug), title, rand, menu_order, meta_value (Note that a 'meta_key=keyname' must also be present in the query)
		'order' => 'ASC', //ASC, DESC
		'posts_per_page' => '-1',
		'itemclass' => 'itemclass',
		'meta_key' => '',
		'thumb_size' => 'tb_grid',
        'default_cat' => ''
		), $atts ) );

	$jma_terms = get_terms( array ($taxonomy) );//echo '<pre>'; print_r($jma_terms); echo '</pre>';

	//add buttons
	echo '<div class="isotope-wrap" data-init="' . $default_cat . '">';
	$new = (array(term_id => 99999, slug => '*', name => 'All', depth => 0));//add the 'all' button to the array of category results
	if(!$default_cat)array_unshift($jma_terms, $new);

	foreach($jma_terms as $i => $jma_term){//build new array with value that reflects item depth
		$tax_ancestors = get_ancestors($jma_term->term_id, $taxonomy);
		$this_count = count(get_ancestors($jma_term->term_id, $taxonomy));
		$max_count = $this_count > $max_count? $this_count: $max_count;//need max value for next loop      get_term_children( $term, $taxonomy )
		$jma_term = (array)$jma_term;
		$jma_term['tax_ancestors'] = $tax_ancestors;
		$new_array[] = $jma_term;
	}
	echo '<div id="filters">';
	for ($i=1; $i < ($max_count+2); $i++) {
		$hidden = ($i == 1)? '': 'jma-hide ';
		echo '<div class="' . $hidden . 'button-group btn-group clearfix" display: block" data-level="' . $i . '">';
		foreach($new_array as $ii => $jma_term){
			if(count($jma_term['tax_ancestors']) == ($i-1)){
				$parents = '';
				foreach($jma_term['tax_ancestors'] as $t => $tax_ancestor){//print_r($tax_ancestor);
					$term_obj = get_term_by('id', $tax_ancestor, $taxonomy);
					$leader = $t? ' ': '';
					$parents .= $leader . $term_obj->slug;
				}
				$kids = '';
				foreach(get_term_children( $jma_term['term_id'], $taxonomy ) as $t => $child_id){
					$term_obj = get_term_by('id', $child_id, $taxonomy);
					$leader = $t? ' ': '';
					$kids .= $leader . $term_obj->slug;
				}
				$filter = $jma_term['slug'];
				$is_checked = !($i-1) && !$ii? ' is-checked': '';
				echo $sep . '<div class="btn-wrap"><button type="button" class="btn btn-default trigger cat-id-' . $jma_term['term_id'] . $is_checked . '" data-kids="' . $kids . '" data-parents="' . $parents . '" data-filter="' . $filter . '">' . $jma_term['name']  . '</button></div>';
			}
		}
		echo '</div><!--button-group-->';
	}
	echo '</div><!--filters"-->';




	//end buttons

	$args = array('post_type' => $post_type, 'orderby' => $orderby, 'order' => $order, 'posts_per_page' => $posts_per_page);//name(slug), menu_order plugin= simple page ordering
	if($meta_key)
		$args['meta_key'] = $meta_key;

	$iso_query = new WP_Query( $args );

	// The Loop
	if ( $iso_query->have_posts() ) {
		echo '<div class="jma-iso-items-wrap" style="position: relative">';
		while ( $iso_query->have_posts() ) {
			$iso_query->the_post();
			$post_id = $iso_query->post->ID;

			//build class strting for isotope
			$jma_taxes = wp_get_post_terms( $post_id, $taxonomy );
			$class_string = "";

			$tax_string = '';
			foreach($jma_taxes as $jma_tax){
				$ancestors = get_ancestors($jma_tax->term_id , $taxonomy);
				$tax_string .= " " . $jma_tax->slug;
				foreach ($ancestors as $ancestor) {
					$term_obj =  get_term_by('id', $ancestor, $taxonomy);//echo '<pre>';print_r($term_obj);echo '</pre>';
					$tax_string .= " " . $term_obj->slug;
				}
			}

			echo '<div id="iso-' . $post_id . '" class="' . $itemclass . ' jma-iso-item isoid-' . $post_id . ' ' . $tax_string . '">';
			echo '<div  class="jma-iso-item-inner">';

			require 'jma_cpt_isotope_grid_content.php';

		    echo '</div><!--jma-iso-item-inner-->';
		echo '<div style="clear: both"></div>';
		    echo '</div><!--jma-iso-item-->';
		}
		echo '</div><!--jma-iso-items-wrap-->';
	}
	echo '</div><!--isotope-wrap-->';
	$x = ob_get_contents();
	ob_end_clean();
	wp_reset_postdata();
	return str_replace("\r\n", '', $x);
}
add_shortcode('cpt_isotope_display','jma_iso_display');

