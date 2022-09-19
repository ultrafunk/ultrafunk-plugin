<?php declare(strict_types=1);
/*
 * Shared plugin + theme functions
 *
 */


namespace Ultrafunk\Plugin\Shared;


use const Ultrafunk\Plugin\Constants\PLUGIN_ENV;

use Ultrafunk\Plugin\Constants\COOKIE_KEY;


/**************************************************************************************************************************/


//
// Output debug info to the browser console, this will not always work and may cause strange side effects...
//
function console_log(mixed $output) : void
{
  if (WP_DEBUG)
    echo '<script>console.log(' . json_encode($output, JSON_HEX_TAG) . ');</script>';
}

//
// Get named cookie value if it exists with range check
//
function get_cookie_value(string $cookie_name, int $min_val, int $max_val, int $default_val) : int
{
  if (isset($_COOKIE[$cookie_name]))
  {
    $cookie_val = intval($_COOKIE[$cookie_name]);

    if (($cookie_val >= $min_val) && ($cookie_val <= $max_val))
      return $cookie_val;
  }

  return $default_val;
}

/*
//
// Get named cookie json data if it exists
//
function get_cookie_json(string $cookie_name) : ?object
{
  if (isset($_COOKIE[$cookie_name]))
  {
    $cookie_data = json_decode(stripslashes($_COOKIE[$cookie_name]));

    if ($cookie_data !== null)
      return $cookie_data;
  }

  return null;
}
*/

//
// Get UID cookie for random shuffle transient name
//
function get_shuffle_transient_name() : string
{
  if (isset($_COOKIE[COOKIE_KEY::UF_SHUFFLE_UID]))
  {
    $cookie = sanitize_user(wp_unslash($_COOKIE[COOKIE_KEY::UF_SHUFFLE_UID]), true);

    if (strlen($cookie) < 50)
      return sprintf('random_shuffle_%s', $cookie);
  }

  return '';
}


/**************************************************************************************************************************/


function set_list_session_vars(array $session_vars) : array
{
  $params = \Ultrafunk\Plugin\Globals\get_request_params();
  $data   = $params['data'];
  $query  = $params['query'];
  $path   = isset($params['route_path']) ? $params['route_path'] : '';

  $session_vars['params']      = $params['get'];
  $session_vars['currentPage'] = $params['current_page'];
  $session_vars['maxPages']    = $params['max_pages'];

  if (isset($params['max_pages']) && ($params['max_pages'] > 1))
  {
    if ($params['current_page'] === 1)
    {
      $session_vars['nextPage'] = '/' . $path . '/page/' . ($params['current_page'] + 1) . '/';
    }
    else if ($params['current_page'] < $params['max_pages'])
    {
      $session_vars['prevPage'] = '/' . $path . '/page/' . ($params['current_page'] - 1) . '/';
      $session_vars['nextPage'] = '/' . $path . '/page/' . ($params['current_page'] + 1) . '/';
    }
    else
    {
      $session_vars['prevPage'] = '/' . $path . '/page/' . ($params['current_page'] - 1) . '/';
    }

    if ($params['current_page'] === 2)
      $session_vars['prevPage'] = '/' . $path . '/';
  }
  else if (isset($data['first_letter']))
  {
    $letters = $data['letters_range'];
    $index   = array_search($data['first_letter'], $letters);

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
    $session_vars['prevPage'] .= ($query['string'] !== null) ? "?{$query['string']}" : '';
  }

  if ($session_vars['nextPage'] !== null)
  {
    $session_vars['nextPage']  = PLUGIN_ENV['site_url'] . $session_vars['nextPage'];
    $session_vars['nextPage'] .= ($query['string'] !== null) ? "?{$query['string']}" : '';
  }

  return $session_vars;
}
