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

  public function parse_validate_set_params() : void
  {
    if (checkdate(\intval($this->route_request->path_parts[2]), 1, \intval($this->route_request->path_parts[1])))
    {
      $this->request_params['request_type']['date'] = true;
      $date_params        = ['year' => \intval($this->route_request->path_parts[1]), 'month' => \intval($this->route_request->path_parts[2])];
      $this->route_path   = "list/{$this->route_request->path_parts[1]}/{$this->route_request->path_parts[2]}";
      $this->title_parts  = ['prefix' => 'Channel', 'title' => date('F Y', mktime(0, 0, 0, $date_params['month'], 1, $date_params['year']))];
      $this->current_page = $this->get_current_page($this->route_request->path_parts, 4);
      $this->max_pages    = $this->get_max_pages($this->count_tracks_month_year($date_params), $this->items_per_page);
      
      $this->is_valid_request = ($this->current_page <= $this->max_pages);
      
      $this->query_args = [
        'post_type'      => 'uf_track',
        'paged'          => $this->current_page,
        'posts_per_page' => $this->items_per_page,
        'date_query'     => $date_params,
      ];
    }
  }

  private function count_tracks_month_year(array $date_params) : int
  {
    $query = new \WP_Query([
      'post_type'   => 'uf_track',
      'post_status' => 'publish',
      'date_query'  => $date_params,
      'fields'      => 'ids',
    ]);

    return $query->found_posts;
  }
}
