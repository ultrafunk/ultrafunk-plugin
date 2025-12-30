<?php declare(strict_types=1);
/*
 * Misc admin filters and actions
 *
 */


namespace Ultrafunk\Plugin\Admin\FiltersActions;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


use function Ultrafunk\Plugin\Shared\Utils\get_channels_top_artists_info;


/**************************************************************************************************************************/


//
// Remove default WordPress header stuff that is not needed...
//
function cleanup_wp_header() : void
{
  // Remove WP-Emoji for admins
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', '\Ultrafunk\Plugin\Admin\FiltersActions\cleanup_wp_header');

//
// Swap admin menu item positions for Posts and Tracks = Tracks first
//
function swap_menu_items() : void
{
  global $menu;

  if ($menu[5][5] === 'menu-posts')
  {
    $posts_menu  = $menu[5];
    $tracks_menu = $menu[6];
    $menu[5]     = $tracks_menu;
    $menu[6]     = $posts_menu;
  }
}
add_action('admin_menu', '\Ultrafunk\Plugin\Admin\FiltersActions\swap_menu_items');

//
// Show number of published tracks in the Dashboard "At a Glance" widget
//
function dashboard_tracks_count(array $data) : array
{
  return [ "<a href='/wp-admin/edit.php?post_type=uf_track'>" . get_channels_top_artists_info()['all_tracks_count'] . " Tracks</a>" ];
}
add_filter('dashboard_glance_items', '\Ultrafunk\Plugin\Admin\FiltersActions\dashboard_tracks_count');

//
// Ultrafunk plugin admin styles
//
function enqueue_admin_styles()
{
  wp_enqueue_style('admin-settings-style', plugins_url() . '/ultrafunk/inc/admin/settings.css', [], \Ultrafunk\Plugin\Config\VERSION);
}
add_action('admin_enqueue_scripts', '\Ultrafunk\Plugin\Admin\FiltersActions\enqueue_admin_styles');

//
// Use WP Settings / General => Date Format & Time Format for Posts, Pages etc. Date column formatting
//
function post_date_column_time(string $t_time, object $post) : string
{
  $date_time_options = get_options(['date_format', 'time_format']);
  $date_string = get_the_time($date_time_options['date_format'], $post);
  $time_string = get_the_time($date_time_options['time_format'], $post);

  return $date_string . ' at ' . $time_string;
}
add_filter('post_date_column_time', '\Ultrafunk\Plugin\Admin\FiltersActions\post_date_column_time', 10, 2);
