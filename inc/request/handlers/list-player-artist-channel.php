<?php declare(strict_types=1);
/*
 * List-player artist & channel requests
 *
 */


namespace Ultrafunk\Plugin\RequestHandler;


/**************************************************************************************************************************/


class ListPlayerArtistChannel extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public $taxonomy     = null;
  public $term_type    = null;
  public $title_prefix = null;
  public $wp_term      = null;

  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'list_player');
    
    switch ($route_request->matched_route)
    {
      case 'list_player_artist':
      case 'list_player_artist_page':
        $this->request_params['get']['artist'] = true;
        $this->taxonomy     = 'uf_artist';
        $this->term_type    = 'artists';
        $this->title_prefix = 'Artist';
        break;

      case 'list_player_channel':
      case 'list_player_channel_page':
        $this->request_params['get']['channel'] = true;
        $this->taxonomy     = 'uf_channel';
        $this->term_type    = 'channels';
        $this->title_prefix = 'Channel';
        break;
    }
  }

  protected function parse_validate_set_params() : bool
  {
    $slug          = sanitize_title($this->route_request->path_parts[2]);
    $this->wp_term = get_term_by('slug', $slug, $this->taxonomy);

    if (($this->wp_term !== false) && ($this->wp_term->count > 0))
    {
      $this->route_path   = 'list/' . strtolower($this->title_prefix) . '/' . $slug;
      $this->title_parts  = ['prefix' => $this->title_prefix, 'title' => $this->wp_term->name];
      $this->current_page = $this->get_current_page($this->route_request->path_parts, 4);
      $this->request_params['data']['wp_term'] = $this->wp_term;

      $this->query_args = [
        'tax_query' => [
          [
            'taxonomy' => $this->taxonomy,
            'field'    => 'slug',
            'terms'    => $slug,
          ],
        ],
      ];

      return true;
    }

    return false;
  }
}
