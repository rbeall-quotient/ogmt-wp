<?php
  /**
   * Class that serves up object groups list view
   *
   * Featured object groups are displayed first, followed by non-featured
   * object groups.
   */
  class ogmt_groups_list_view
  {
    /**
     * Constructor gathers json from ogmt cache and instantiates new
     * ogmt_url_manager().
     *
     * @param string $ogmt_cache cached edan json responses
     */
    function __construct($ogmt_cache)
    {
      $this->featured = $ogmt_cache['featured'];
      $this->groups = $ogmt_cache['groups'];

      $this->url_handler = new ogmt_url_manager();
    }

    /**
     * Display featured and general object groups
     *
     * @return string html content with object group data
     */
    function content()
    {
      $content  = '<div>';
      $content .= '<div>' . $this->get_featured_view() . '</div>';
      $content .= '<div>' . $this->show_object_groups() . '</div>';
      $content .= '</div>';

      return $content;
    }

    /**
     * Display featured object groups in a horizontal list
     *
     * @return string html string of featured groups content
     */
    function get_featured_view()
    {
      $content  = '';

      if($this->featured)
      {
        $content .= '<h5>Featured Groups</h5>';
        $content .= '<ul style="list-style:none;">';

        foreach($this->featured->{'objectGroups'} as $group)
        {
          $content .= '<li style="display:inline-block; padding: 20px;">';
          $content .= '<a href="' . $this->url_handler->group_url($group->{'url'}). '">';
          $content .= '<img style="height:350px; width:350px;" alt="' . $group->{'feature'}->{'alt'} . '" src="' . $group->{'feature'}->{'url'} . '"/>';
          $content .= '<figcaption>' . $group->{'title'} . '</figcaption>';
          $content .= '</a>';
          $content .= '</li>';
        }
        $content .= '</ul>';
      }

      return $content;
    }

    /**
     * Display vertical list of object groups
     *
     * @return string html string of object group content
     */
    function show_object_groups()
    {
      $content  = '';

      if($this->groups)
      {
        $content .= '<h5>All Object Groups</h5>';
        $content .= '<ul style="list-style:none;">';

        foreach($this->groups->{'objectGroups'} as $group)
        {
          $url = $this->url_handler->group_url($group->{'url'});

          $content .= '<li><span style="display:inline-block;">';
          $content .= '<a href="' . $url . '">';

          if(property_exists($group->{'feature'}, 'media'))
          {
            $content .= $group->{'feature'}->{'media'};
          }
          else
          {
            $content .= '<img src="' . $group->{'feature'}->{'url'} . '"/>';
          }

          $content .= '</a>';

          $content .= '<div><div><a href="' . $url . '">';
          $content .= '<h4>' . $group->{'title'} . '</h4></a></div>';

          if(property_exists($group, 'description'))
          {
              $content .= '<div>' . $group->{'description'} . '</div>';
          }

          $content .= '</div></span></li>';
        }

        $content .= '</ul>';
      }
      
      return $content;
    }
  }
?>
