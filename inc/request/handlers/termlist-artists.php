<?php declare(strict_types=1);
/*
 * Termlist artists requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


final class TermlistArtists extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->params->get = ['termlist' => 'artists'];
    $this->params->query_vars['term_type']     = 'artists';
    $this->params->query_vars['term_path']     = 'artist';
    $this->params->query_vars['letters_range'] = range('a', 'z');
    $this->params->query_vars['first_letter']  = ($this->route_request->matched_route === 'artists') ? 'a' : $this->route_request->path_parts[1][0];

    $this->template_file  = 'content-termlist.php';
    $this->template_class = 'Termlist';

    $this->params->route_path = 'artists';
    $this->wp_query_vars = [
      'taxonomy'     => 'uf_artist',
      'first_letter' => $this->params->query_vars['first_letter'],
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
