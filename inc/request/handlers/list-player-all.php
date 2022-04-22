<?php declare(strict_types=1);
/*
 * List-player all tracks requests
 *
 */


namespace Ultrafunk\Plugin\RequestHandler;


/**************************************************************************************************************************/


class ListPlayerAll extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
  }

  protected function parse_validate_set_params() : bool
  {
    $this->request_params['get']['all'] = true;
    $this->route_path   = 'list';
    $this->title_parts  = ['prefix' => 'Channel', 'title' => 'All Tracks'];
    $this->current_page = $this->get_current_page($this->route_request->path_parts, 2);

    return true;
  }
}
