<?php declare(strict_types=1);
/*
 * Ultrafunk custom post and taxonomy types
 *
 */


namespace Ultrafunk\Plugin\Shared\CustomTypes;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


//
// Register custom post types
//
function register_custom_post_types() : void
{
  //
  // Register Tracks custom post type
  //
  $labels = [
    'add_new'                  => 'Add New Track',
    'add_new_item'             => 'Add New Track',
    'all_items'                => 'All Tracks',
    'archives'                 => 'Track Archives',
    'attributes'               => 'Track Attributes',
    'edit_item'                => 'Edit Track',
    'filter_items_list'        => 'Filter Tracks list',
    'insert_into_item'         => 'Insert into Track',
    'item_published'           => 'Track published.',
    'item_published_privately' => 'Track published privately.',
    'item_reverted_to_draft'   => 'Track reverted to draft.',
    'item_scheduled'           => 'Track scheduled.',
    'item_updated'             => 'Track updated.',
    'items_list'               => 'Tracks list',
    'items_list_navigation'    => 'Tracks list navigation',
    'menu_name'                => 'Tracks',
    'name'                     => 'Tracks',
    'name_admin_bar'           => 'Track',
    'new_item'                 => 'New Track',
    'not_found'                => 'No Tracks found.',
    'not_found_in_trash'       => 'No Tracks found in Trash.',
    'search_items'             => 'Search Tracks',
    'singular_name'            => 'Track',
    'uploaded_to_this_item'    => 'Uploaded to this Track',
    'view_item'                => 'View Track',
    'view_items'               => 'View Tracks',
  ];

  $args = [
    'description'   => 'Track custom post type.',
    'labels'        => $labels,
    'public'        => true,
    'menu_icon'     => 'dashicons-album',
    'menu_position' => 5,
    'supports'      => ['title', 'editor', 'author', 'revisions', 'custom-fields', 'thumbnail'],
    'taxonomies'    => ['uf_channel', 'uf_artist'],
    'show_in_rest'  => true,
    'rest_base'     => 'tracks',
    'rewrite'       => ['slug' => 'track'],
  ];

  register_post_type('uf_track', $args);
}
add_action('init', '\Ultrafunk\Plugin\Shared\CustomTypes\register_custom_post_types');


/**************************************************************************************************************************/


//
// Register custom taxonomies for tracks: Artists + Channels
//
function register_custom_taxonomies() : void
{
  //
  // Register Artists custom taxonomy
  //
  $labels = [
    'name'          => 'Artists',
    'singular_name' => 'Artist',
    'search_items'  => 'Search Artists',
    'all_items'     => 'All Artists',
    'view_item'     => 'View Artist',
    'edit_item'     => 'Edit Artist',
    'update_item'   => 'Update Artist',
    'add_new_item'  => 'Add New Artist',
    'new_item_name' => 'New Artist Name',
    'not_found'     => 'No Artists Found',
    'back_to_items' => 'Back to Artists',
    'menu_name'     => 'Artists',
  ];

  $args = [
    'labels'            => $labels,
    'hierarchical'      => false,
    'public'            => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rest_base'         => 'artists',
    'query_var'         => 'artist',
    'rewrite'           => ['slug' => 'artist'],
  ];

  register_taxonomy('uf_artist', 'uf_track', $args);

  //
  // Register Channels custom taxonomy
  //
  $labels = [
    'name'              => 'Channels',
    'singular_name'     => 'Channel',
    'search_items'      => 'Search Channels',
    'all_items'         => 'All Channels',
    'view_item'         => 'View Channel',
    'parent_item'       => 'Parent Channel',
    'parent_item_colon' => 'Parent Channel:',
    'edit_item'         => 'Edit Channel',
    'update_item'       => 'Update Channel',
    'add_new_item'      => 'Add New Channel',
    'new_item_name'     => 'New Channel Name',
    'not_found'         => 'No Channels Found',
    'back_to_items'     => 'Back to Channels',
    'menu_name'         => 'Channels',
  ];

  $args = [
    'labels'            => $labels,
    'hierarchical'      => true,
    'public'            => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rest_base'         => 'channels',
    'query_var'         => 'channel',
    'rewrite'           => ['slug' => 'channel'],
  ];

  register_taxonomy('uf_channel', 'uf_track', $args);
}
add_action('init', '\Ultrafunk\Plugin\Shared\CustomTypes\register_custom_taxonomies');
