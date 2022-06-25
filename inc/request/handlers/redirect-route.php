<?php declare(strict_types=1);
/*
 * Redirect route request handler
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


/**************************************************************************************************************************/


class RedirectRoute extends \Ultrafunk\Plugin\Request\RequestHandler
{
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request, 'redirect_route');
  }

  protected function parse_validate_set_params() : bool
  {
    switch ($this->route_request->matched_route)
    {
      case 'list_channel_soundcloud':
        wp_redirect('/channel/soundcloud/', 302);
        exit;

      case 'list_channel_soundcloud_page':
        wp_redirect("/channel/soundcloud/page/{$this->get_current_page($this->route_request->path_parts, 4)}/", 302);
        exit;

      case 'list_shuffle_channel_soundcloud':
        wp_redirect('/shuffle/channel/soundcloud/', 302);
        exit;

      case 'list_shuffle_channel_soundcloud_page':
        wp_redirect("/shuffle/channel/soundcloud/page/{$this->get_current_page($this->route_request->path_parts, 5)}/", 302);
        exit;
      }

    return false;
  }
}
