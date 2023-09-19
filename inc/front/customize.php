<?php declare(strict_types=1);
/*
 * Customize frontend stuff
 *
 */


namespace Ultrafunk\Plugin\Front\Customize;


/**************************************************************************************************************************/


//
// Remove default WordPress header stuff that is not needed...
//
function cleanup_wp_header() : void
{
  // Remove WP generator meta tag
  remove_action('wp_head', 'wp_generator');

  // Remove WP-Emoji for visitors
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('wp_print_styles', 'print_emoji_styles');

  // Remove support for embedding WordPress content
  remove_action('wp_head', 'wp_oembed_add_host_js');
  // Remove oEmbed discovery links.
  remove_action('wp_head', 'wp_oembed_add_discovery_links');

  // Remove page / post REST endpoint header links
  remove_action('wp_head', 'rest_output_link_wp_head');

  // Remove wlwmanifest.xml (needed to support windows live writer)
  remove_action('wp_head', 'wlwmanifest_link');

  // Remove Gutenberg CSS for visitors
  remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');

  // Remove RSD link
  remove_action('wp_head', 'rsd_link');

  // Remove Shortlink
  remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', '\Ultrafunk\Plugin\Front\Customize\cleanup_wp_header');

//
// Remove Gutenberg Block Library stuff from header (CSS) + footer (SVGs)
// https://github.com/WordPress/gutenberg/issues/38299
//
function remove_wp_block_library() : void
{
  remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
  remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
}
add_action('after_setup_theme', '\Ultrafunk\Plugin\Front\Customize\remove_wp_block_library', 10, 0);

//
// Remove /wp-includes/css/classic-themes.min.css that is added by default in WP 6.1
//
function remove_classic_theme_styles()
{
  wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', '\Ultrafunk\Plugin\Front\Customize\remove_classic_theme_styles');

//
// Show Tracks in site RSS feed
//
function add_tracks_to_feed(array $query_vars) : array
{
  if (isset($query_vars['feed']))
    $query_vars['post_type'] = ['post', 'uf_track'];

  return $query_vars;
}
add_filter('request', '\Ultrafunk\Plugin\Front\Customize\add_tracks_to_feed');
