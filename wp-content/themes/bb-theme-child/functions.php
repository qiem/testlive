<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );


//Add color presets for Beaver Builder
function my_builder_color_presets( $colors ) {
  $colors = array();

    $colors[] = '0f172a';
    $colors[] = 'cbd5e1';
    $colors[] = 'f5f5f4';
    $colors[] = 'e7e5e4';
    $colors[] = '831843';
    $colors[] = 'f472b6';

  return $colors;
}

add_filter( 'fl_builder_color_presets', 'my_builder_color_presets' );



//Change the Customizer color palette presets
add_action('customize_controls_print_footer_scripts', function () {
  ?>
  <script>
    jQuery(document).ready(function($){
      $('.wp-picker-container').iris({
        mode: 'hsl',
        controls: {
        horiz: 'h', // square horizontal displays hue
        vert: 's', // square vertical displays saturdation
        strip: 'l' // slider displays lightness
      },
        palettes: ['#0f172a', '#cbd5e1', '#f5f5f4', '#e7e5e4', '#831843', '#f472b6']
      })
    });
  </script>
  <?php
});