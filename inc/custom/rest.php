<?php declare(strict_types=1);
/*
 * Ultrafunk custom REST functions, actions and filters
 *
 */


namespace Ultrafunk\Plugin\Custom\Rest;


use WP_REST_Request;
use WP_Term_Query;

use function Ultrafunk\Plugin\Shared\get_term_links;


/**************************************************************************************************************************/


//
// Register meta fields for REST API fetch
//
function register_custom_post_metas() : void
{
  register_post_meta('uf_track', 'track_artist',
    [
      'type'         => 'string',
      'description'  => 'track_artist',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_artist_id',
    [
      'type'         => 'number',
      'description'  => 'track_artist_id',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_source_type',
    [
      'type'         => 'number',
      'description'  => 'track_source_type',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_source_data',
    [
      'type'         => 'string',
      'description'  => 'track_source_data',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_title',
    [
      'type'         => 'string',
      'description'  => 'track_title',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_duration',
    [
      'type'         => 'number',
      'description'  => 'track_duration',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );
}
add_action('rest_api_init', '\Ultrafunk\Plugin\Custom\Rest\register_custom_post_metas');


/**************************************************************************************************************************/


//
// uf_track REST API response: Return full artists and channels links to tracks (not just IDs as per REST API defaults)
//
function register_custom_rest_fields() : void
{
  register_rest_field('uf_track', 'artists_links', [
    'get_callback' => function($post, $attr, $request) { return get_artists_channels($post, $request, 'uf_artist'); },
    'schema'       => null,
  ]);

  register_rest_field('uf_track', 'channels_links', [
    'get_callback' => function($post, $attr, $request) { return get_artists_channels($post, $request, 'uf_channel'); },
    'schema'       => null,
  ]);
}
add_action('rest_api_init', '\Ultrafunk\Plugin\Custom\Rest\register_custom_rest_fields');

function get_artists_channels($post, $request, $taxonomy)
{
  $links_path = isset($request['links_path']) ? ('/' . sanitize_title($request['links_path'])) : '';
  $separator  = isset($request['separator'])  ? ', ' : '';
  $term_data  = get_object_term_cache($post['id'], $taxonomy);

  // If data is not cached, get from DB
  if ($term_data === false)
    $term_data = wp_get_object_terms($post['id'], $taxonomy);

  if ($taxonomy === 'uf_artist')
    return get_term_links($term_data, "$links_path/artist/", $separator, (int)get_post_meta($post['id'], 'track_artist_id', true));
  else
    return get_term_links($term_data, "$links_path/channel/", $separator);
}

//
// Modify uf_track REST request to return random shuffle query data
//
function rest_uf_track_query(array $args, object $request) : array
{
  if ($request->get_param('shuffle') === 'true')
  {
    $shuffle_path = 'all';
    $shuffle_type = $request->get_param('shuffle_type');
    $shuffle_slug = $request->get_param('shuffle_slug');
    $transient    = get_transient(\Ultrafunk\Plugin\Storage\get_shuffle_transient_name());

    if ($shuffle_slug !== null)
      $shuffle_path = $shuffle_type . '/' . $shuffle_slug;

    if (($transient !== false) && ($shuffle_path === $transient['shuffle_path']))
    {
      $args['orderby']  = 'post__in';
      $args['post__in'] = $transient['post_ids'];
    }
  }

  return $args;
}
add_filter('rest_uf_track_query', '\Ultrafunk\Plugin\Custom\Rest\rest_uf_track_query', 10, 2);


/**************************************************************************************************************************/


//
// REST Get Channel Top Artists data
//
function get_top_artists(WP_REST_Request $request) : ?array
{
  $top_artists = get_transient('uf_channels_top_artists');
  $channel_id  = isset($request['channel_id']) ? intval($request['channel_id']) : -1;

  if (($top_artists !== false) && isset($top_artists[$channel_id]))
  {
    $channel_artists = $top_artists[$channel_id];
    $request_result  = [];

    $query_result = new WP_Term_Query([
      'taxonomy'   => 'uf_artist',
      'include'    => array_keys($channel_artists),
      'orderby'    => 'include',
      'hide_empty' => true,
    ]);

    foreach($query_result->terms as $term)
    {
      $request_result[] = [
        'artist_name' => $term->name,
        'artist_slug' => $term->slug,
        'track_count' => $channel_artists[$term->term_id],
      ];
    }

    return $request_result;
  }

  return null;
}

//
// Adding custom REST endpoint for Top Artists in All Channels view
//
function register_custom_rest_routes() : void
{
  register_rest_route('ultrafunk/v1', '/top-artists', [
    'methods'             => 'GET',
    'callback'            => '\Ultrafunk\Plugin\Custom\Rest\get_top_artists',
    'permission_callback' => '__return_true',
  ]);
}
add_action('rest_api_init', '\Ultrafunk\Plugin\Custom\Rest\register_custom_rest_routes');
