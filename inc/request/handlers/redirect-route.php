<?php declare(strict_types=1);
/*
 * Redirect route request handler
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


final class RedirectRoute extends \Ultrafunk\Plugin\Request\RequestHandler
{
  protected function has_valid_request_params() : bool
  {
    switch ($this->route_request->matched_route)
    {
      case 'channel_soundcloud':
        wp_redirect('/channel/soundcloud/', 302);
        exit;

      case 'channel_soundcloud_page':
        wp_redirect("/channel/soundcloud/page/{$this->params->current_page}/", 302);
        exit;

      case 'shuffle_channel_soundcloud':
        wp_redirect('/shuffle/channel/soundcloud/', 302);
        exit;

      case 'shuffle_channel_soundcloud_page':
        wp_redirect("/shuffle/channel/soundcloud/page/{$this->params->current_page}/", 302);
        exit;
    }

    return false;
  }
}
