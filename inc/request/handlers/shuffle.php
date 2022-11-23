<?php declare(strict_types=1);
/*
 * Shuffle requests
 *
 */


namespace Ultrafunk\Plugin\Request\Handler;


use Ultrafunk\Plugin\Constants\COOKIE_KEY;

use function Ultrafunk\Plugin\Shared\ {
  console_log,
  get_shuffle_transient_name,
};

use function Ultrafunk\Plugin\Globals\ {
  set_request_params,
  perf_start,
  perf_stop,
};


/**************************************************************************************************************************/


class Shuffle extends \Ultrafunk\Plugin\Request\RequestHandler
{
  private bool $shuffle_all       = false;
  private bool $shuffle_all_page  = false;
  private bool $shuffle_slug      = false;
  private bool $shuffle_slug_page = false;

  public ?object $params = null;

  //
  // Constructor -- Set all private class data / variables
  //
  public function __construct(object $wp_env, object $route_request)
  {
    parent::__construct($wp_env, $route_request);

    $this->params = (object) [
      'is_shuffle' => true,
      'type'       => 'all',
      'slug'       => null,
      'slug_name'  => null,
      'path'       => 'all',
      'page_num'   => 0,
    ];

    switch ($route_request->matched_route)
    {
      case 'shuffle_all':
        $this->shuffle_all = true;
        break;

      case 'shuffle_all_page':
        $this->shuffle_all_page   = true;
        $this->params->page_num = intval($route_request->path_parts[3]);
        break;

      case 'shuffle_slug':
        $this->shuffle_slug   = true;
        $this->params->slug = sanitize_title($route_request->path_parts[2]);
        break;

      case 'shuffle_slug_page':
        $this->shuffle_slug_page  = true;
        $this->params->slug     = sanitize_title($route_request->path_parts[2]);
        $this->params->page_num = intval($route_request->path_parts[4]);
        break;
    }

    $this->params->type = sanitize_title($route_request->path_parts[1]);

    if ($this->shuffle_slug || $this->shuffle_slug_page)
      $this->params->path = ($this->params->type . '/' . $this->params->slug);
  }

  protected function has_valid_request_params() : bool
  {
    $paged = $this->get_page_num(999999);

    if ($paged !== 0)
    {
      $transient = false;

      if (($paged === 1) && isset($_COOKIE[COOKIE_KEY::UF_RESHUFFLE]))
      {
        setcookie(COOKIE_KEY::UF_RESHUFFLE, '', time() - 3600, '/');

        perf_start('create_rnd_transient_start');
        $transient = $this->create_transient();
        perf_stop('create_rnd_transient', 'create_rnd_transient_start');
      }
      else
      {
        perf_start('get_rnd_transient_start');
        $transient = get_transient(get_shuffle_transient_name());

        // We got a stored transient, check if it is the correct one for this request (path match)
        if (($transient !== false) && ($this->params->path !== $transient['shuffle_path']))
          $transient = false;

        perf_stop('get_rnd_transient', 'get_rnd_transient_start');
      }

      if ($transient !== false)
      {
        $this->set_slug_name();
        set_request_params($this->params);

        $this->wp_env->query_vars = [
          'orderby'          => 'post__in',
          'post_type'        => 'uf_track',
          'post__in'         => $transient['post_ids'],
          'paged'            => $paged,
          'suppress_filters' => true,
        ];

        // ToDo: Needs to be updated to work as all the list-nnn.php classes,
        // currently it is a mix of old and new...
        if (!isset($this->params->get))
          $this->is_valid_request = true;

        return true;
      }
    }

    return false;
  }

  //
  // Set unique ID cookie for random shuffle
  //
  private function set_cookie(string $uid) : void
  {
    if (!isset($_COOKIE[COOKIE_KEY::UF_SHUFFLE_UID]))
    {
      $options = [
        'expires'  => (time() + (60 * 60 * 24 * 30)),
        'path'     => '/',
        'secure'   => true,
        'httponly' => false,
        'samesite' => 'Strict',
      ];

      setcookie(COOKIE_KEY::UF_SHUFFLE_UID, $uid, $options);
    }
  }

  //
  // Create get_posts() query args with optional ['tax_query']
  //
  private function get_posts_query_args() : array
  {
    $args = [
      'fields'           => 'ids',
      'post_type'        => 'uf_track',
      'posts_per_page'   => -1,
      'suppress_filters' => true,
    ];

    if ($this->shuffle_slug)
    {
      $args['tax_query'] = [
        [
          'taxonomy' => 'uf_' . $this->params->type,
          'field'    => 'slug',
          'terms'    => $this->params->slug,
        ]
      ];
    }

    return $args;
  }

  //
  // Get term name from slug
  //
  private function set_slug_name() : void
  {
    $wp_term = get_term_by('slug', $this->params->slug, 'uf_' . $this->params->type);

    if ($wp_term !== false)
      $this->params->slug_name = $wp_term->name;
  }

  //
  // Create random shuffle transient for unique Ultrafunk ID
  //
  private function create_transient() : mixed
  {
    $posts_array['shuffle_path'] = $this->params->path;
    $posts_array['post_ids']     = get_posts($this->get_posts_query_args());

    if (!empty($posts_array['post_ids']) && (shuffle($posts_array['post_ids']) === true))
    {
      $transient_name = get_shuffle_transient_name();

      if (empty($transient_name))
      {
        $uid            = uniqid('', true);
        $transient_name = sprintf('random_shuffle_%s', $uid);
        $this->set_cookie($uid);
      }

      delete_transient($transient_name);

      if (set_transient($transient_name, $posts_array, WEEK_IN_SECONDS) === true)
        return $posts_array;
    }

    return false;
  }

  //
  // Get page number for the current shuffle type
  //
  private function get_page_num(int $max_page_num) : int
  {
    $page_num = 0;

    if ($this->shuffle_all || $this->shuffle_slug)
      return 1;

    if ($this->shuffle_all_page || $this->shuffle_slug_page)
      $page_num = $this->params->page_num;

    if (($page_num >= 1) && ($page_num < $max_page_num))
      return $page_num;

    return 0;
  }
}
