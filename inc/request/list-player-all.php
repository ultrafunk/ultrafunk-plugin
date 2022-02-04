<?php declare(strict_types=1);
/*
 * List-player all tracks requests
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


class ListPlayerAll extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
  }

  public function parse_validate_set_params() : void
  {
    $this->request_params['request_type']['all'] = true;
    $this->route_path   = 'list';
    $this->title_parts  = ['prefix' => 'Channel', 'title' => 'All Tracks'];
    $this->current_page = $this->get_current_page($this->route_request->path_parts, 2);
    $this->max_pages    = $this->get_max_pages(\intval(wp_count_posts('uf_track')->publish), $this->items_per_page);

    $this->is_valid_request = ($this->current_page <= $this->max_pages);

    $this->query_args = [
      'post_type'      => 'uf_track',
      'paged'          => $this->current_page,
      'posts_per_page' => $this->items_per_page,
    ];
  }
}
