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
    $this->params->get = ['termlist' => 'channels'];
    $this->params->query['term_type'] = 'channels';
    $this->params->query['term_path'] = 'channel';

    $this->template_file  = 'content-termlist.php';
    $this->template_class = 'Termlist';

    $this->params->route_path = 'channels';
    $this->query_args = ['taxonomy' => 'uf_channel'];

    return true;
  }
}
