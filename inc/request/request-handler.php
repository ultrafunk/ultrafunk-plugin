<?php declare(strict_types=1);
/*
 * Request Handler abstract base class
 *
 */


namespace Ultrafunk\Plugin\Request;


use const Ultrafunk\Plugin\Constants\PLUGIN_ENV;

use function Ultrafunk\Plugin\Shared\console_log;

use function Ultrafunk\Plugin\Globals\ {
  get_request_params,
  perf_stop,
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
  
  public function __construct(object $wp_env, object $route_request, string $type_key = 'UNKNOWN_REQUEST_TYPE')
  {
    $this->wp_env         = $wp_env;
    $this->route_request  = $route_request;
    $this->items_per_page = PLUGIN_ENV['list_per_page'];

    $this->request_params['request_type'][$type_key] = true;
    $this->request_params['request_data'] = [];
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
             ? \intval($path_parts[$path_part_index])
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

  abstract public function parse_validate_set_params() : void;
  
  public function render_content(string $template_name, string $template_function) : void
  {
    if ($this->is_valid_request)
    {
      // Set public handler parameters for this request
      $this->set_request_params();

      // Debug info for the current request
    //console_log($this);
    //console_log(get_request_params());

      // Load template file for this request
      require_once get_template_directory() . '/php/templates/' . $template_name;

      // Output HTTP headers
      $this->wp_env->send_headers();
      
      // Get site template header
      get_header();
      
      // Call the template files entry point for rendering content
      $template_function($this);
      
      // Stop performance counter
      perf_stop('RouteRequest', 'RouteRequest_start');
      
      // Get site template footer
      get_footer();

      // We are DONE!
      exit;
    }
  }
}
