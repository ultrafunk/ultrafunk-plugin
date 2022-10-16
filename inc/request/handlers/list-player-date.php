<?php declare(strict_types=1);
/*
 * List-player date requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerDate extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
  }

  protected function parse_validate_set_params() : bool
  {
    $year  = intval($this->route_request->path_parts[1]);
    $month = intval($this->route_request->path_parts[2]);

    if (checkdate($month, 1, $year))
    {
      $this->request_params['get']['date'] = true;
      $this->route_path   = "list/$year/{$this->route_request->path_parts[2]}";
      $this->title_parts  = ['prefix' => 'Channel', 'title' => date('F Y', mktime(0, 0, 0, $month, 1, $year))];
      $this->current_page = $this->get_current_page($this->route_request->path_parts, 4);
      $this->query_args   = ['date_query' => ['year' => $year, 'month' => $month]];

      return true;
    }

    return false;
  }
}
