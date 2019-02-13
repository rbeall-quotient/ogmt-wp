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
  add_action('init', 'ogmt_add_tags');

  /*** filters ***/
  add_filter( 'the_content', 'ogmt_insert_content');
  add_filter('pre_get_document_title', 'ogmt_set_doc_title');
  add_filter( 'the_title', 'ogmt_set_title', 10);

  /**
  * Callback for adding custom query variables corresponding to
  * EDAN call.
  */
  function ogmt_add_tags()
  {
    add_rewrite_tag('%creds%', '(.*)');
    add_rewrite_tag('%_service%', '(.*)');
    add_rewrite_tag('%objectGroupUrl%', '(.*)');
    add_rewrite_tag('%pageUrl%', '(.*)');
    add_rewrite_tag('%jsonDump%', '(.*)');
  }

  /**
  * Callback function for inserting EDAN content into
  * OGMT page
  */
  function ogmt_insert_content( $content )
  {
    $handler = new ogmt_edan_handler();

    /*Using stripped down url instead of page title because we
    * we are changing the title and this title filter might be called before
    * we access content.
    */
    if(ogmt_name_from_url() == "ogmt")
    {
      $objectGroup = $handler->get_object_group();

      if($objectGroup)
      {
        if(get_query_var('jsonDump'))
        {
          print_r("<pre>");
          echo htmlspecialchars(json_encode($objectGroup, JSON_PRETTY_PRINT));
          //print_r(json_encode(wp_cache_get('ogmt_json')));
          print_r("</pre>");
        }
        else
        {
          //instantiate view manager and append standard view and menu view to content.
          $view_manager = new ogmt_view_manager($objectGroup);

          //get page content and menu placed in a grid
          $content .= $view_manager->get_content_grid();
        }
      }
    }

    return $content;
  }

  /**
   * Modify title to match ObjectGroup information
   *
   * @param String $title title for display
   */
  function ogmt_set_title( $title )
  {
    $handler = new ogmt_edan_handler();
    $objectGroup = $handler->get_object_group();

    /**
     * if in the loop and the title is cached (or if object group is retrieved successfully)
     * modify the page title on display.
     */
    if(in_the_loop() && $objectGroup)
    {
      //$title = '<div>' . $objectGroup->{'title'};
      $title = $objectGroup->{'title'};

      if(property_exists($objectGroup->{'page'}, 'pageId') && $objectGroup->{'page'}->{'pageId'} != $objectGroup->{'defaultPageId'})
      {
          $title = "<div><h6>" . $title . "</h6>" . $objectGroup->{'page'}->{'title'} . "</div>";
      }
    }

    return $title;
  }

  /**
   * Modify title to match ObjectGroup information
   *
   * Note: used for both doc title and display title
   *
   * @param String $title title for display
   */
  function ogmt_set_doc_title( $title )
  {
    $handler = new ogmt_edan_handler();
    $objectGroup = $handler->get_object_group();

    if($objectGroup)
    {
      $title = $objectGroup->{'title'};
      //if not on default page, modify the doc title accordingly
      if(property_exists($objectGroup->{'page'}, 'pageId') && $objectGroup->{'page'}->{'pageId'} != $objectGroup->{'defaultPageId'})
      {
          //get_bloginfo('name') returns site title.
          $title = $title . " -- " . $objectGroup->{'page'}->{'title'} . ' | ' . get_bloginfo('name');
      }
      else
      {
        $title .= ' | ' . get_bloginfo('name');
      }
    }

    return $title;
  }

  /**
  * Return url stripped of query vars and '/' and '?' characters.
  * This will correspond to the EDAN object groups call.
  *
  * Adapted from: https://roots.io/routing-wp-requests/
  *
  * @return String page url without query variables
  */
  function ogmt_name_from_url()
  {
    $url = trim(esc_url_raw(add_query_arg([])), '/');
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');

    if ($home_path && strpos($url, $home_path) === 0)
    {
      $url = trim(substr($url, strlen($home_path)), '/');
    }

    $url = str_replace('index.php/', '', $url);

    return trim(explode('?', $url, 2)[0], '/');
  }
?>
