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
  protected $wp_env           = null;
  protected $route_request    = null;
  protected $template_file    = null;
  protected $template_class   = null;
  protected $is_valid_request = false;
  
  public $request_params = [];
  public $route_path     = null;
  public $title_parts    = null;
  public $items_per_page = 0;
  public $current_page   = 1;
  public $max_pages      = 1;
  public $query_args     = [];
  public $query_result   = null;
  public $filter_slug    = null;
  public $filter_tax     = null;
  
  public function __construct(
    object $wp_env,
    object $route_request,
    string $type_key = 'UNKNOWN_REQUEST_TYPE',
    array $template = null,
  )
  {
    $this->wp_env         = $wp_env;
    $this->route_request  = $route_request;
    $this->items_per_page = PLUGIN_ENV['list_per_page'];

    $this->template_file  = isset($template['file'])  ? $template['file']  : PLUGIN_ENV['template_file'];
    $this->template_class = isset($template['class']) ? $template['class'] : PLUGIN_ENV['template_class'];

    $this->request_params['get'][$type_key] = true;
    $this->request_params['data']  = [];
    $this->request_params['query'] = [
      'string' => $this->route_request->query_string,
      'params' => $this->route_request->query_params,
    ];
  }

  abstract protected function parse_validate_set_params() : bool;

  protected function set_request_params() : void
  {
    $this->request_params['route_path']     = $this->route_path;
    $this->request_params['title_parts']    = $this->title_parts;
    $this->request_params['items_per_page'] = $this->items_per_page;
    $this->request_params['current_page']   = $this->current_page;
    $this->request_params['max_pages']      = $this->max_pages;

    if ($this->filter_slug !== null)
    {
      $this->request_params['filter']['slug']     = $this->filter_slug;
      $this->request_params['filter']['taxonomy'] = $this->filter_tax;
    }

    \Ultrafunk\Plugin\Globals\set_request_params($this->request_params);
  }

  protected function set_filter_params(string $key, string $taxonomy)
  {
    if (isset($this->route_request->query_params[$key]))
    {
      $this->filter_slug = sanitize_title($this->route_request->query_params[$key]);
      $this->filter_tax  = $taxonomy;
    }
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
  

  /**************************************************************************************************************************/

  
  private function request_query(string $query_class) : object
  {
    set_is_custom_query(true);
    $query_result = new $query_class($this->query_args);
    set_is_custom_query(false);

    return $query_result;
  }

  public function get_response() : void
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

    // WE ARE DONE!!!
    exit;
  }

  private function render_valid_response() : void
  {
    // Set public parameters for this request
    $this->set_request_params();

    $this->begin_output();

    // Load template class file for this request
    require get_template_directory() . PLUGIN_ENV['template_file_path'] . $this->template_file;

    $template_class = PLUGIN_ENV['template_class_path'] . $this->template_class;
    $template = new $template_class($this);
    $template->render();

    $this->end_output();
  }

  private function render_error_response() : void
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

  public function render_content() : bool
  {
    if (!empty($this->template_file) && !empty($this->template_class))
    {
      if ($this->is_valid_request)
        $this->render_valid_response();
      else
        $this->render_error_response();
    }
    else if ($this->is_valid_request)
    {
      perf_stop('route_request', 'RouteRequest_start');

      //
      // WordPress 6.X changed do_parse_request behaviour: https://wp.me/p2AvED-oWf
      //
      // If we want WordPress to continue normally with our parsed request parameters,
      // we have do to some of the WP-class => WP::main() heavy lifting for it...
      //

      $this->wp_env->query_posts();
      $this->wp_env->handle_404();
      $this->wp_env->register_globals();

      return false;
    }

    return true;
  }
}
