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

  /*** actions ***/
  add_action('init', 'add_tags');

  /*** filters ***/
  add_filter( 'the_content', 'insert_edan_content');
  add_filter('pre_get_document_title', 'set_ogmt_title');
  add_filter( 'the_title', 'set_ogmt_title', 10);

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
  * Callback function for inserting EDAN content into
  * OGMT page
  */
  function insert_edan_content( $content )
  {
    /*Using stripped down url instead of page title because we
    * we are changing the title and this title filter might be called before
    * we access content.
    */
    if(get_url() == "ogmt")
    {
      $objectGroup = get_object_group(get_edan_vars());

      if($objectGroup)
      {
        //Validate that media exists to display
        if(property_exists($objectGroup->{'feature'},'media'))
        {
          $content .= $objectGroup->{'feature'}->{'media'};
        }

        //Validate if page->content is present. Display description instead
        if(property_exists($objectGroup->{'page'},'content'))
        {
          $content .= $objectGroup->{'page'}->{'content'};
        }
        elseif(property_exists($objectGroup, 'description'))
        {
          $content .= $objectGroup->{'description'};
        }
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
    $url = trim(esc_url_raw(add_query_arg([])), '/');
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');

    if ($home_path && strpos($url, $home_path) === 0)
    {
      $url = trim(substr($url, strlen($home_path)), '/');
    }

    return trim(explode('?', $url, 2)[0], '/');
  }

  /**
   * Get array containing query vars from url
   * @return array EDAN query vars
   */
  function get_edan_vars()
  {
    return array(
      "creds" => get_query_var('creds'),
      "_service" => get_query_var('_service'),
      "objectGroupUrl" => get_query_var('objectGroupUrl'),
    );
  }

  /**
   * Modify title to match ObjectGroup information
   *
   * Note: used for both doc title and display title
   *
   * @param String $title title for display
   */
  function set_ogmt_title( $title )
  {
    if(wp_cache_get('ogmt_title') || get_object_group(get_edan_vars()))
    {
      return wp_cache_get('ogmt_title');
    }

    return $title;
  }
?>
