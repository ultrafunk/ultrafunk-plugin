<?php declare(strict_types=1);
/*
 * List-player date requests
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


class ListPlayerDate extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
  }

  protected function parse_validate_set_params() : bool
  {
    if (checkdate(\intval($this->route_request->path_parts[2]), 1, \intval($this->route_request->path_parts[1])))
    {
      $this->request_params['get']['date'] = true;
      $date_params        = ['year' => \intval($this->route_request->path_parts[1]), 'month' => \intval($this->route_request->path_parts[2])];
      $this->route_path   = "list/{$this->route_request->path_parts[1]}/{$this->route_request->path_parts[2]}";
      $this->title_parts  = ['prefix' => 'Channel', 'title' => date('F Y', mktime(0, 0, 0, $date_params['month'], 1, $date_params['year']))];
      $this->current_page = $this->get_current_page($this->route_request->path_parts, 4);
      
      $this->query_args = [
        'suppress_filters' => true,
        'date_query'       => $date_params,
      ];

      return true;
    }

    return false;
  }
}
