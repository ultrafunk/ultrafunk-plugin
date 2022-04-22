<?php declare(strict_types=1);
/*
 * Request Handler abstract base class
 *
 */


namespace Ultrafunk\Plugin\Request;


use const Ultrafunk\Plugin\Constants\PLUGIN_ENV;

use function Ultrafunk\Plugin\Shared\console_log;

use function Ultrafunk\Plugin\Globals\ {
  perf_stop,
  set_is_custom_query,
};


/**************************************************************************************************************************/


abstract class RequestHandler
{
  protected $wp_env        = null;
  protected $route_request = null;

  public $is_valid_request = false;
  public $request_params   = [];
  public $route_path       = null;
  public $title_parts      = null;
  public $items_per_page   = 0;
  public $current_page     = 1;
  public $max_pages        = 1;
  public $query_args       = [];
  public $query_result     = null;
  
  public function __construct(object $wp_env, object $route_request, string $type_key = 'UNKNOWN_REQUEST_TYPE')
  {
    $this->wp_env         = $wp_env;
    $this->route_request  = $route_request;
    $this->items_per_page = PLUGIN_ENV['list_per_page'];

    $this->request_params['get'][$type_key] = true;
    $this->request_params['data']  = [];
    $this->request_params['query'] = [
      'string' => $this->route_request->query_string,
      'params' => $this->route_request->query_params,
    ];
  }

  protected function set_request_params() : void
  {
    $this->request_params['route_path']     = $this->route_path;
    $this->request_params['title_parts']    = $this->title_parts;
    $this->request_params['items_per_page'] = $this->items_per_page;
    $this->request_params['current_page']   = $this->current_page;
    $this->request_params['max_pages']      = $this->max_pages;

    \Ultrafunk\Plugin\Globals\set_request_params($this->request_params);
  }

  protected function get_current_page(array $path_parts, int $path_part_index) : int
  {
    return (isset($path_parts[$path_part_index])
             ? intval($path_parts[$path_part_index])
             : 1);
  }
  
  protected function get_max_pages(int $item_count, int $items_per_page) : int
  {
    if ($item_count === 0)
      return 0;
      
    return (($item_count > $items_per_page)
             ? ((int)ceil($item_count / $items_per_page))
             : 1);
  }

  abstract protected function parse_validate_set_params() : bool;

  private function request_query(string $query_class) : object
  {
    set_is_custom_query(true);
    $query_result = new $query_class($this->query_args);
    set_is_custom_query(false);

    return $query_result;
  }

  public function get_request_response() : void
  {
    if ($this->parse_validate_set_params())
    {
      if (!empty($this->request_params['get']['list_player']))
      {
        $this->query_args['post_type']        = 'uf_track';
        $this->query_args['paged']            = $this->current_page;
        $this->query_args['posts_per_page']   = $this->items_per_page;
        $this->query_args['suppress_filters'] = $this->query_args['suppress_filters'] ?? true;

        $wp_query_result = $this->request_query('WP_Query');

        $this->query_result     = $wp_query_result->posts;
        $this->max_pages        = $this->get_max_pages($wp_query_result->found_posts, $this->items_per_page);
        $this->is_valid_request = $wp_query_result->have_posts();
      }
      else if (!empty($this->request_params['get']['termlist']))
      {
        $this->query_args['hide_empty'] = true;
        
        $wp_term_query_result = $this->request_query('WP_Term_Query');

        if ($wp_term_query_result->terms !== null)
        {
          $this->request_params['data']['item_count'] = count($wp_term_query_result->terms);
          $this->query_result     = $wp_term_query_result->terms;
          $this->is_valid_request = true;
        }
      }

    //console_log($this);
    }
  }

  
  /**************************************************************************************************************************/

  
  private function begin_output() : void
  {
    // Show debug info for this request
    //console_log($this->request_params);

    // Output HTTP headers
    $this->wp_env->send_headers();
    
    // Get site template header
    get_header();
  }

  private function end_output() : void
  {
    // Stop performance counter
    perf_stop('route_request', 'RouteRequest_start');
    
    // Get site template footer
    get_footer();

    // We are DONE!
    exit;
  }

  public function render_content(string $template_name, string $render_function) : void
  {
    if ($this->is_valid_request)
    {
      // Set public parameters for this request
      $this->set_request_params();

      $this->begin_output();

      // Load template file for this request
      require get_template_directory() . '/php/templates/' . $template_name;

      // Call the template files entry point for rendering content
      $render_function($this);
      
      $this->end_output();
    }
    else
    {
      global $wp_query;
      $response_params = array('response' => $this->request_params['get']);

      // Setup global $wp_query so it contains relevant data to handle this request failure...
      if (!empty($this->request_params['get']['search']))
      {
        $response_params['error']  = ['http_status' => 200, 'details' => 'No search matches'];
        $wp_query->is_search       = true;
        $wp_query->query_vars['s'] = $this->route_request->query_params['s'];
      }
      else
      {
        $response_params['error'] = ['http_status' => 404, 'details' => 'Page not found'];
        $wp_query->set_404();
        status_header(404);
      }

      \Ultrafunk\Plugin\Globals\set_request_params($response_params);

      $this->begin_output();
      get_template_part('php/templates/content', 'none');
      $this->end_output();
    }
  }
}
