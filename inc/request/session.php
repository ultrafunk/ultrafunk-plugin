<?php declare(strict_types=1);
/*
 * Setup session data for this request
 *
 */


namespace Ultrafunk\Plugin\Request;


use SimpleXMLElement;

use Ultrafunk\Plugin\Shared\PLAYER_TYPE;

use const Ultrafunk\Plugin\Config\PLUGIN_ENV;

use function Ultrafunk\Plugin\Globals\ {
  get_global,
  get_settings_value,
  is_list_player,
  is_shuffle,
  is_termlist,
  get_request_params,
};


/**************************************************************************************************************************/


//
// Set request session variables for client side use (JavaScript)
//
function set_request_session_vars(array $session_vars) : array
{
  $params = get_request_params();
  $path   = isset($params->route_path) ? $params->route_path : '';

  $session_vars['get']         = $params->get;
  $session_vars['currentPage'] = $params->current_page;
  $session_vars['maxPages']    = $params->max_pages;

  if (isset($params->query_filter))
  {
    $wp_term = get_term_by('slug', $params->query_filter['slug'], $params->query_filter['taxonomy']);
    $session_vars['filter']['taxonomy']    = $params->query_filter['rest_taxonomy'];
    $session_vars['filter']['taxonomy_id'] = $wp_term->term_id;
  }

  if ($params->max_pages > 1)
  {
    if ($params->current_page === 1)
    {
      $session_vars['nextPage'] = '/' . $path . '/page/' . ($params->current_page + 1) . '/';
    }
    else if ($params->current_page < $params->max_pages)
    {
      $session_vars['prevPage'] = '/' . $path . '/page/' . ($params->current_page - 1) . '/';
      $session_vars['nextPage'] = '/' . $path . '/page/' . ($params->current_page + 1) . '/';
    }
    else
    {
      $session_vars['prevPage'] = '/' . $path . '/page/' . ($params->current_page - 1) . '/';
    }

    if ($params->current_page === 2)
      $session_vars['prevPage'] = '/' . $path . '/';
  }
  else if (isset($params->query_vars['first_letter']))
  {
    $letters = $params->query_vars['letters_range'];
    $index   = array_search($params->query_vars['first_letter'], $letters);

    if ($index === 0)
    {
      $session_vars['nextPage'] = '/' . $path . '/b/';
    }
    else if (($index + 1) < count($letters))
    {
      $session_vars['prevPage'] = '/' . $path . '/' . $letters[$index - 1] . '/';
      $session_vars['nextPage'] = '/' . $path . '/' . $letters[$index + 1] . '/';
    }
    else
    {
      $session_vars['prevPage'] = '/' . $path . '/' . $letters[$index - 1] . '/';
    }
  }

  // Prepend full site url for better client side validation + append parameters if present
  if ($session_vars['prevPage'] !== null)
  {
    $session_vars['prevPage']  = PLUGIN_ENV['site_url'] . $session_vars['prevPage'];
    $session_vars['prevPage'] .= ($params->query['string'] !== null) ? "?{$params->query['string']}" : '';
  }

  if ($session_vars['nextPage'] !== null)
  {
    $session_vars['nextPage']  = PLUGIN_ENV['site_url'] . $session_vars['nextPage'];
    $session_vars['nextPage'] .= ($params->query['string'] !== null) ? "?{$params->query['string']}" : '';
  }

  return $session_vars;
}

//
// Get prev + next post/posts URLs + other navigation variables
//
function get_session_vars() : array
{
  $is_user_per_page = (is_shuffle() || is_search() || is_list_player('search'));

  $session_vars = [
    'prevPage'       => null,
    'nextPage'       => null,
    'shufflePath'    => esc_url(PLUGIN_ENV['site_url'] . get_shuffle_path()),
    'listPerPage'    => $is_user_per_page ? get_global('list_per_page')    : get_settings_value('list_tracks_per_page'),
    'galleryPerPage' => $is_user_per_page ? get_global('gallery_per_page') : get_settings_value('gallery_tracks_per_page'),
  ];

  // Return defaults because get_next_posts_link() returns results even when a 404 happens
  if (is_404())
    return $session_vars;

  if (is_termlist() || is_list_player())
    return set_request_session_vars($session_vars);

  if (is_single())
  {
    // Reverse order for: prev = left direction and next = right direction (orderby: from New to Old)
    $prevPost = get_next_post();
    $nextPost = get_previous_post();

    if (!empty($prevPost))
      $prevUrl = get_the_permalink($prevPost->ID);

    if (!empty($nextPost))
      $nextUrl = get_the_permalink($nextPost->ID);

    $session_vars['prevPage'] = isset($prevUrl) ? $prevUrl : null;
    $session_vars['nextPage'] = isset($nextUrl) ? $nextUrl : null;
  }
  else
  {
    $prevLink = get_previous_posts_link('');
    $nextLink = get_next_posts_link('');

    if ($prevLink !== null)
      $prevUrl = new SimpleXMLElement($prevLink);

    if ($nextLink !== null)
      $nextUrl = new SimpleXMLElement($nextLink);

    $session_vars['prevPage'] = isset($prevUrl) ? ((string) $prevUrl['href']) : null;
    $session_vars['nextPage'] = isset($nextUrl) ? ((string) $nextUrl['href']) : null;
  }

  return $session_vars;
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
