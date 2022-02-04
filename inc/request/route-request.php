<?php declare(strict_types=1);
/*
 * Custom request router
 *
 */


namespace Ultrafunk\Plugin\Request\RouteRequest;


use const Ultrafunk\Plugin\Constants\PLUGIN_ENV;

use function Ultrafunk\Plugin\Globals\ {
  perf_start,
  perf_stop,
};


/**************************************************************************************************************************/


const DEFAULT_ROUTES =
[
  [
    'route_uid'         => 'list',
    'match_uid_exactly' => true,
    'handler_file'      => 'inc/request/list-player-all.php',
    'handler_class'     => '\Ultrafunk\Plugin\Request\ListPlayerAll',
    'template_file'     => 'content-list-player.php',
    'template_class'    => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_all' => '/^list$/',
    ]
  ],
  [
    'route_uid'      => 'list/page/',
    'handler_file'   => 'inc/request/list-player-all.php',
    'handler_class'  => '\Ultrafunk\Plugin\Request\ListPlayerAll',
    'template_file'  => 'content-list-player.php',
    'template_class' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_all_page' => '/^list\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'     => 'shuffle/',
    'handler_file'  => 'inc/request/shuffle.php',
    'handler_class' => '\Ultrafunk\Plugin\Request\Shuffle',
    'routes' => [
      'shuffle_all'       => '/^shuffle\/all$/',
      'shuffle_all_page'  => '/^shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'      => 'artists',
    'handler_file'   => 'inc/request/termlist-artists.php',
    'handler_class'  => '\Ultrafunk\Plugin\Request\TermlistArtists',
    'template_file'  => 'content-termlist.php',
    'template_class' => '\Ultrafunk\Theme\Templates\Termlist',
    'routes' => [
      'artists'             => '/^artists$/',
    //'artists_page'        => '/^artists\/page\/(?!0)\d{1,6}$/',
      'artists_letter'      => '/^artists\/[a-z]$/',
    //'artists_letter_page' => '/^artists\/[a-z]\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'         => 'channels',
    'match_uid_exactly' => true,
    'handler_file'      => 'inc/request/termlist-channels.php',
    'handler_class'     => '\Ultrafunk\Plugin\Request\TermlistChannels',
    'template_file'     => 'content-termlist.php',
    'template_class'    => '\Ultrafunk\Theme\Templates\Termlist',
    'routes' => [
      'channels'      => '/^channels$/',
    //'channels_page' => '/^channels\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'      => 'list/artist/',
    'handler_file'   => 'inc/request/list-player-artist-channel.php',
    'handler_class'  => '\Ultrafunk\Plugin\Request\ListPlayerArtistChannel',
    'template_file'  => 'content-list-player.php',
    'template_class' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_artist'      => '/^list\/artist\/[a-z0-9-]*$/',
      'list_player_artist_page' => '/^list\/artist\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'      => 'list/channel/',
    'handler_file'   => 'inc/request/list-player-artist-channel.php',
    'handler_class'  => '\Ultrafunk\Plugin\Request\ListPlayerArtistChannel',
    'template_file'  => 'content-list-player.php',
    'template_class' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_channel'      => '/^list\/channel\/[a-z0-9-]*$/',
      'list_player_channel_page' => '/^list\/channel\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'      => 'list/20',
    'handler_file'   => 'inc/request/list-player-date.php',
    'handler_class'  => '\Ultrafunk\Plugin\Request\ListPlayerDate',
    'template_file'  => 'content-list-player.php',
    'template_class' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_date'      => '/^list\/20[0-9]{2}\/[0-3][0-9]$/',
      'list_player_date_page' => '/^list\/20[0-9]{2}\/[0-3][0-9]\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'      => 'list/shuffle/',
    'handler_file'   => 'inc/request/list-player-shuffle.php',
    'handler_class'  => '\Ultrafunk\Plugin\Request\ListPlayerShuffle',
    'template_file'  => 'content-list-player.php',
    'template_class' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'shuffle_all'       => '/^list\/shuffle\/all$/',
      'shuffle_all_page'  => '/^list\/shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
];


/**************************************************************************************************************************/


class RouteRequest
{
  private $server_url    = null;
  public $request_path   = null;
  public $path_parts     = null;
  public $params         = null;
  public $matched_route  = null;
  public $handler_file   = null;
  public $handler_class  = null;
  public $template_file  = null;
  public $template_class = null;

  public function __construct() {}

  private function find_route_key(array $routes) : ?int
  {
    foreach($routes as $key => $value)
    {
      /*
      if (!empty($value['match_uid_exactly']))
      
      *** EQUALS ***
      
      if (isset($value['match_uid_exactly']) && ($value['match_uid_exactly'] === true))
      */
  
      if (!empty($value['match_uid_exactly']))
      {
        if ($value['route_uid'] === $this->request_path)
          return $key;
      }
      else if (str_starts_with($this->request_path, $value['route_uid']))
      {
        return $key;
      }
    }

    return null;
  }

  //
  // Make routed URLs consistent with WP + pretty permalinks enabled
  //
  private function request_needs_redirect(string $route_path) : bool
  {
    $page_1     = '/page/1';
    $url_params = ($this->params !== null) ? "?$this->params" : '';

    // Redirect when URL ends with /page/1 for WP + pretty permalinks behaviour
    if (substr_compare($this->request_path, $page_1, -\strlen($page_1)) === 0)
    {
      wp_redirect('/' . substr($this->request_path, 0, -\strlen($page_1)) . '/' . $url_params, 301);
      exit;
    }

    // This is better than using .htaccess "hacks" to add trailing path slash to ONLY custom routes
    // Trailing path slash is needed for page caching to work properly when using WP Fastest Cache...
    if ($route_path[-1] !== '/')
    {
      wp_redirect('/' . $this->request_path . '/' . $url_params, 301);
      exit;
    }

    return false;
  }

  public function host_matches() : bool
  {
    if (isset($_SERVER['REQUEST_SCHEME']) && isset($_SERVER['HTTP_HOST']))
    {
      $this->server_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
      return (PLUGIN_ENV['site_url'] === $this->server_url);
    }

    return false;
  }

  public function route_matches(string $request_url = NULL, array $routes = DEFAULT_ROUTES) : bool
  {
    if ($request_url === null)
      $request_url = $_SERVER['REQUEST_URI'];
    
    if (isset($request_url) && isset($routes))
    {
      $esc_request_url    = esc_url_raw($request_url);
      $url_parts          = explode('?', $esc_request_url, 2);
      $this->request_path = trim($url_parts[0], '/');

      if (!empty($this->request_path) && (strlen($this->request_path) < 1024))
      {
        $this->path_parts = explode('/', $this->request_path, 25);
        $this->params     = isset($url_parts[1]) ? $url_parts[1] : null;
        $route_key        = $this->find_route_key($routes);

        if ($route_key !== null)
        {
          foreach($routes[$route_key]['routes'] as $key => $route)
          {
            if (preg_match($route, $this->request_path) === 1)
            {
              if ($this->request_needs_redirect($url_parts[0]) === false)
              {
                $this->matched_route  = $key;
                $this->handler_file   = $routes[$route_key]['handler_file']   ?? null;
                $this->handler_class  = $routes[$route_key]['handler_class']  ?? null;
                $this->template_file  = $routes[$route_key]['template_file']  ?? null;
                $this->template_class = $routes[$route_key]['template_class'] ?? null;
                return true;
              }
            }
          }
        }
      }
    }

    return false;
  }
}


/**************************************************************************************************************************/


//
// wp_is_rest_request() does not work until AFTER do_parse_request(): https://core.trac.wordpress.org/ticket/42061
// 
function is_rest_request() : bool
{
  return (isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], rest_get_url_prefix()) !== false));
}

//
// Minimal check for valid permalink structure
//
function is_valid_permalink_structure() : bool
{
  $permalink_structure = get_option('permalink_structure');

  // We need a custom permalink structure to be set
  if (($permalink_structure === false) || empty($permalink_structure))
    return false;

  // And we need the permalink structure to end with a trailing slash
  if ($permalink_structure[-1] !== '/')
    return false;

  return true;
}


/**************************************************************************************************************************/


//
// Filter do_parse_request to check for any custom routes
// This requires pretty permalinks to be enabled!
//
function parse_request(bool $do_parse, object $wp_env) : bool
{
  perf_start('RouteRequest_start');

  if ((is_admin()        === false) &&
      (wp_doing_ajax()   === false) &&
      (is_rest_request() === false))
  {
    // ToDo: Better solution?
    // Make sure that only logged in users have access to custom routes when debug is enabled
    if (WP_DEBUG && (is_user_logged_in() === false))
      return $do_parse;

    if (is_valid_permalink_structure() === false)
      return $do_parse;

    $route_request = new RouteRequest();

    if ($route_request->host_matches()          &&
        $route_request->route_matches()         &&
       ($route_request->handler_file  !== null) &&
       ($route_request->handler_class !== null))
    {
      require ULTRAFUNK_PLUGIN_PATH . $route_request->handler_file;
      
      $request_handler = new $route_request->handler_class($wp_env, $route_request);
      $request_handler->parse_validate_set_params();

      if (($route_request->template_file !== null) && ($route_request->template_class !== null))
      {
        $request_handler->render_content($route_request->template_file, $route_request->template_class . '\render_template');
      }
      else if ($request_handler->is_valid_request)
      {
        perf_stop('RouteRequest', 'RouteRequest_start');

        // return false = We have parsed / handled this request, continue with our query_vars
        // https://developer.wordpress.org/reference/hooks/do_parse_request/        
        return false;
      }
    }
  }

  perf_stop('RouteRequest', 'RouteRequest_start');
  
  return $do_parse;
}
add_filter('do_parse_request', '\Ultrafunk\Plugin\Request\RouteRequest\parse_request', 10, 2);
