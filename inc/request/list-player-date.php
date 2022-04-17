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
    $year  = intval($this->route_request->path_parts[1]);
    $month = intval($this->route_request->path_parts[2]);

    if (checkdate($month, 1, $year))
    {
      $this->request_params['get']['date'] = true;
      $date_params        = ['year' => $year, 'month' => $month];
      $this->route_path   = "list/$year/$month";
      $this->title_parts  = ['prefix' => 'Channel', 'title' => date('F Y', mktime(0, 0, 0, $month, 1, $year))];
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
