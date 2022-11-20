<?php declare(strict_types=1);
/*
 * Termlist channels requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class TermlistChannels extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->request_params['get'] = ['termlist' => 'channels'];
    $this->request_params['query']['term_type'] = 'channels';
    $this->request_params['query']['term_path'] = 'channel';

    $this->template_file  = 'content-termlist.php';
    $this->template_class = 'Termlist';

    $this->route_path = 'channels';
    $this->query_args = [ 'taxonomy' => 'uf_channel' ];

    return true;
  }
}
