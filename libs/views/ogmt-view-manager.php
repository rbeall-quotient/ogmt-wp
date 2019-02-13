<?php

  /**
   * Class designed to handle displaying views for object groups. Methods to
   * append html for media and description block as well as menu blocks.
   */
  class ogmt_view_manager
  {
    public $object_group;

    function __construct($object_group)
    {
      $this->object_group = $object_group;
    }

    /**
     * Display object group media and content
     *
     * @return String media and content to append to $content variable.
     */
    function get_standard_view()
    {
      $content = "";

      //Validate that media exists to display
      if(property_exists($this->object_group->{'feature'},'media'))
      {
        $content .= $this->object_group->{'feature'}->{'media'};
      }

      //Validate if page->content is present. Display description instead
      if(property_exists($this->object_group->{'page'},'content'))
      {
        $content .= $this->object_group->{'page'}->{'content'};
      }
      elseif(property_exists($this->object_group, 'description'))
      {
        $content .= $this->object_group->{'description'};
      }

      return $content;
    }

    /**
     * display menu items for object group
     *
     * @return String return menu items to append to $content variable.
     */
    function get_menu_view()
    {
      $content = "<h3>Contents: </h3>";

      foreach($this->object_group->menu as $menu)
      {
        $url = $this->get_menu_url($menu->url);
        $content .= "<a href=\"$url\">$menu->title</a><br/>";
      }

      return $content;
    }

    //get object group url and append the correct menu url
    function get_menu_url($q_var)
    {
      $_service = get_query_var('_service');
      $creds = get_query_var('creds');
      $objectGroupUrl = get_query_var('objectGroupUrl');

      $url = trim(esc_url_raw(add_query_arg([])), '/');
      $url = explode('?', $url, 2)[0];
      $url .= "?creds=$creds&_service=$_service&objectGroupUrl=$objectGroupUrl&pageUrl=$q_var";

      return $url;
    }

    function get_content_grid()
    {
      $content  = '<div style="width: 100%; overflow: hidden;">';
      $content .= '<div style="width: 75%; float: left;">' . $this->get_standard_view() . '</div>';
      $content .= '<div style="float: right;">' . $this->get_menu_view() . '</div>';
      $content .= '</div>';

      return $content;
    }
  }

?>
