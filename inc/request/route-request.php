<?php declare(strict_types=1);
/*
 * Custom request router
 *
 */


namespace Ultrafunk\Plugin\Request;


use const Ultrafunk\Plugin\Config\ {
  IS_PROD_BUILD,
  PLUGIN_ENV,
};

use const Ultrafunk\Plugin\Request\DEFAULT_ROUTES;

use function Ultrafunk\Plugin\Globals\ {
  perf_start,
  perf_stop,
};


/**************************************************************************************************************************/


class RouteRequest
{
  private ?string $server_url = null;

  public ?string $request_path  = null;
  public ?array  $path_parts    = null;
  public ?string $query_string  = null;
  public array   $query_params  = [];
  public ?string $matched_route = null;
  public ?string $handler_file  = null;
  public ?string $handler_class = null;

  private function match_route_uid(string $route_uid) : bool
  {
    // If $route_uid starts with '===', match exactly
    if (str_starts_with($route_uid, '==='))
      return (substr($route_uid, 3) === $this->request_path);
    else if (str_starts_with($this->request_path, $route_uid))
      return true;

    return false;
  }

  private function find_route_key(array $routes) : ?int
  {
    foreach($routes as $key => $route)
    {
      foreach($route['route_uids'] as $route_uid)
      {
        if ($this->match_route_uid($route_uid))
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
    $url_params = ($this->query_string !== null) ? "?$this->query_string" : '';

    // Redirect when URL ends with /page/1 for WP + pretty permalinks behaviour
    if (substr_compare($this->request_path, $page_1, -strlen($page_1)) === 0)
    {
      wp_redirect('/' . substr($this->request_path, 0, -strlen($page_1)) . '/' . $url_params, 301);
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

      if (isset($url_parts[1]))
      {
        $this->query_string = $url_parts[1];
        parse_str($url_parts[1], $this->query_params);
      }

      if (!empty($this->request_path) && (strlen($this->request_path) < 1024))
      {
        $this->path_parts = explode('/', $this->request_path, 25);
        $route_key        = $this->find_route_key($routes);

        if ($route_key !== null)
        {
          foreach($routes[$route_key]['routes'] as $key => $route)
          {
            if (preg_match($route, $this->request_path) === 1)
            {
              if ($this->request_needs_redirect($url_parts[0]) === false)
              {
                $this->matched_route = $key;
                $this->handler_file  = $routes[$route_key]['handler']['file']  ?? null;
                $this->handler_class = $routes[$route_key]['handler']['class'] ?? null;

                return true;
              }
            }
          }
        }
      }
    }

    return false;
  }

  public function has_request_handler() : bool
  {
    return (!empty($this->handler_file) && !empty($this->handler_class));
  }

  public function new_request_handler(object $wp_env, object $route_request) : object
  {
    $handler_class = PLUGIN_ENV['handler_class_path'] . $this->handler_class;
    return new $handler_class($wp_env, $route_request);
  }
}


/**************************************************************************************************************************/


//
// wp_is_rest_request() does not work until AFTER do_parse_request(): https://core.trac.wordpress.org/ticket/42061
// Based on WooCommerce: https://github.com/woocommerce/woocommerce/blob/a8fff3175ccbb460dd0d1be1aefccf4488498105/plugins/woocommerce/includes/class-woocommerce.php#L357
//
function is_rest_request() : bool
{
  if (empty($_SERVER['REQUEST_URI']))
    return false;

  $rest_prefix = trailingslashit(rest_get_url_prefix());

  return (strpos($_SERVER['REQUEST_URI'], $rest_prefix) !== false);
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
      (wp_doing_cron()   === false) &&
      (wp_doing_ajax()   === false) &&
      (is_rest_request() === false))
  {
    // ToDo: Better solution?
    // Only logged in users have access to custom routes on dev
    if ((IS_PROD_BUILD === false) && (is_user_logged_in() === false))
      return $do_parse;

    if (is_valid_permalink_structure() === false)
      return $do_parse;

    $route_request = new RouteRequest();

    if ($route_request->host_matches()  &&
        $route_request->route_matches() &&
        $route_request->has_request_handler())
    {
      require ULTRAFUNK_PLUGIN_PATH . PLUGIN_ENV['handler_file_path'] . $route_request->handler_file;

      $request_handler = $route_request->new_request_handler($wp_env, $route_request);
      $request_handler->get_response();

      return $request_handler->render_content();
    }
  }

  perf_stop('route_request', 'RouteRequest_start');

  return $do_parse;
}
add_filter('do_parse_request', '\Ultrafunk\Plugin\Request\parse_request', 10, 2);
