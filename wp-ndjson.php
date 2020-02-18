<?php
/**
* Plugin Name: WP NDJSON
* Plugin URI: https://github.com/emaildano/wp-ndjson
* Description: Export WordPress posts to NDJSON.
* Version: v1.0.0
* Author: Daniel Olson
* Author URI: https://github.com/emaildano
* License: GPL2
* Text Domain: wp-ndjson
*/


/**
 * On Plugin Activation
 */

function wp_ndjson_install() {
  create_dir();
}

register_activation_hook( __FILE__, 'wp_ndjson_install' );

/**
 * On Plugin Deactivation
 */

function wp_ndjson_deactivate() {
  remove_dir();
}

register_deactivation_hook( __FILE__, 'wp_ndjson_deactivate' );

/**
 * On Plugin Uninstall
 */

function wp_ndjson_uninstall() {
  remove_dir();
}

register_uninstall_hook( __FILE__, 'wp_ndjson_uninstall' );


/**
 * Retreives file and folder information
 */

function wp_get_ndjson() {
  
  $upload_dir = wp_get_upload_dir();
  $file_name = 'index.json';
  $folder_name = '/wp-ndjson/';
  $tmpfile_name = 'index.tmp';
  $basedir = $upload_dir['basedir'] . $folder_name;
  $baseurl = $upload_dir['baseurl'] . $folder_name;
  $file = $basedir . $file_name; // e.g.: /var/www/vhosts/example.com/wp-content/uploads/wp-ndjson/index.json
  $url = $baseurl . $file_name; // e.g.: https://example.com/wp-content/uploads/wp-ndjson/index.json
  $tmpfile = $basedir . $tmpfile_name; //e.g.: https://example.com/wp-content/uploads/wp-ndjson/index.tmp
  
  return [
    'file'    => $file,
    'basedir' => $basedir,
    'tmpfile' => $tmpfile,
    'url'     => $url,
  ];
}

/**
 * Create directory
 */

function create_dir() {
  
  $ndjson = wp_get_ndjson();
  $dirname = dirname($ndjson['basedir'] . '.');

  if (!is_dir($dirname)) {
    mkdir($dirname, 0755, true);
  }
}

/**
 * Remove directory
 */

function remove_dir() {
  $ndjson = wp_get_ndjson();
  $dirname = dirname($ndjson['basedir'] . '.');
  rmdir($dirname);
}

/**
 * Create index
 */

add_action( 'admin_post_create_index', 'create_dir' );
add_action( 'admin_post_create_index', 'create_index' );
function create_index() {

  $args = [
    'post_type' => 'any',
    'post_status' => 'publish',
    'posts_per_page' => 10
  ];

  $query = new WP_Query( $args );
  $posts = [];
  $ndjson = wp_get_ndjson();
  $f = fopen( $ndjson['file'] , "w" );

  while( $query->have_posts() ) : $query->the_post();

    $data = [
      'id' => get_the_id(),
      'title' => get_the_title()
    ];

    $content = json_encode($data) . PHP_EOL;

    fwrite($f, $content);

  endwhile;

  wp_reset_query();

  fclose($f);

}

/**
 * Update index
 */

add_action('save_post', 'update_index');
function update_index($id) {

    // Check autosave
     if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    // Check permissions & remove action
    if ( !current_user_can('edit_post', $id) ) return;
    remove_action('save_post', 'update_index');

    // Restore action
    add_action('save_post', 'update_index');

    $replaced = false;
    $ndjson = wp_get_ndjson();
    $reading = fopen($ndjson['file'], 'r');
    $writing = fopen($ndjson['tmpfile'], 'w');

    while (!feof($reading)) {
      $line = fgets($reading);
      if (stristr( $line, '{"id":'.get_the_id($id) )) {
        $line = '{"id":'.get_the_id($id).',"title":"'.get_the_title($id).'"}' . PHP_EOL;
        $replaced = true;
      }
      fputs($writing, $line);
    }

    fclose($reading);
    fclose($writing);
    
    // Check or skip for changes.
    if ($replaced) {
      rename($ndjson['tmpfile'], $ndjson['file']);
    } else {
      unlink($ndjson['tmpfile']);
    }
}

/**
 * Admin Settings Menu
 */

add_action( 'admin_menu', 'wp_ndjson' );
function wp_ndjson() {
  add_options_page(
    'WP NDJSON',
    'WP NDJSON',
    'manage_options',
    'wp-ndjson',
    'wp_ndjson_options'
  );
}

require_once('lib/includes.php');