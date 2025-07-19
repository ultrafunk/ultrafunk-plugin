<?php declare(strict_types=1);
/*
 * List-player all tracks requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


final class ListPlayerAll extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->params->get         = ['list_player' => 'all'];
    $this->params->route_path  = 'list';
    $this->params->title_parts = ['prefix' => 'Channel', 'title' => 'All Tracks'];

    return true;
  }
}
