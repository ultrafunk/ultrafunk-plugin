<?php declare(strict_types=1);
/*
 * Termlist channels requests
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


class TermlistChannels extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public $term_type = 'channels';
  public $term_path = 'channel';

  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'termlist');
  }

  protected function parse_validate_set_params() : bool
  {
    $this->request_params['get']['channels'] = true;
    $this->route_path = 'channels';
    $this->query_args = [ 'taxonomy' => 'uf_channel' ];

    return true;
  }
}
