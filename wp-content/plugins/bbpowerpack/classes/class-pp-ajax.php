<?php
/**
 * Handles logic for AJAX.
 *
 * @package BB_PowerPack
 * @since 1.0.0
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PowerPack AJAX handler.
 */
class BB_PowerPack_Ajax {
	static public $cg_settings = array();
	/**
	 * Initializes actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function init() {
		add_action( 'wp', 										__CLASS__ . '::handle_ajax' );
		add_action( 'pp_post_grid_ajax_before_query', 			__CLASS__ . '::loop_fake_actions' );
		add_action( 'wp_ajax_pp_get_taxonomies', 				__CLASS__ . '::get_post_taxonomies' );
		add_action( 'wp_ajax_nopriv_pp_get_taxonomies', 		__CLASS__ . '::get_post_taxonomies' );
		add_action( 'wp_ajax_pp_get_saved_templates', 			__CLASS__ . '::get_saved_templates' );
		add_action( 'wp_ajax_nopriv_pp_get_saved_templates', 	__CLASS__ . '::get_saved_templates' );
		add_action( 'wp_ajax_pp_notice_close', 					__CLASS__ . '::close_notice' );
		add_filter( 'found_posts', 								__CLASS__ . '::found_posts', 1, 2 );
	}

	/**
	 * Hooks for fake loop.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	static public function loop_fake_actions() {
		if ( apply_filters( 'pp_post_grid_ajax_fake_loop', false ) ) {
			add_action( 'loop_start', __CLASS__ . '::fake_loop_true' );
			add_action( 'loop_end', __CLASS__ . '::fake_loop_false' );
		}
	}

	/**
	 * Fake loop.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	static public function fake_loop_true() {
		global $wp_query;
		// Fake being in the loop.
		$wp_query->in_the_loop = true;
	}

	/**
	 * Reset fake loop.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	static public function fake_loop_false() {
		global $wp_query;
		// Stop faking being in the loop.
		$wp_query->in_the_loop = false;

		remove_action( 'loop_start', __CLASS__ . '::fake_loop_true' );
		remove_action( 'loop_end', __CLASS__ . '::fake_loop_false' );
	}

	/**
	 * Execute method based on action passed.
	 *
	 * @return void
	 */
	static public function handle_ajax() {
		if ( ! isset( $_POST['pp_action'] ) || empty( $_POST['pp_action'] ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_POST['pp_action'] ) );

		if ( ! method_exists( __CLASS__, $action ) ) {
			return;
		}

		// Tell WordPress this is an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$method = $action;

		self::$method();
	}

	/**
	 * Logic to upload CSV file using Table module.
	 *
	 * @return void
	 */
	static public function table_csv_upload() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( __( 'Error uploading file.', 'bb-powerpack' ) );
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'pp_table_csv' ) ) { // input var okay.
			wp_send_json_error( __( 'Invalid request.', 'bb-powerpack' ) );
		}

		if ( ! isset( $_FILES['file'] ) ) {
			wp_send_json_error( __( 'Please provide CSV file.', 'bb-powerpack' ) );
		}

		$file = $_FILES['file'];

		// validate file type.
		if ( 'csv' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
			wp_send_json_error( __( 'Invalid file type. Please provide CSV file.', 'bb-powerpack' ) );
		}

		$upload_dir = BB_PowerPack::$upload_dir;

		$source_path = $file['tmp_name'];
		$target_path = $upload_dir['path'] . $file['name'];

		if ( file_exists( $target_path ) ) {
			unlink( $target_path );
		}

		if ( move_uploaded_file( $source_path, $target_path ) ) {
			wp_send_json_success( array(
				'filename' 		=> $file['name'],
				'filepath'		=> $target_path,
				'upload_time'	=> isset( $_POST['time'] ) ? esc_attr( $_POST['time'] ) : current_time( 'timestamp' ),
			) );
		}

		wp_send_json_error( __( 'Error uploading file.', 'bb-powerpack' ) );
	}

	/**
	 * Logic to query posts for Content Grid.
	 *
	 * @return void
	 */
	static public function get_ajax_posts() {
		$is_error = false;

		$node_id            = isset( $_POST['node_id'] ) ? wp_unslash( $_POST['node_id'] ) : false;
		$template_id        = isset( $_POST['template_id'] ) ? wp_unslash( $_POST['template_id'] ) : false;
		$template_node_id   = isset( $_POST['template_node_id'] ) ? wp_unslash( $_POST['template_node_id'] ) : false;

		if ( apply_filters( 'pp_post_grid_ajax_force_module_settings', false ) ) {
			if ( isset( $_POST['settings'] ) ) {
				unset( $_POST['settings'] );
			}
		}

		if ( ! empty( self::$cg_settings ) && isset( self::$cg_settings[ $node_id ] ) ) {
			$settings = self::$cg_settings[ $node_id ];
		} elseif ( ! isset( $_POST['settings'] ) || empty( $_POST['settings'] ) ) {
			if ( $node_id ) {
				// Get the module settings.
				if ( $template_id ) {
					$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
					$data     = FLBuilderModel::get_layout_data( 'published', $post_id );
					$module   = isset( $data[ $template_node_id ] ) ? $data[ $template_node_id ] : '';
					$settings = is_object( $module ) ? $module->settings : false;
				} else {
					// $module   = FLBuilderModel::get_module( $node_id );
					// $settings = is_object( $module ) ? $module->settings : false;
				}

				if ( ! isset( $settings ) || empty( $settings ) ) {
					$module = FLBuilderModel::get_node( $node_id );
					if ( $module && isset( $module->settings ) ) {
						$settings = $module->settings;
					}
				}
			}
		} else {
			$settings = (object) $_POST['settings'];
		}

		if ( isset( $settings ) ) {
			if ( class_exists( 'FLThemeBuilderFieldConnections' ) ) {
				$settings = FLThemeBuilderFieldConnections::connect_settings( $settings );
			}
			self::$cg_settings[ $node_id ] = $settings;
		} else {
			wp_send_json_error();
		}

		$settings = apply_filters( 'fl_builder_loop_before_query_settings', $settings );

		$module_dir = pp_get_module_dir( 'pp-content-grid' );
		$module_url = pp_get_module_url( 'pp-content-grid' );

		$response = array(
			'data'  => '',
			'pagination' => false,
		);

		$post_type = $settings->post_type;
		$offset    = isset( $settings->offset ) ? intval( $settings->offset ) : 0;

		global $post;
		global $wp_query;

		$args = array(
			'post_type'             => $post_type,
			'post_status'           => 'publish',
			'tax_query'           => array(
				'relation' => 'AND',
			),
			'ignore_sticky_posts'   => true,
			'pp_original_offset'    => $offset,
			'pp_content_grid'       => true,
			'pp_node_id'			=> $node_id,
			'pp_node_html_id'		=> isset( $settings->id ) ? $settings->id : '',
			'settings'				=> $settings,
		);

		if ( 'custom_query' === $settings->data_source ) {

			// author filter.
			if ( isset( $settings->users ) ) {

				$users = $settings->users;
				$arg = 'author__in';

				// Set to NOT IN if matching is present and set to 0.
				if ( isset( $settings->users_matching ) && ! $settings->users_matching ) {
					$arg = 'author__not_in';
				}

				if ( ! empty( $users ) ) {
					if ( is_string( $users ) ) {
						$users = explode( ',', $users );
					}

					$args[ $arg ] = $users;
				}
			}
		} // End if().

		if ( isset( $_POST['author_id'] ) && ! empty( $_POST['author_id'] ) ) {
			$args['author__in'] = array( absint( wp_unslash( $_POST['author_id'] ) ) );
		}

		if ( isset( $_POST['search_term'] ) && ! empty( $_POST['search_term'] ) ) {
			$args['s'] = wp_unslash( $_POST['search_term'] );
		}

		if ( 'no' !== $settings->post_grid_filters_display && 'none' !== $settings->post_grid_filters && isset( $_POST['term'] ) && ! isset( $_POST['is_tax'] ) ) {
			$args['tax_query'] = array(
				'relation'	=> 'AND',
				array(
					'taxonomy' => $settings->post_grid_filters,
					'field'    => 'slug',
					'terms'    => sanitize_text_field( wp_unslash( $_POST['term'] ) ),
				),
			);
		} else {
			if ( 'custom_query' !== $settings->data_source && isset( $_POST['taxonomy'] ) && isset( $_POST['term'] ) ) {
				$args['tax_query'] = array(
					'relation'	=> 'AND',
					array(
						'taxonomy' => sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ),
						'field'    => 'slug',
						'terms'    => sanitize_text_field( wp_unslash( $_POST['term'] ) ),
					),
				);
			}
		}

		if ( 'custom_query' === $settings->data_source ) {

			$taxonomies = FLBuilderLoop::taxonomies( $post_type );

			foreach ( $taxonomies as $tax_slug => $tax ) {

				$tax_value = '';
				$term_ids  = array();
				$operator  = 'IN';

				// Get the value of the suggest field.
				if ( isset( $settings->{'tax_' . $post_type . '_' . $tax_slug} ) ) {
					// New style slug.
					$tax_value = $settings->{'tax_' . $post_type . '_' . $tax_slug};
				} elseif ( isset( $settings->{'tax_' . $tax_slug} ) ) {
					// Old style slug for backwards compat.
					$tax_value = $settings->{'tax_' . $tax_slug};
				}

				// Get the term IDs array.
				if ( ! empty( $tax_value ) ) {
					$term_ids = explode( ',', $tax_value );
				}

				// Handle matching settings.
				if ( isset( $settings->{'tax_' . $post_type . '_' . $tax_slug . '_matching'} ) ) {

					$tax_matching = $settings->{'tax_' . $post_type . '_' . $tax_slug . '_matching'};

					if ( ! $tax_matching ) {
						// Do not match these terms.
						$operator = 'NOT IN';
					} elseif ( 'related' === $tax_matching ) {
						// Match posts by related terms from the global post.
						global $post;
						$terms 	 = wp_get_post_terms( $post->ID, $tax_slug );
						$related = array();

						foreach ( $terms as $term ) {
							if ( ! in_array( $term->term_id, $term_ids ) ) {
								$related[] = $term->term_id;
							}
						}

						if ( empty( $related ) ) {
							// If no related terms, match all except those in the suggest field.
							$operator = 'NOT IN';
						} else {

							// Don't include posts with terms selected in the suggest field.
							$args['tax_query'][] = array(
								'taxonomy'	=> $tax_slug,
								'field'		=> 'id',
								'terms'		=> $term_ids,
								'operator'  => 'NOT IN',
							);

							// Set the term IDs to the related terms.
							$term_ids = $related;
						}
					}
				} // End if().

				if ( ! empty( $term_ids ) ) {

					$args['tax_query'][] = array(
						'taxonomy'	=> $tax_slug,
						'field'		=> 'id',
						'terms'		=> $term_ids,
						'operator'  => $operator,
					);
				}
			} // End foreach().
		}
		
		if ( 'main_query' !== $settings->data_source ) {
			if ( isset( $settings->posts_per_page ) ) {
				$args['posts_per_page'] = $settings->posts_per_page;
			}

			// posts filter.
			if ( isset( $settings->{'posts_' . $post_type} ) ) {

				$ids = $settings->{'posts_' . $post_type};
				$arg = 'post__in';

				if ( isset( $settings->{'posts_' . $post_type . '_matching'} ) ) {
					if ( ! $settings->{'posts_' . $post_type . '_matching'} ) {
						$arg = 'post__not_in';
					}
				}

				if ( ! empty( $ids ) ) {
					$args[ $arg ] = explode( ',', $ids );
				}
			}
			
			if ( $post && isset( $settings->exclude_current_post ) && 'yes' === $settings->exclude_current_post ) {
				$args['post__not_in'][] = $post->ID;
			}
		}

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && 'product' === $post_type ) {
			$args['meta_query'][] = array(
				'key'       => '_stock_status',
				'value'     => 'instock',
				'compare'   => '=',
			);
		}

		if ( isset( $_POST['paged'] ) ) {
			$args['paged'] = absint( wp_unslash( $_POST['paged'] ) );
		}

		if ( 'main_query' !== $settings->data_source && 'acf_relationship' !== $settings->data_source ) {
			// Offset.
			if ( isset( $settings->offset ) ) {
				$page = isset( $args['paged'] ) ? $args['paged'] : 1;
				$per_page = ( isset( $args['posts_per_page'] ) && $args['posts_per_page'] > 0 ) ? $args['posts_per_page'] : 10;
				if ( $page < 2 ) {
					$args['offset'] = absint( $settings->offset );
				} else {
					$args['offset'] = absint( $settings->offset ) + ( ( $page - 1 ) * $per_page );
				}
			}

			// Order by author.
			if ( 'author' === $settings->order_by ) {
				$args['orderby'] = array(
					'author' => $settings->order,
					'date' => $settings->order,
				);
			} else {
				$args['orderby'] = $settings->order_by;

				// Order by meta value arg.
				if ( strstr( $settings->order_by, 'meta_value' ) ) {
					$args['meta_key'] = $settings->order_by_meta_key;
				}

				if ( isset( $_POST['orderby'] ) ) {
					$orderby = esc_attr( wp_unslash( $_POST['orderby'] ) );

					$args = self::get_conditional_args( $orderby, $args );
				}

				if ( isset( $settings->order ) ) {
					$args['order'] = $settings->order;
				}
			}
		} // End if().

		if ( 'tribe_events' === $post_type ) {
			$compare = '>=';
			if ( isset( $settings->event_orderby ) && '' !== $settings->event_orderby ) {
				$orderby = $settings->event_orderby;
			} else {
				$orderby = 'EventStartDate';
			}
			if ( isset( $settings->event_order ) && '' !== $settings->event_order ) {
				$order = $settings->event_order;
			} else {
				$order = 'ASC';
			}
			if ( isset( $settings->show_events ) && ! empty( $settings->show_events ) ) {
				switch ( $settings->show_events ) {
					case 'past':
						$compare = '<';
						break;
					case 'today':
						$compare = '=';
						break;
				}
			}
			$args['meta_key'] 		= '_' . $orderby;
			$args['orderby'] 		= 'meta_value';
			$args['order'] 			= $order;
			$args['eventDisplay'] 	= 'custom';
			if ( 'all' !== $settings->show_events ) {
				$today              = gmdate( 'Y-m-d' ) . ' 00:00:00';
				$args['meta_query'] = array(
					array(
						'key'     => '_EventStartDate',
						'compare' => $compare,
						'value'   => $today,
					),
				);
			}
		}

		$args = apply_filters( 'pp_post_grid_ajax_query_args', $args );

		do_action( 'pp_post_grid_ajax_before_query', $settings );

		/**
		 * Custom Content Workaround for Pods fields.
		 *
		 * Before query is performed.
		 * @see fl_builder_loop_before_query
		 * 
		 * @since 2.14.0
		 */
		do_action( 'fl_builder_loop_before_query', $settings );

		if ( isset( $args['settings'] ) ) {
			unset( $args['settings'] );
		}

		if ( 'main_query' !== $settings->data_source ) {
			$query = new WP_Query( $args );
		} else {
			$query = $wp_query;
			if ( method_exists( 'WC_Query', 'pre_get_posts' ) ) {
				WC()->query->pre_get_posts( $query );
			}

			$tax_query = $query->get( 'tax_query' );

			if ( ! is_array( $tax_query ) ) {
				$tax_query = array();
			}
			
			if ( isset( $args['tax_query'] ) ) {
				$query->set( 'tax_query', array_merge( $tax_query, $args['tax_query'] ) );
			}
	
			if ( isset( $_POST['paged'] ) ) {
				$query->set('paged', absint( wp_unslash( $_POST['paged'] ) ) );
			}

			if ( isset( $_POST['author_id'] ) && ! empty( $_POST['author_id'] ) ) {
				$query->set( 'author__in', array( absint( wp_unslash( $_POST['author_id'] ) ) ) );
			}

			if ( isset( $_POST['search_term'] ) && ! empty( $_POST['search_term'] ) ) {
				$query->is_search = true;
				$query->set( 's', wp_unslash( $_POST['search_term'] ) );
				$query->set( 'p', 0 );
				$query->set( 'page_id', 0 );
			}

			$query = new WP_Query( $query->query_vars );
		}

		// Add compatibility for Relevanssi.
		if ( $query->is_search() && function_exists( 'relevanssi_do_query' ) ) {
			relevanssi_do_query( $query );
		}

		do_action( 'pp_post_grid_ajax_after_query', $settings, $query );

		if ( $query->have_posts() ) :

			// create pagination.
			if ( $query->max_num_pages > 1 && 'none' !== $settings->pagination ) {
				$style = ( 'scroll' === $settings->pagination || 'load_more' === $settings->pagination ) ? ' style="display: none;"' : '';
				ob_start();

				echo '<div class="pp-content-grid-pagination pp-ajax-pagination fl-builder-pagination"' . $style . '>';
				if ( ('scroll' === $settings->pagination || 'load_more' === $settings->pagination ) && isset( $_POST['term'] ) ) {
					BB_PowerPack_Post_Helper::ajax_pagination(
						$query,
						$settings,
						esc_attr( wp_unslash( $_POST['current_page'] ) ),
						esc_attr( wp_unslash( $_POST['paged'] ) ),
						sanitize_text_field( wp_unslash( $_POST['term'] ) ),
						esc_attr( wp_unslash( $_POST['node_id'] ) )
					);
				} else {
					BB_PowerPack_Post_Helper::ajax_pagination(
						$query,
						$settings,
						esc_attr( wp_unslash( $_POST['current_page'] ) ),
						esc_attr( wp_unslash( $_POST['paged'] ) )
					);
				}
				echo '</div>';
				if ( 'load_more' == $settings->pagination ) { ?>
					<div class="pp-content-grid-load-more">
						<a href="#" class="pp-grid-load-more-button">
						<span class="pp-grid-loader-text"><?php echo $settings->load_more_text; ?></span>
						<span class="pp-grid-loader-icon"><img src="<?php echo BB_POWERPACK_URL . 'assets/images/spinner.gif'; ?>" alt="loader" /></span></a>
					</div>
				<?php } ?>
				<?php if ( 'scroll' == $settings->pagination ) { ?>
					<div class="pp-content-grid-loader" style="display: none;">
						<span class="pp-grid-loader-text"><?php _e('Loading...', 'bb-powerpack'); ?></span>
						<span class="pp-grid-loader-icon"><img src="<?php echo BB_POWERPACK_URL . 'assets/images/spinner.gif'; ?>" alt="loader" /></span>
					</div>
				<?php }

				$response['pagination'] = ob_get_clean();
			}
			if ( $query->max_num_pages < 1 ) {
				$response['last'] = true;
			}

			$count = 0;

			// posts query.
			while ( $query->have_posts() ) {

				$query->the_post();

				$title_attrs = the_title_attribute( array( 'echo' => false ) );
				$terms_list = wp_get_post_terms( get_the_ID(), $settings->post_taxonomies );
				$post_id = get_the_ID();
				$permalink = get_permalink();
				$post_type = get_post_type( get_post() );
				$settings->post_type = $post_type;

				$count++;

				ob_start();

				if ( 'custom' === $settings->post_grid_style_select ) {
					include BB_POWERPACK_DIR . 'includes/post-module-layout.php';
				} else {
					include apply_filters( 'pp_cg_module_layout_path', $module_dir . 'includes/post-grid.php', $settings->layout, $settings );
				}

				$response['data'] .= do_shortcode( wp_unslash( ob_get_clean() ) );
			}

			wp_reset_postdata();

		else :
			$no_posts_found = apply_filters( 'pp_post_grid_ajax_not_found_text', esc_html__( 'No posts found.', 'bb-powerpack' ), $settings, $query );
			$response['data'] = '<div class="pp-content-post pp-posts-not-found-text">' . $no_posts_found . '</div>';
		endif;

		wp_reset_query();

		$response = apply_filters( 'pp_post_grid_ajax_response', $response, $settings, $query );

		wp_send_json( $response );
	}

	static public function found_posts( $found_posts, $query ) {
		if ( isset( $query->query ) && isset( $query->query['pp_content_grid'] ) ) {
			return (int) $found_posts - (int) $query->query['pp_original_offset'];
		}

		return $found_posts;
	}

	/**
	 * Get conditional arguments for meta data.
	 *
	 * @param string $type	Type of the meta key.
	 * @param array  $args	WP query args.
	 * @return array
	 */
	static public function get_conditional_args( $type, $args ) {
		switch ( $type ) :
			case 'date':
				$args['orderby'] = 'date ID';
				$args['order'] = 'DESC';
				break;

			case 'price':
				$args['meta_key'] = '_price';
				$args['order'] = 'ASC';
				$args['orderby'] = 'meta_value_num';
				break;

			case 'price-desc':
				$args['meta_key'] = '_price';
				$args['order'] = 'DESC';
				$args['orderby'] = 'meta_value_num';
				break;

			default:
				break;

		endswitch;

		return $args;
	}

	/**
	 * Get taxonomies of a post type.
	 *
	 * @param string $post_type Post type.
	 */
	static public function get_post_taxonomies( $post_type = 'post' ) {
		if ( isset( $_POST['post_type'] ) && ! empty( $_POST['post_type'] ) ) {
			$post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
		}

		if ( 'all' === $post_type ) {
			$post_types = FLBuilderLoop::post_types();
			$taxonomies = array();
			foreach ( $post_types as $type => $obj ) {
				$type_taxonomies = FLBuilderLoop::taxonomies( $type );
				if ( ! empty( $type_taxonomies ) ) {
					foreach ( $type_taxonomies as $key => $tax ) {
						if ( ! array_key_exists( $key, $taxonomies ) ) {
							$taxonomies[ $key ] = $tax;
						}
					}
				}
			}
		} else {
			$taxonomies = FLBuilderLoop::taxonomies( $post_type );
		}

		$html = '';

		foreach ( $taxonomies as $tax_slug => $tax ) {
			$html .= '<option value="' . $tax_slug . '">' . $tax->label . ' (' . $tax->name . ')' . '</option>';
		}

		echo $html;
		die;
	}

	/**
	 * Get saved templates.
	 *
	 * @since 1.4
	 */
	static public function get_saved_templates() {
		$response = array(
			'success' => false,
			'data'	=> array(),
		);

		$args = array(
			'post_type' 		=> 'fl-builder-template',
			'orderby' 			=> 'title',
			'order' 			=> 'ASC',
			'posts_per_page' 	=> '-1',
		);

		if ( isset( $_POST['type'] ) && ! empty( $_POST['type'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'		=> 'fl-builder-template-type',
					'field'			=> 'slug',
					'terms'			=> sanitize_text_field( wp_unslash( $_POST['type'] ) ),
				),
			);
		}

		$posts = get_posts( $args );

		$options = '';

		if ( count( $posts ) ) {
			foreach ( $posts as $post ) {
				$options .= '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
			}

			$response = array(
				'success' => true,
				'data' => $options,
			);
		} else {
			$response = array(
				'success' => true,
				'data' => '<option value="" disabled>' . __( 'No templates found!', 'bb-powerpack' ) . '</option>',
			);
		}

		echo json_encode( $response );
		die;
	}

	static public function close_notice() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'pp_notice' ) ) {
			wp_send_json_error( esc_html__( 'Action failed. Please refresh the page and retry.', 'bb-powerpack' ) );
		}
		if ( ! isset( $_POST['notice'] ) || empty( $_POST['notice'] ) ) {
			wp_send_json_error( esc_html__( 'Action failed. Please refresh the page and retry.', 'bb-powerpack' ) );
		}

		try {
			update_user_meta( get_current_user_id(), 'bb_powerpack_dismissed_latest_update_notice', true );
			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}
}

BB_PowerPack_Ajax::init();
