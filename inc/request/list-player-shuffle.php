<?php declare(strict_types=1);
/*
 * List-player shuffle requests
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


class ListPlayerShuffle extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
  }

  public function parse_validate_set_params() : void
  {
    // Shift array to fit request-shuffle format = remove the first 'player' url part
    array_shift($this->route_request->path_parts);

    // Load shuffle.php and set params since it will not be done by RouteRequest in this case
    require ULTRAFUNK_PLUGIN_PATH . 'inc/request/shuffle.php';
    $shuffle_handler = new \Ultrafunk\Plugin\Request\Shuffle($this->wp_env, $this->route_request);
    $shuffle_handler->parse_validate_set_params();
    
    if ($shuffle_handler->is_valid_request)
    {
      $shuffle_params = \Ultrafunk\Plugin\Globals\get_request_params();
      
      $this->request_params['request_type']['shuffle'] = true;
      $this->request_params['request_type']['shuffle_type'] = $shuffle_params['type'];
      $this->request_params['request_type']['shuffle_slug'] = $shuffle_params['slug'];
      
      $this->route_path   = 'list/shuffle/' . $shuffle_params['path'];
      $title              = ($shuffle_params['type'] === 'all') ? 'All Tracks' : $shuffle_params['slug_name'];
      $this->title_parts  = ['prefix' => 'Shuffle', 'title' => $title];
      $this->current_page = $this->wp_env->query_vars['paged'];
      $this->max_pages    = $this->get_max_pages(count($this->wp_env->query_vars['post__in']), $this->items_per_page);
      $this->query_args   = $this->wp_env->query_vars;
      $this->query_args  += ['posts_per_page' => $this->items_per_page];

      $this->is_valid_request = ($this->current_page <= $this->max_pages);
    }
  }
}
