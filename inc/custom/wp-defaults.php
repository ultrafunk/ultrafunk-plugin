<?php declare(strict_types=1);
/*
 * Cleanup misc WP frontend stuff
 *
 */


namespace Ultrafunk\Plugin\Custom\WPDefaults;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


//
// Remove ALL inlined Gutenberg block library CSS
//
function after_setup_theme() : void
{
  add_filter('should_load_separate_core_block_assets', '__return_false');
}
add_action('after_setup_theme', '\Ultrafunk\Plugin\Custom\WPDefaults\after_setup_theme');

//
// Remove default WordPress header stuff that is not needed...
//
function cleanup_header() : void
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

  // Remove Gutenberg 'wp-block-library-css' CSS file
  remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');

  // Remove RSD link
  remove_action('wp_head', 'rsd_link');

  // Remove Shortlink
  remove_action('wp_head', 'wp_shortlink_wp_head');

  // Remove header feed / RSS links
  remove_action('wp_head', 'feed_links', 2);
  remove_action('wp_head', 'feed_links_extra', 3);
}
add_action('init', '\Ultrafunk\Plugin\Custom\WPDefaults\cleanup_header');

//
// Remove unused CSS that is by default included or inlined by WordPress
//
function remove_unused_styles()
{
  wp_dequeue_style('classic-theme-styles');
  wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', '\Ultrafunk\Plugin\Custom\WPDefaults\remove_unused_styles');

//
// Disable since WP 6.7: https://core.trac.wordpress.org/ticket/62413
//
add_filter('wp_img_tag_add_auto_sizes', '__return_false');
