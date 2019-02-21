<?php
  class ogmt_groups_views
  {
    public $groups;
    public $featured;

    function __construct($ogmt_cache)
    {
      $this->featured = $ogmt_cache['featured'];
      $this->groups = $ogmt_cache['groups'];
    }

    function show_groups()
    {
      $content  = '<div>';
      $content .= '<div>' . $this->get_featured_view() . '</div>';
      $content .= '<div>' . $this->show_object_groups() . '</div>';
      $content .= '</div>';

      return $content;
    }

    function get_featured_view()
    {
      $content  = '<h5>Featured Groups</h5>';
      $content .= '<ul style="list-style:none;">';

      foreach($this->featured->{'objectGroups'} as $group)
      {
        $content .= '<li style="display:inline-block; padding: 20px;">';
        $content .= '<a href="' . $this->get_group_link($group->{'url'}). '">';
        $content .= '<img style="height:350px; width:350px;" alt="' . $group->{'feature'}->{'alt'} . '" src="' . $group->{'feature'}->{'url'} . '"/>';
        $content .= '<figcaption>' . $group->{'title'} . '</figcaption>';
        $content .= '</a>';
        $content .= '</li>';
      }
      $content .= '</ul>';

      return $content;
    }

    function get_group_link($group)
    {
      $url  = trim(esc_url_raw(add_query_arg([])), '/');
      $url  = explode('?', $url, 2)[0];
      $url .= "?creds=nmah&objectGroupUrl=$group";

      return $url;
    }

    function show_object_groups()
    {
      $content  = '<h5>All Object Groups</h5>';
      $content .= '<ul style="list-style:none;">';

      foreach($this->groups->{'objectGroups'} as $group)
      {
        $url = $this->get_group_link($group->{'url'});
        $content .= '<li><span style="display:inline-block;">';
        $content .= '<a href="' . $url . '">';

        if(property_exists($group->{'feature'}, 'media'))
        {
          console_log("media");
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

      return $content;
    }
  }
?>
