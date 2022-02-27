<?php declare(strict_types=1);
/*
* Ultrafunk custom track post type
*/


namespace Ultrafunk\Plugin\PostTypes;


/**************************************************************************************************************************/


//
// Register Ultrafunk custom post types
//
function register_custom() : void
{
  //
  // Register Tracks custom post type
  //
  $labels = [
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
add_action('init', '\Ultrafunk\Plugin\PostTypes\register_custom');

