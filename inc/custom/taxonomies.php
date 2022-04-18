<?php declare(strict_types=1);
/*
* Ultrafunk custom taxonomies for tracks: Artists + Channels
*/


namespace Ultrafunk\Plugin\Taxonomies;


/**************************************************************************************************************************/


//
// Register Ultrafunk custom taxonomies
//
function register_custom() : void
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
    'query_var'         => true,
    'rewrite'           => ['slug' => 'artist'],
  ];

  register_taxonomy('uf_artist', ['uf_track'], $args);

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
    'query_var'         => true,
    'rewrite'           => ['slug' => 'channel'],
  ];

  register_taxonomy('uf_channel', ['uf_track'], $args);
}
add_action('init', 'Ultrafunk\Plugin\Taxonomies\register_custom');
