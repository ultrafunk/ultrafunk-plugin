<?php declare(strict_types=1);
/*
 * List-player artist & channel requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class ListPlayerArtistChannel extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    switch ($this->route_request->matched_route)
    {
      case 'list_player_artist':
      case 'list_player_artist_page':
        {
          $this->request_params['get'] = ['list_player' => 'artist'];
          $this->request_params['query']['term_type'] = 'artists';

          $taxonomy     = 'uf_artist';
          $title_prefix = 'Artist';

          $this->set_filter_params('channel', 'uf_channel');
        }
        break;

      case 'list_player_channel':
      case 'list_player_channel_page':
        {
          $this->request_params['get'] = ['list_player' => 'channel'];
          $this->request_params['query']['term_type'] = 'channels';

          $taxonomy     = 'uf_channel';
          $title_prefix = 'Channel';
        }
        break;
    }

    $slug    = sanitize_title($this->route_request->path_parts[2]);
    $wp_term = get_term_by('slug', $slug, $taxonomy);

    if (($wp_term !== false) && ($wp_term->count > 0))
    {
      $this->request_params['query']['term_id'] = $wp_term->term_id;

      $this->route_path   = 'list/' . strtolower($title_prefix) . '/' . $slug;
      $this->title_parts  = ['prefix' => $title_prefix, 'title' => $wp_term->name];
      $this->current_page = $this->get_current_page(4);

      $this->query_args = [
        'tax_query' => [
          [
            'taxonomy' => $taxonomy,
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
