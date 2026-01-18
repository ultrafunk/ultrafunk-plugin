<?php declare(strict_types=1);
/*
 * DJ-player requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


final class DJPlayer extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    $this->params->get    = ['dj_player'];
    $this->template_file  = 'content-dj-player.php';
    $this->template_class = 'DJPlayer';
    $this->include_header = false;
    $this->include_footer = false;

    return true;
  }
}
