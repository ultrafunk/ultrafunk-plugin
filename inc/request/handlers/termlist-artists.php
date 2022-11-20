<?php declare(strict_types=1);
/*
 * Termlist artists requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class TermlistArtists extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->request_params['get'] = ['termlist' => 'artists'];
    $this->request_params['query']['term_type']     = 'artists';
    $this->request_params['query']['term_path']     = 'artist';
    $this->request_params['query']['letters_range'] = range('a', 'z');
    $this->request_params['query']['first_letter']  = ($this->route_request->matched_route === 'artists') ? 'a' : $this->route_request->path_parts[1][0];

    $this->template_file  = 'content-termlist.php';
    $this->template_class = 'Termlist';

    $this->route_path = 'artists';
    $this->query_args = [
      'taxonomy'     => 'uf_artist',
      'first_letter' => $this->request_params['query']['first_letter'],
    ];

    $this->add_terms_clauses_filter();

    return true;
  }

  private function add_terms_clauses_filter() : void
  {
    add_filter('terms_clauses', function(array $clauses, array $taxonomies, array $args) : array
    {
      if (!isset($args['first_letter']))
        return $clauses;

      global $wpdb;

      $clauses['where'] .= ' AND ' . $wpdb->prepare("t.name LIKE %s", $wpdb->esc_like($args['first_letter']) . '%');

      return $clauses;
    }, 10, 3);
  }
}
