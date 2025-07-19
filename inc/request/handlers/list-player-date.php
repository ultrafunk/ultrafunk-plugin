<?php declare(strict_types=1);
/*
 * List-player date requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


final class ListPlayerDate extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->params->get = ['list_player' => 'date'];

    $year      = intval($this->route_request->path_parts[1]);
    $month     = intval($this->route_request->path_parts[2]);
    $month_str = sprintf('%02d', $month);;

    if (checkdate($month, 1, $year))
    {
      $this->params->route_path  = "list/$year/{$month_str}";
      $this->params->title_parts = ['prefix' => 'Channel', 'title' => gmdate('F Y', mktime(0, 0, 0, $month, 1, $year))];
      $this->wp_query_vars       = ['date_query' => ['year' => $year, 'month' => $month]];

      return true;
    }

    return false;
  }
}
