<?php declare(strict_types=1);
/*
 * List-player date requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerDate extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->params->get = ['list_player' => 'date'];

    $year  = intval($this->route_request->path_parts[1]);
    $month = intval($this->route_request->path_parts[2]);

    if (checkdate($month, 1, $year))
    {
      $this->params->route_path  = "list/$year/{$this->route_request->path_parts[2]}";
      $this->params->title_parts = ['prefix' => 'Channel', 'title' => date('F Y', mktime(0, 0, 0, $month, 1, $year))];
      $this->query_args = ['date_query' => ['year' => $year, 'month' => $month]];

      return true;
    }

    return false;
  }
}
