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
    add_rewrite_tag('%objectGroupUrl%', '(.*)');
    add_rewrite_tag('%pageUrl%', '(.*)');
    add_rewrite_tag('%listStart%', '(.*)');
    add_rewrite_tag('%edan_fq%', '(.*)');
    add_rewrite_tag('%jsonDump%', '(.*)');
  }

  /**
  * Callback function for inserting EDAN content into
  * OGMT page
  */
  function ogmt_insert_content( $content )
  {
    //get options from admin menu and plug them into the options handler
    $options = new options_handler(get_option('ogmt_settings'));

    /*Using stripped down url instead of page title because we
    * we are changing the title and this title filter might be called before
    * we access content.
    */
    if(ogmt_name_from_url() == $options->get_path())
    {
      $view_handler = new ogmt_view_handler();
      $content = $view_handler->get_ogmt_content();
    }

    return $content;
  }

  /**
   * Modify title to match ObjectGroup information
   *
   * @param string $title title for display
   */
  function ogmt_set_title( $title )
  {
    $options = new options_handler(get_option('ogmt_settings'));
    $handler = new ogmt_edan_handler();

    /**
     * if in the loop and the title is cached (or if object group is retrieved successfully)
     * modify the page title on display.
     */
    if(in_the_loop() && ogmt_name_from_url() == $options->get_path())
    {
      if(get_query_var('objectGroupUrl'))
      {
        $objectGroup = $handler->get_ogmt_cache()['objectGroup'];

        if($objectGroup)
        {
          $title = $objectGroup->{'title'};

          if(property_exists($objectGroup->{'page'}, 'pageId') && $objectGroup->{'page'}->{'pageId'} != $objectGroup->{'defaultPageId'})
          {
              $title = "<div><h6>" . $title . "</h6>" . $objectGroup->{'page'}->{'title'} . "</div>";
          }
        }
      }
      else
      {
        $title = $options->get_title();
      }
    }

    return $title;
  }

  /**
   * Modify title to match ObjectGroup information
   *
   * Note: used for both doc title and display title
   *
   * @param string $title title for display
   */
  function ogmt_set_doc_title( $title )
  {
    $options = new options_handler(get_option('ogmt_settings'));

    if(ogmt_name_from_url() != $options->get_path())
    {
      return $title;
    }

    $handler = new ogmt_edan_handler();

    if(get_query_var('objectGroupUrl'))
    {
      $objectGroup = $handler->get_ogmt_cache()['objectGroup'];

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
    }
    else
    {
      $title = $options->get_title();
    }

    return $title;
  }
?>
