<?php declare(strict_types=1);
/*
 * Shared plugin + theme functions
 *
 */


namespace Ultrafunk\Plugin\Shared;


use const Ultrafunk\Plugin\Constants\PLUGIN_ENV;

use Ultrafunk\Plugin\Constants\ {
  COOKIE_KEY,
  TRACK_TYPE,
};

use function Ultrafunk\Plugin\Globals\get_request_params;


/**************************************************************************************************************************/


//
// Output debug info to the browser console, this will not always work and may cause strange side effects...
//
function console_log($output) : void
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


function request_pagination(object $request_handler) : void
{
  if (isset($request_handler->max_pages) && ($request_handler->max_pages > 1))
  {
    $args = [
      'base'      => "/$request_handler->route_path/%_%",
      'format'    => 'page/%#%/',
      'total'     => $request_handler->max_pages,
      'current'   => $request_handler->current_page,
      'type'      => 'list',
      'mid_size'  => 4,
      'prev_text' => '&#10094;&#10094; Prev.',
      'next_text' => 'Next &#10095;&#10095;',
    ];

    ?>
    <nav class="navigation pagination" aria-label="Pagination">
      <h2 class="screen-reader-text">Pagination</h2>
      <div class="nav-links">
        <?php echo paginate_links($args); ?>
      </div>
    </nav>
    <?php
  }
}


/**************************************************************************************************************************/


function set_list_session_vars(array $session_vars) : array
{
  $params = get_request_params();
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


/**************************************************************************************************************************/


const YOUTUBE_VIDEO_ID_REGEX = '/[0-9A-Za-z_-]{10}[048AEIMQUYcgkosw]/';

const DEFAULT_TRACK_DATA = [
  'track_type'   => TRACK_TYPE::SOUNDCLOUD,
  'thumnail_src' => '/wp-content/themes/ultrafunk/inc/img/sc_thumbnail_placeholder.png',
  'css_class'    => 'type-soundcloud',
  'source_uid'   => null,
];

function get_track_data(object $track) : array
{
  if (intval($track->track_source_type) === TRACK_TYPE::YOUTUBE)
  {
    preg_match(YOUTUBE_VIDEO_ID_REGEX, $track->track_source_data, $source_uid);

    return [
      'track_type'   => TRACK_TYPE::YOUTUBE,
      'thumnail_src' => "https://img.youtube.com/vi/$source_uid[0]/default.jpg",
      'css_class'    => 'type-youtube',
      'source_uid'   => $source_uid[0],
    ];
  }

  return DEFAULT_TRACK_DATA;
}
