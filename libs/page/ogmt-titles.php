<?php
  /**
   * Filter the title of the page based on OGMT data.
   */

  //Modify document title
  add_filter('pre_get_document_title', 'ogmt_set_doc_title');

  //Modify page title
  add_filter( 'the_title', 'ogmt_set_title', 10);

  /**
   * Modify title to match ObjectGroup information
   *
   * @param string $title title for display
   */
  function ogmt_set_title( $title )
  {
    $options = new options_handler();
    $cache = new cache_handler();

    /**
     * if in the loop and the title is cached (or if object group is retrieved successfully)
     * modify the page title on display.
     */
    if(in_the_loop() && ogmt_name_from_url() == $options->get_path())
    {
      if(get_query_var('objectGroupUrl'))
      {
        $objectGroup = $cache->get()['objectGroup'];

        if($objectGroup)
        {
          $title = $objectGroup->{'title'};

          if(validate_page_id($objectGroup))
          {
            $pagename = $objectGroup->{'page'}->{'title'};
            $title = "<div><h6>" . $title . "</h6>" . $pagename . "</div>";
          }
        }
      }
      elseif(get_query_var('edanUrl'))
      {
        $object = $cache->get()['object'];

        if($object)
        {
          if(property_exists($object, 'content') && property_exists($object->{'content'}, 'descriptiveNonRepeating'))
          {
            if(property_exists($object->{'content'}->{'descriptiveNonRepeating'}, 'title'))
            {
              $title = $object->{'content'}->{'descriptiveNonRepeating'}->{'title'}->{'content'};
            }
          }
          elseif(property_exists($object, 'title'))
          {
            if(property_exists($object->{'title'}, 'content'))
            {
              $title = $this->object->{'title'}->{'content'};
            }
            else
            {
              $title = $this->object->{'title'};
            }
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
    $options = new options_handler();
    $cache = new cache_handler();

    if(ogmt_name_from_url() == $options->get_path())
    {
      if(get_query_var('objectGroupUrl'))
      {
        $objectGroup = $cache->get()['objectGroup'];

        if($objectGroup)
        {
          $title    = $objectGroup->{'title'};
          $pagename = $objectGroup->{'page'}->{'title'};
          $sitename = get_bloginfo('name');

          //if not on default page, modify the doc title accordingly
          if(validate_page_id($objectGroup))
          {
            //get_bloginfo('name') returns site title.
            $title = $title . " -- " . $pagename . ' | ' . $sitename;
          }
          else
          {
            $title .= ' | ' . $sitename;
          }
        }
      }
      elseif(get_query_var('edanUrl'))
      {
        $object = $cache->get()['object'];

        if($object)
        {
          if(property_exists($object, 'content') && property_exists($object->{'content'}, 'descriptiveNonRepeating'))
          {
            if(property_exists($object->{'content'}->{'descriptiveNonRepeating'}, 'title'))
            {
              $title = $object->{'content'}->{'descriptiveNonRepeating'}->{'title'}->{'content'};
            }
          }
          elseif(property_exists($object, 'title'))
          {
            if(property_exists($object->{'title'}, 'content'))
            {
              $title = $this->object->{'title'}->{'content'};
            }
            else
            {
              $title = $this->object->{'title'};
            }
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
   * Test if page id and default id are present and whether they match.
   *
   * @param  object $grp object group
   * @return boolean     True if IDs exist and are not equal. False otherwise.
   */
  function validate_page_id($grp)
  {
    $id_exists      = property_exists($grp, 'page') && property_exists($grp->{'page'}, 'pageId');
    $default_exists = property_exists($grp, 'defaultPageId');
    $notdefault     = $grp->{'page'}->{'pageId'} != $grp->{'defaultPageId'};

    return $id_exists && $default_exists && $notdefault;
  }

?>
