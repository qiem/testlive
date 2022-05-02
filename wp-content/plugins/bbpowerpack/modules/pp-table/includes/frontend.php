<?php
$source = isset( $settings->source ) ? $settings->source : 'manual';
$tableheaders = array();
$tablerows = array();
$sortable_attrs = $module->get_sortable_attrs();
?>

<div class="pp-table-wrap">
<?php
if ( 'csv_import' == $source ) {
	if ( isset( $settings->csv_import ) && ! empty( $settings->csv_import ) ) {
		$csv_import = (array) $settings->csv_import;
		if ( isset( $csv_import['filepath'] ) ) {
			$csv_filepath 	= $csv_import['filepath'];
			if ( file_exists( $csv_filepath ) ) {
				$csv_content 	= file_get_contents( $csv_filepath );
				if ( ! empty( $csv_content ) ) {
					$csv_rows 		= explode( "\n", $csv_content );
					$tableheaders 	= str_getcsv( $csv_rows[0] );
					$tablerows 		= array();

					if ( isset( $settings->first_row_header ) && 'yes' === $settings->first_row_header ) {
						$i = 1;
					} else {
						$i = 0;
					}

					for ( ; $i < count( $csv_rows ); $i++ ) {
						$row 		= new stdClass();
						$row->cell 	= str_getcsv( $csv_rows[ $i ] );
						$tablerows[] = $row;
					}
				}
			}
		}
	}
} elseif ( 'acf_repeater' === $source ) {
	$repeater_name = isset( $settings->acf_repeater_name ) ? $settings->acf_repeater_name : '';
	$post_id = empty( $settings->acf_repeater_post_id ) ? get_the_ID() : absint( $settings->acf_repeater_post_id );
	if ( isset( $_GET['fl_builder'] ) ) {
		if ( ! class_exists( 'acf' ) ) {
			esc_html_e( 'ACF Pro plugin is not active.', 'bb-powerpack' );
			return;
		} elseif ( empty( $repeater_name ) ) {
			esc_html_e( 'Enter ACF Repeater Name.', 'bb-powerpack' );
			return;
		} elseif ( ! $post_id ) {
			esc_html_e( 'Invalid post ID.', 'bb-powerpack' );
			return;
		}
	}

	$field = get_field_object( $repeater_name, $post_id );

	// Check whether it is ACF repeater field or not.
	if ( isset( $_GET['fl_builder'] ) ) {
		if ( empty( $field ) || ! is_array( $field ) || 'repeater' !== $field['type'] ) {
			echo sprintf( __( '"%s" ACF Repeater field does not exist.', 'bb-powerpack' ), $repeater_name );
			return;
		}
	}

	$sub_fields = $field['sub_fields'];
	$repeater_rows = $field['value'];
	$image_fields = array();

	// Check if the field is empty.
	if ( ( empty( $sub_fields ) || empty( $repeater_rows ) ) && isset( $_GET['fl_builder'] ) ) {
		esc_html_e( 'ACF Repeater field is empty.', 'bb-powerpack' );
		return;
	}

	foreach ( $sub_fields as $sub_field ) {
		if ( ( 'image' === $sub_field['type'] || 'file' === $sub_field['type'] ) && isset( $sub_field['return_format'] ) ) {
			$field_name = $sub_field['name'];
			$image_fields[ $field_name ] = array(
				'type' => $sub_field['type'],
				'return_format' => $sub_field['return_format']
			);
		}
		$tableheaders[] = $sub_field['label'];
	}

	foreach ( $repeater_rows as $index => $repeater_row ) {
		$row_cell = $repeater_row;
		foreach ( $repeater_row as $key => $value ) {
			if ( isset( $image_fields[ $key ] ) ) {
				$url = 'url' === $image_fields[ $key ]['return_format'] ? $value : '';
				$url = 'array' === $image_fields[ $key ]['return_format'] ? $value['url'] : $value;

				if ( 'image' === $image_fields[ $key ]['type'] ) {
					$row_cell[ $key ] = '<img src="' . $url . '" alt="' . basename( $url ) . '" />';
				} else {
					$row_cell[ $key ] = $url;
				}
			}
		}

		$row = new stdClass();
		$row->cell = $row_cell;

		$tablerows[] = $row;
	}

} else {
	$tableheaders = $settings->header;
	$tablerows = $settings->rows;
}

$tableheaders = apply_filters( 'pp_table_headers', $tableheaders, $settings );
$tablerows = apply_filters( 'pp_table_rows', $tablerows, $settings );

if ( ! empty( $tableheaders[0] ) ) {
	do_action( 'pp_before_table_module', $settings );
?>
<table class="pp-table-<?php echo $id; ?> pp-table-content tablesaw" <?php echo $sortable_attrs; ?> data-tablesaw-minimap>
	<?php if ( 'manual' === $source || ( isset( $settings->first_row_header ) && 'yes' === $settings->first_row_header ) ) { ?>
	<thead>
		<tr>
			<?php
			$i = 1;
			foreach ( $tableheaders as $tableheader ) {
				echo '<th id="pp-table-col-' . $i++ . '" class="pp-table-col" scope="col" data-tablesaw-sortable-col>';
					echo trim( $tableheader );
				echo '</th>';
			}
			$i = 0;
			?>
		</tr>
	</thead>
	<?php } ?>
	<tbody>
		<?php
		if ( ! empty( $tablerows[0] ) ) {
			foreach ( $tablerows as $tablerow ) {
				echo '<tr class="pp-table-row">';
				foreach ( $tablerow->cell as $tablecell ) {
					echo '<td>' . trim( $tablecell ) . '</td>';
				}
				echo '</tr>';
			}
		}
		?>
	</tbody>
</table>
<?php
do_action( 'pp_after_table_module', $settings );
} // End if().
?>
</div>
