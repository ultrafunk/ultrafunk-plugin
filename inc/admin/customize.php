<?php declare(strict_types=1);
/*
 * Customize admin interface
 *
 */


namespace Ultrafunk\Plugin\Admin\Customize;


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
add_action('init', '\Ultrafunk\Plugin\Admin\Customize\cleanup_wp_header');

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
add_action('admin_menu', '\Ultrafunk\Plugin\Admin\Customize\swap_menu_items');

//
// Show number of published tracks in the Dashboard "At a Glance" widget
//
function dashboard_tracks_count(array $data) : array
{
	$count  = wp_count_posts('uf_track');
	$data[] = "<a href='/wp-admin/edit.php?post_type=uf_track'>$count->publish Tracks</a>";

	return $data;
}
add_filter('dashboard_glance_items', '\Ultrafunk\Plugin\Admin\Customize\dashboard_tracks_count');

//
// Show admin menu item for Reusable Blocks
//
function reusable_blocks_admin_menu() : void
{
  add_menu_page(
    'Reusable Blocks',
    'Reusable Blocks',
    'edit_posts',
    'edit.php?post_type=wp_block',
    '',
    'dashicons-editor-table',
    22
  );
}
add_action('admin_menu', '\Ultrafunk\Plugin\Admin\Customize\reusable_blocks_admin_menu');
