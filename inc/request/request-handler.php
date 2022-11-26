<?php declare(strict_types=1);
/*
 * Request Handler abstract base class
 *
 */


namespace Ultrafunk\Plugin\Request;


use stdClass;

use const Ultrafunk\Plugin\Constants\PLUGIN_ENV;

use function Ultrafunk\Plugin\Globals\ {
  perf_stop,
  set_is_custom_query,
  get_settings_value,
};


/**************************************************************************************************************************/


class RequestParams
{
  public ?string $route_path       = null;
  public ?array  $title_parts      = null;
  public int     $found_items      = 0;
  public int     $items_per_page   = 0;
  public int     $current_page     = 1;
  public int     $max_pages        = 1;
  public ?array  $get              = null;
  public ?array  $query            = null;
  public array   $filter           = ['slug' => null, 'taxonomy' => null];

  public function __construct(?string $query_string, ?array $query_params)
  {
    $this->items_per_page = get_settings_value('list_tracks_per_page');
    $this->query = ['string' => $query_string, 'params' => $query_params];
  }
}


/**************************************************************************************************************************/


abstract class RequestHandler
{
  protected bool    $is_valid_request = false;
  protected string  $template_file    = PLUGIN_ENV['template_file'];
  protected string  $template_class   = PLUGIN_ENV['template_class'];
  protected array   $query_args       = [];
  protected mixed   $query_result     = null;
  public    ?object $params           = null;

  public function __construct(protected object $wp_env, protected object $route_request)
  {
    $this->params = new RequestParams($route_request->query_string, $route_request->query_params);
    $this->params->current_page = $this->get_current_page();
  }

  abstract protected function has_valid_request_params() : bool;

  protected function set_filter_params(string $key, string $taxonomy = null) : void
  {
    if (isset($this->route_request->query_params[$key]))
    {
      $this->params->filter['slug']     = sanitize_title($this->route_request->query_params[$key]);
      $this->params->filter['taxonomy'] = $taxonomy;
    }
  }

  protected function get_current_page() : int
  {
    if (1 === preg_match('/\/page\/(?!0)\d{1,6}$/', $this->route_request->request_path, $matches))
      return (int)(explode('/', $matches[0])[2]);

    return 1;
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
    $this->add_filter_query_args();

    set_is_custom_query(true);
    $query_result = new $query_class($this->query_args);
    set_is_custom_query(false);

    return $query_result;
  }

  private function add_filter_query_args() : void
  {
    // Append filter (AND) second taxonomy term(s) if present
    if (($this->params->filter['slug'] !== null) && ($this->params->filter['taxonomy'] !== null))
    {
      $this->query_args['tax_query']    += ['relation' => 'AND'];
      $this->query_args['tax_query'][1]  = [
        'field'    => 'slug',
        'terms'    => $this->params->filter['slug'],
        'taxonomy' => $this->params->filter['taxonomy'],
      ];
    }
  }

  public function get_response() : void
  {
    if ($this->has_valid_request_params())
    {
      if (isset($this->params->get['list_player']))
      {
        $this->query_args['post_type']        = 'uf_track';
        $this->query_args['paged']            = $this->params->current_page;
        $this->query_args['posts_per_page']   = $this->params->items_per_page;
        $this->query_args['suppress_filters'] = $this->query_args['suppress_filters'] ?? true;

        $wp_query_result = $this->request_query('WP_Query');

        $this->query_result        = $wp_query_result->posts;
        $this->params->found_items = $wp_query_result->found_posts;
        $this->params->max_pages   = $this->get_max_pages($wp_query_result->found_posts, $this->params->items_per_page);
        $this->is_valid_request    = $wp_query_result->have_posts();
      }
      else if (isset($this->params->get['termlist']))
      {
        $this->query_args['hide_empty'] = true;

        $wp_term_query_result = $this->request_query('WP_Term_Query');

        if ($wp_term_query_result->terms !== null)
        {
          $this->params->found_items = count($wp_term_query_result->terms);
          $this->query_result        = $wp_term_query_result->terms;
          $this->is_valid_request    = true;
        }
      }
    }
  }


  /**************************************************************************************************************************/


  private function begin_output() : void
  {
    // Show debug info for this request
    //console_log(\Ultrafunk\Plugin\Globals\get_request_params());

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
    \Ultrafunk\Plugin\Globals\set_request_params($this->params);

    $this->begin_output();

    // Load template class file for this request
    require get_template_directory() . PLUGIN_ENV['template_file_path'] . $this->template_file;

    $template_class = PLUGIN_ENV['template_class_path'] . $this->template_class;
    $template = new $template_class($this->params, $this->query_result);
    $template->render();

    $this->end_output();
  }

  private function render_error_response() : void
  {
    global $wp_query;
    $response_params = new stdClass();
    $response_params->response = $this->params->get;
    $response_params->query    = $this->params->query;

    // Setup global $wp_query so it contains relevant data to handle this request failure...
    if (current($this->params->get) === 'search')
    {
      $response_params->error    = ['http_status' => 200, 'details' => 'No search matches'];
      $wp_query->is_search       = true;
      $wp_query->query_vars['s'] = $this->route_request->query_params['s'];
    }
    else
    {
      $response_params->error = ['http_status' => 404, 'details' => 'Page not found'];
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
    if (isset($this->params->get))
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
