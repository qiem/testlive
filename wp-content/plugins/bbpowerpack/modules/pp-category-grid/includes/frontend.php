<?php
if ( ! isset( $settings->post_type ) ) {
	$post_type = 'post';
} else {
	$post_type = $settings->post_type;
}
$var_tax_type = 'posts_' . $post_type . '_tax_type';
$tax_type     = $var_cat_matching = $var_cat = '';
if ( isset( $settings->$var_tax_type ) ) {
	$tax_type         = $settings->$var_tax_type;
	$var_cat          = 'tax_' . $post_type . '_' . $tax_type;
	$var_cat_matching = $var_cat . '_matching';
}

$cat_match    = isset( $settings->$var_cat_matching ) ? $settings->$var_cat_matching : false;
$ids          = isset( $settings->$var_cat ) ? explode( ',', $settings->$var_cat ) : array();
$taxonomy     = isset( $tax_type ) ? $tax_type : '';
$orderby      = isset( $settings->order_by ) ? $settings->order_by : 'name';
$order        = isset( $settings->order ) ? $settings->order : 'ASC';
$show_count   = 1;
$pad_counts   = 1;
$hierarchical = 1;
$title        = '';
$empty        = ( isset( $settings->show_empty ) && 'yes' === $settings->show_empty ) ? false : true;

$taxonomy_thumbnail_enable     = BB_PowerPack_Taxonomy_Thumbnail::$taxonomy_thumbnail_enable;
$taxonomy_thumbnail_taxonomies = BB_PowerPack_Taxonomy_Thumbnail::$taxonomies;

$args = array(
	'taxonomy'     => $taxonomy,
	'orderby'      => $orderby,
	'order'        => $order,
	'show_count'   => $show_count,
	'pad_counts'   => $pad_counts,
	'hierarchical' => $hierarchical,
	'title_li'     => $title,
	'hide_empty'   => $empty,
);

// Order by meta value arg.
if ( strstr( $orderby, 'meta_value' ) && isset( $settings->order_by_meta_key ) ) {
	$args['meta_key'] = $settings->order_by_meta_key;
}

if ( $cat_match && 'related' !== $cat_match && ! empty( $ids ) ) {
	if ( isset( $settings->display_data ) && ( 'children_only' === $settings->display_data || 'default' === $settings->display_data ) && ! empty( $ids[0] ) ) {
		//only single value is allowed so we have made new custom function, get_child_categories()
		$args['parent'] = $ids;
	} else {
		$args['include'] = $ids;
	}
}
if ( ( ! $cat_match || 'related' === $cat_match ) && ! empty( $ids ) ) {
	if ( isset( $settings->display_data ) && ( 'parent_only' !== $settings->display_data ) && ! empty( $ids[0] ) ) {

		foreach ( $ids as $term_id ) {
			$tmp_ids = get_term_children( $term_id, $taxonomy );
			$ids     = array_merge( $ids, $tmp_ids );
		}
		$args['exclude'] = $ids;
	} else {
		$args['exclude'] = $ids;
	}
}

// Show child terms on taxonomy archive page.
if ( isset( $settings->on_tax_archive ) && ( is_tax() || is_category() || is_tag() ) ) {
	$current_object = get_queried_object();
	if ( 'children_only' === $settings->on_tax_archive ) {
		$args['child_of'] = $current_object->term_id;
	}
	if ( 'parent_only' === $settings->on_tax_archive && intval( $current_object->parent ) > 0 ) {
		$args['include'] = (array) $current_object->parent;
	}
}

$args = apply_filters( 'pp_category_grid_query_args', $args, $settings );

if ( isset( $settings->display_data ) && 'children_only' === $settings->display_data && isset( $args['parent'] ) && ! empty( $args['parent'][0] ) ) {
	$all_categories = PPCategoryGridModule::get_categories( $args, 'children_only' );
} elseif ( isset( $settings->display_data ) && 'default' === $settings->display_data && isset( $args['parent'] ) && ! empty( $args['parent'][0] ) ) {
	$all_categories = PPCategoryGridModule::get_categories( $args, 'default' );
} else {
	$all_categories = get_categories( $args );
}

// TODO: Selection Order.
if ( $cat_match == 1 && 'term_order' === $orderby && ! empty( $ids ) ) {
	$_all_categories = $all_categories;
	$all_categories = array();
	$ordered = array();

	foreach ( $_all_categories as $_category ) {
		$all_categories[ $_category->term_id ] = $_category;
	}

	foreach ( $ids as $id ) {
		if ( isset( $all_categories[ $id ] ) ) {
			$ordered[ $id ] = $all_categories[ $id ];
		}
	}

	$all_categories = $ordered;
}

global $post;

$current_post_terms = array();
$assigned_only = isset( $settings->on_post ) && 'assigned_only' === $settings->on_post;

if ( is_single() && $post && $post->ID ) {
	$current_post_terms = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'slugs' ) );
}

$hide_img = isset( $settings->category_show_image ) && 'no' === $settings->category_show_image;
$is_tax_archive = is_tax() || is_category() || is_tag();
$queried_object = $is_tax_archive ? get_queried_object() : false;
$exclude_current_cat = apply_filters( 'pp_category_grid_exclude_current_category', true );
?>

<div class="pp-categories-container<?php echo 'yes' === $settings->category_grid_slider ? ' swiper-container' : ''; ?>">
	<div class="pp-categories<?php echo 'yes' === $settings->category_grid_slider ? ' swiper-wrapper' : ''; ?> pp-clear">
	<?php

	foreach ( $all_categories as $cat ) {
		// filter categories which are actually assigned to current post.
		if ( $assigned_only && ! empty( $current_post_terms ) && ! in_array( $cat->slug, $current_post_terms ) ) {
			continue;
		}

		// Exclude current term.
		if ( $exclude_current_cat && $queried_object && $cat->term_id === $queried_object->term_id ) {
			continue;
		}

		if ( isset( $settings->display_data ) && 'parent_only' === $settings->display_data ) {
			if ( isset( $args['include'][0] ) && intval( $args['include'][0] ) > 0 ) {
				$inc_array = $args['include'];
				if ( ! in_array( $cat->term_id, $inc_array ) ) {
					continue;
				}
			} elseif ( 0 !== $cat->parent ) {
				continue;
			} elseif ( ! empty( $current_post_terms ) ) {
				// To display only assigned parents on single post.
				$parent = get_term_by( 'id', $cat->parent );
				if ( $parent && ! in_array( $parent->slug, $current_post_terms ) ) {
					continue;
				}
			}
		} elseif ( isset( $settings->display_data ) && 'children_only' === $settings->display_data ) {
			if ( isset( $args['include'][0] ) && intval( $args['include'][0] ) > 0 ) {
				$inc_array = $args['include'];
				if ( ! in_array( $cat->parent, $inc_array ) ) {
					continue;
				}
			} elseif ( isset( $args['exclude'][0] ) && intval( $args['exclude'][0] ) > 0 ) {
				$exc_array = $args['exclude'];
				if ( in_array( $cat->parent, $exc_array ) || 0 === $cat->parent ) {
					continue;
				}
			} elseif ( 0 === $cat->parent ) {
				continue;
			} elseif ( ! empty( $current_post_terms ) ) {
				// To display only assigned children on single post.
				$parent = get_term_by( 'id', $cat->parent );
				if ( $parent && ! in_array( $parent->slug, $current_post_terms ) ) {
					continue;
				}
			}
		} elseif ( isset( $settings->display_data ) && 'default' === $settings->display_data && isset( $args['exclude'] ) && ! empty( $args['exclude'][0] ) ) {
			$exc_array = $args['exclude'];
			if ( in_array( $cat->parent, $exc_array ) ) {
				continue;
			}
		}
		$cat_thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
		if ( 'enabled' === $taxonomy_thumbnail_enable && ! empty( $taxonomy_thumbnail_taxonomies ) && in_array( $tax_type, (array) $taxonomy_thumbnail_taxonomies ) ) {
			$taxonomy_thumbnail_id = get_term_meta( $cat->term_id, 'taxonomy_thumbnail_id', true );
			if ( empty( $cat_thumb_id ) ) {
				$cat_thumb_id = $taxonomy_thumbnail_id;
			}
		}
		$category_image = wp_get_attachment_image_src( $cat_thumb_id, $settings->category_image_size );
		$term_link      = get_term_link( $cat, $taxonomy );

		$layout_path = apply_filters( 'pp_category_grid_layout_path', $module->dir . 'includes/layout-1.php', $cat, $settings );
		if ( ! empty( $layout_path ) ) {
			include $layout_path;
		}
	}
	?>
	</div>

	<?php
	if ( 'yes' === $settings->category_grid_slider ) {
		?>
		<div class="swiper-pagination"></div>
		<?php if ( 'yes' === $settings->slider_navigation ) { ?>
			<!-- If we need navigation buttons -->
			<div class="swiper-button-prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 44"><path d="M0,22L22,0l2.1,2.1L4.2,22l19.9,19.9L22,44L0,22L0,22L0,22z"></svg></div>
			<div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 44"><path d="M27,22L27,22L5,44l-2.1-2.1L22.8,22L2.9,2.1L5,0L27,22L27,22z"></svg></div>
			<?php
		}
	}
	?>

</div>
