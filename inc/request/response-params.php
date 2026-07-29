<?php declare(strict_types=1);
/*
 * Set / Get response parameters for this request for client side use (JavaScript)
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


use SimpleXMLElement;

use Ultrafunk\Plugin\Shared\Constants\PLAYER_TYPE;

use const Ultrafunk\Plugin\Config\PLUGIN_ENV;

use function Ultrafunk\Plugin\Singleton\ {
  get_prop,
  get_settings_value,
  is_list_player,
  is_shuffle,
  is_termlist,
  get_request_params,
};


/**************************************************************************************************************************/


function set_client_response_params(array $response_params) : array
{
  $request_params = get_request_params();
  $path = isset($request_params->route_path) ? $request_params->route_path : '';

  $response_params['get']         = $request_params->get;
  $response_params['currentPage'] = $request_params->current_page;
  $response_params['maxPages']    = $request_params->max_pages;

  if (isset($request_params->query_filter))
  {
    $wp_term = get_term_by('slug', $request_params->query_filter['slug'], $request_params->query_filter['taxonomy']);
    $response_params['filter']['taxonomy']    = $request_params->query_filter['rest_taxonomy'];
    $response_params['filter']['taxonomy_id'] = $wp_term->term_id;
  }

  if ($request_params->max_pages > 1)
  {
    if ($request_params->current_page === 1)
    {
      $response_params['nextPage'] = '/' . $path . '/page/' . ($request_params->current_page + 1) . '/';
    }
    else if ($request_params->current_page < $request_params->max_pages)
    {
      $response_params['prevPage'] = '/' . $path . '/page/' . ($request_params->current_page - 1) . '/';
      $response_params['nextPage'] = '/' . $path . '/page/' . ($request_params->current_page + 1) . '/';
    }
    else
    {
      $response_params['prevPage'] = '/' . $path . '/page/' . ($request_params->current_page - 1) . '/';
    }

    if ($request_params->current_page === 2)
      $response_params['prevPage'] = '/' . $path . '/';
  }
  else if (isset($request_params->query_vars['first_letter']))
  {
    $letters = $request_params->query_vars['letters_range'];
    $index   = array_search($request_params->query_vars['first_letter'], $letters);

    if ($index === 0)
    {
      $response_params['nextPage'] = '/' . $path . '/b/';
    }
    else if (($index + 1) < count($letters))
    {
      $response_params['prevPage'] = '/' . $path . '/' . $letters[$index - 1] . '/';
      $response_params['nextPage'] = '/' . $path . '/' . $letters[$index + 1] . '/';
    }
    else
    {
      $response_params['prevPage'] = '/' . $path . '/' . $letters[$index - 1] . '/';
    }
  }

  // Prepend full site url for better client side validation + append parameters if present
  if ($response_params['prevPage'] !== null)
  {
    $response_params['prevPage']  = PLUGIN_ENV['site_url'] . $response_params['prevPage'];
    $response_params['prevPage'] .= ($request_params->query['string'] !== null) ? "?{$request_params->query['string']}" : '';
  }

  if ($response_params['nextPage'] !== null)
  {
    $response_params['nextPage']  = PLUGIN_ENV['site_url'] . $response_params['nextPage'];
    $response_params['nextPage'] .= ($request_params->query['string'] !== null) ? "?{$request_params->query['string']}" : '';
  }

  return $response_params;
}

function get_client_response_params() : array
{
  $is_user_per_page = (is_shuffle() || is_search() || is_list_player('search'));

  $response_params = [
    'prevPage'       => null,
    'nextPage'       => null,
    'shufflePath'    => esc_url(PLUGIN_ENV['site_url'] . get_shuffle_path()),
    'listPerPage'    => $is_user_per_page ? get_prop('list_per_page')    : get_settings_value('list_tracks_per_page'),
    'galleryPerPage' => $is_user_per_page ? get_prop('gallery_per_page') : get_settings_value('gallery_tracks_per_page'),
  ];

  // Return defaults because get_next_posts_link() returns results even when a 404 happens
  if (is_404())
    return $response_params;

  if (is_termlist() || is_list_player())
    return set_client_response_params($response_params);

  if (is_single())
  {
    // Reverse order for: prev = left direction and next = right direction (orderby: from New to Old)
    $prevPost = get_next_post();
    $nextPost = get_previous_post();

    if (!empty($prevPost))
      $prevUrl = get_the_permalink($prevPost->ID);

    if (!empty($nextPost))
      $nextUrl = get_the_permalink($nextPost->ID);

    $response_params['prevPage'] = isset($prevUrl) ? $prevUrl : null;
    $response_params['nextPage'] = isset($nextUrl) ? $nextUrl : null;
  }
  else
  {
    $prevLink = get_previous_posts_link('');
    $nextLink = get_next_posts_link('');

    if ($prevLink !== null)
      $prevUrl = new SimpleXMLElement($prevLink);

    if ($nextLink !== null)
      $nextUrl = new SimpleXMLElement($nextLink);

    $response_params['prevPage'] = isset($prevUrl) ? ((string) $prevUrl['href']) : null;
    $response_params['nextPage'] = isset($nextUrl) ? ((string) $nextUrl['href']) : null;
  }

  return $response_params;
}

//
// Get shuffle URL from current context
//
function get_shuffle_path() : string
{
  $params = get_request_params();

  if (is_list_player())
  {
    $request_path = '/list/shuffle/all/';

    if (is_shuffle(PLAYER_TYPE::LIST))
      $request_path = '/' . $params->route_path . '/';
    else if (is_list_player('channel') || is_list_player('artist'))
      $request_path = '/' . str_ireplace('list/', 'list/shuffle/', $params->route_path) . '/';

    return $request_path;
  }

  $request_path = '/shuffle/all/';

  if (is_shuffle(PLAYER_TYPE::GALLERY))
  {
    $request_path = '/shuffle/' . $params->path . '/';
  }
  else
  {
    $queried_object = get_queried_object();

    if (isset($queried_object) && isset($queried_object->taxonomy) && isset($queried_object->slug))
    {
      if ($queried_object->taxonomy === 'uf_channel')
        $request_path = '/shuffle/channel/' . $queried_object->slug . '/';
      else if ($queried_object->taxonomy === 'uf_artist')
        $request_path = '/shuffle/artist/' . $queried_object->slug . '/';
    }
  }

  return $request_path;
}
