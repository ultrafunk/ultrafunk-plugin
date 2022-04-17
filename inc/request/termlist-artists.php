<?php declare(strict_types=1);
/*
 * Termlist artists requests
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


class TermlistArtists extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public $first_letter  = null;
  public $letters_range = [];
  public $term_type     = 'artists';
  public $term_path     = 'artist';

  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'termlist');
    $this->add_terms_clauses_filter();
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

  protected function parse_validate_set_params() : bool
  {
    $this->request_params['get']['artists'] = true;
    $this->route_path    = 'artists';
    $this->first_letter  = ($this->route_request->matched_route === 'artists')
                             ? 'a'
                             : $this->route_request->path_parts[1][0];
    $this->letters_range = range('a', 'z');

    $this->request_params['data']['first_letter']  = $this->first_letter;
    $this->request_params['data']['letters_range'] = $this->letters_range;

    $this->query_args = [
      'taxonomy'     => 'uf_artist',
      'first_letter' => $this->first_letter,
    ];

    return true;
  }
}
