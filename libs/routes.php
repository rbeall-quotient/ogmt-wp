<?php

  add_action('init', 'add_tags');

  function add_tags()
  {
    add_rewrite_tag('%creds%', '(.*)');
    add_rewrite_tag('%_service%', '(.*)');
    add_rewrite_tag('%objectGroupUrl%', '(.*)');
  }

  add_filter( 'the_content', 'insert_edan_content');
  //apply_filters('the_content', 'content', $creds, $_service, $objectGroupUrl);

  function insert_edan_content( $content)
  {
    if(get_current_url() == "ogmt")
    {
      $creds=get_query_var('creds');
      $_service=get_query_var('_service');
      $objectGroupUrl=get_query_var('objectGroupUrl');

      console_log("creds: ".$creds);
      console_log("_service: ".$_service);
      console_log("objectGroupUrl: ".$objectGroupUrl);
      console_log("url: ".get_current_url());

      $content .= '<h3>creds:</h1>';
      $content .= '<p>'.$creds.'</p>';
      $content .= '<h3>_service:</h1>';
      $content .= '<p>'.$_service.'</p>';
      $content .= '<h3>objectGroupUrl:</h1>';
      $content .= '<p>'.$objectGroupUrl.'</p>';
    }

    return $content;
  }

  //adapted from: https://roots.io/routing-wp-requests/
  function get_current_url()
  {
    // Get current URL path, stripping out slashes on boundaries
    $current_url = trim(esc_url_raw(add_query_arg([])), '/');
    // Get the path of the home URL, stripping out slashes on boundaries
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
    // If a URL part exists, and the current URL part starts with it...
    if ($home_path && strpos($current_url, $home_path) === 0)
    {
      // ... just remove the home URL path form the current URL path
      $current_url = trim(substr($current_url, strlen($home_path)), '/');
    }

    $urlParts = explode('?', $current_url, 2);
    $urlPath = trim($urlParts[0], '/');

    return $urlPath;
  }

?>
