<?php declare(strict_types=1);
/*
 * List-player search tracks requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerSearch extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
  }

  protected function parse_validate_set_params() : bool
  {
    if (isset($this->route_request->query_params['s']))
    {
      $this->request_params['get']['search'] = true;
      $this->route_path   = 'list/search';
      $this->title_parts  = ['prefix' => 'Search', 'title' => esc_html($this->route_request->query_params['s'])];
      $this->current_page = $this->get_current_page($this->route_request->path_parts, 3);

      $this->query_args = [
        'suppress_filters' => false,
        's'                => $this->route_request->query_params['s'],
      ];

      return true;
    }

    return false;
  }
}
