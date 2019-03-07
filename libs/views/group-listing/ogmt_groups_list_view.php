<?php
  /**
   * Display generalized object group list
   */
  class groups_list_view
  {
    function __construct()
    {
      $edan = new ogmt_edan_handler();
      $this->url_handler = new url_handler();
      $this->groups = $edan->get_cache()['groups'];
    }

    /**
     * Display vertical list of object groups
     *
     * @return string html string of object group content
     */
    function get_groups()
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
