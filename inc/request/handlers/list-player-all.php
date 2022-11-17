<?php declare(strict_types=1);
/*
 * List-player all tracks requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerAll extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->request_params['get'] = ['list_player' => 'all'];
    $this->route_path   = 'list';
    $this->title_parts  = ['prefix' => 'Channel', 'title' => 'All Tracks'];
    $this->current_page = $this->get_current_page($this->route_request->path_parts, 2);

    return true;
  }
}
