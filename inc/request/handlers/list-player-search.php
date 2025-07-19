<?php declare(strict_types=1);
/*
 * List-player search tracks requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


final class ListPlayerSearch extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->params->get = ['list_player' => 'search'];

    if (isset($this->route_request->query_params['s']))
    {
      $this->params->route_path     = 'list/search';
      $this->params->title_parts    = ['prefix' => 'Search', 'title' => esc_html($this->route_request->query_params['s'])];
      $this->params->items_per_page = \Ultrafunk\Plugin\Globals\get_global('list_per_page');

      $this->wp_query_vars = [
        'suppress_filters' => false,
        's'                => $this->route_request->query_params['s'],
      ];

      return true;
    }

    return false;
  }
}
