<?php declare(strict_types=1);
/*
 * Termlist channels requests
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


class TermlistChannels extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public $taxonomy   = 'uf_channel';
  public $term_type  = 'channels';
  public $term_path  = 'channel';
  public $item_count = 0;

  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'termlist');
  }
  
  public function parse_validate_set_params() : void
  {
    $this->request_params['request_type']['channels'] = true;
    $this->route_path = 'channels';
    $this->item_count = \intval(get_terms(['taxonomy' => $this->taxonomy, 'fields' => 'count']));
    
    $this->is_valid_request = ($this->item_count > 0);
  }
}
