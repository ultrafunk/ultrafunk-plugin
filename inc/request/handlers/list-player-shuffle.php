<?php declare(strict_types=1);
/*
 * List-player shuffle requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerShuffle extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
  }

  protected function parse_validate_set_params() : bool
  {
    // Shift array to fit request-shuffle format = remove the first 'player' url part
    array_shift($this->route_request->path_parts);

    // Load shuffle.php and set params since it will not be done by RouteRequest in this case
    require ULTRAFUNK_PLUGIN_PATH . \Ultrafunk\Plugin\Constants\PLUGIN_ENV['handler_file_path'] . 'shuffle.php';
    $shuffle_handler = new \Ultrafunk\Plugin\Request\Handler\Shuffle($this->wp_env, $this->route_request);

    if ($shuffle_handler->parse_validate_set_params())
    {
      $shuffle_params = \Ultrafunk\Plugin\Globals\get_request_params();

      $this->request_params['get']['shuffle']      = true;
      $this->request_params['get']['shuffle_type'] = $shuffle_params['type'];
      $this->request_params['get']['shuffle_slug'] = $shuffle_params['slug'];

      $this->items_per_page = \Ultrafunk\Plugin\Globals\get_globals_prop('list_per_page');
      $this->route_path     = 'list/shuffle/' . $shuffle_params['path'];
      $title                = ($shuffle_params['type'] === 'all') ? 'All Tracks' : $shuffle_params['slug_name'];
      $this->title_parts    = ['prefix' => 'Shuffle', 'title' => $title];
      $this->current_page   = $this->wp_env->query_vars['paged'];
      $this->query_args     = $this->wp_env->query_vars;

      return true;
    }

    return false;
  }
}
