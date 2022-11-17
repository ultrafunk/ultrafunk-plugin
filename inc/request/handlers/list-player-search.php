<?php declare(strict_types=1);
/*
 * List-player search tracks requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerSearch extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->request_params['get'] = ['list_player' => 'search'];

    if (isset($this->route_request->query_params['s']))
    {
      $this->route_path     = 'list/search';
      $this->title_parts    = ['prefix' => 'Search', 'title' => esc_html($this->route_request->query_params['s'])];
      $this->items_per_page = \Ultrafunk\Plugin\Globals\get_globals_prop('list_per_page');
      $this->current_page   = $this->get_current_page($this->route_request->path_parts, 3);

      $this->query_args = [
        'suppress_filters' => false,
        's'                => $this->route_request->query_params['s'],
      ];

      return true;
    }

    return false;
  }
}
