<?php declare(strict_types=1);
/*
 * List-player shuffle requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerShuffle extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->params->get = ['list_player' => 'shuffle'];

    // Shift array to fit request-shuffle format = remove the first 'player' url part
    array_shift($this->route_request->path_parts);

    // Load shuffle.php and set params since it will not be done by RouteRequest in this case
    require ULTRAFUNK_PLUGIN_PATH . \Ultrafunk\Plugin\Config\PLUGIN_ENV['handler_file_path'] . 'shuffle.php';
    $shuffle_handler = new \Ultrafunk\Plugin\Request\Handler\Shuffle($this->wp_env, $this->route_request);

    if ($shuffle_handler->has_valid_request_params())
    {
      $shuffle_params = \Ultrafunk\Plugin\Globals\get_request_params();

      $this->params->get['shuffle_type'] = $shuffle_params->type;
      $this->params->get['shuffle_slug'] = $shuffle_params->slug;

      $this->params->items_per_page = \Ultrafunk\Plugin\Globals\get_global('list_per_page');
      $this->params->route_path     = 'list/shuffle/' . $shuffle_params->path;
      $title                        = ($shuffle_params->type === 'all') ? 'All Tracks' : $shuffle_params->slug_name;
      $this->params->title_parts    = ['prefix' => 'Shuffle', 'title' => $title];
      $this->params->current_page   = $this->wp_env->query_vars['paged'];
      $this->wp_query_vars          = $this->wp_env->query_vars;

      return true;
    }

    return false;
  }
}
