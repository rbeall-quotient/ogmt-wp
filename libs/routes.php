<?php
  /**
   * This file handles adding custom query variables and mapping those variables
   * to an EDAN call. From there, we insert the data retrieved from the EDAN
   * call into the page content.
   *
   * Note: Because WordPress is a glorified blogging platform, all views are
   * different types of 'posts'. As a result, we cannot map a WordPress url to
   * a callback function that calls an external API (EDAN). Instead, we have to
   * create a page and then modify the content based on custom query variables.
   *
   * for example:
   * http://localhost:8080/ogmt_local/ogmt/?creds=nmah&_service=ogmt/v1.1/ogmt/getObjectGroup.htm&objectGroupUrl=19th-century-survey-prints
   *
   */

  add_action('init', 'add_tags');
  add_action('init', 'get_web_info');
  add_filter( 'the_content', 'insert_edan_content');

  /**
   * Callback for adding custom query variables corresponding to
   * EDAN call.
   */
  function add_tags()
  {
    add_rewrite_tag('%creds%', '(.*)');
    add_rewrite_tag('%_service%', '(.*)');
    add_rewrite_tag('%objectGroupUrl%', '(.*)');
  }

  /**
   * Callback function for inserting EDAN content based on
   * custom query vars added in add_tags. Currently inserts
   * values of query vars into page.
   */
  function insert_edan_content( $content )
  {
    //if url does not match EDAN OGMT path, do not insert content
    if(get_url() == "ogmt")
    {
      //get values of EDAN OGMT query vars
      $creds = get_query_var('creds');
      $_service = get_query_var('_service');
      $objectGroupUrl = get_query_var('objectGroupUrl');

      //validate query vars
      if($creds && $_service && $objectGroupUrl)
      {
        $results = generic_call($creds, $_service, $objectGroupUrl);
        $objectGroup   = json_decode($results);
        if( $objectGroup != null )
        {
          $content .= '<h3>JSON parse successful</h3>';
          $content .= '<p>'.$objectGroup->{'title'}.'</p>';
        }
        else
        {
          $content .= '<h3>JSON parsing failed</h3>';
        }
      }
      else
      {
        //if the vars are invalid, display this on the page
        $content .= '<h3>Invalid Credentials</h3>';
      }
    }

    return $content;
  }



  /**
   * Return url stripped of query vars and '/' and '?' characters.
   * This will correspond to the EDAN object groups call.
   *
   * Adapted from: https://roots.io/routing-wp-requests/
   *
   * @return String page url without query variables
   */
  function get_url()
  {
    // Get current URL path, stripping out slashes on boundaries
    $url = trim(esc_url_raw(add_query_arg([])), '/');
    // Get the path of the home URL, stripping out slashes on boundaries
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
    // If a URL part exists, and the current URL part starts with it...
    if ($home_path && strpos($url, $home_path) === 0)
    {
      // ... just remove the home URL path form the current URL path
      $url = trim(substr($url, strlen($home_path)), '/');
    }

    //trim query vars and forward slashes
    return trim(explode('?', $url, 2)[0], '/');
  }

  function get_web_info()
  {
    console_log("Short URL: ".get_url());
    console_log("Long URL: ".trim(esc_url_raw(add_query_arg([])), '/'));
  }

?>
