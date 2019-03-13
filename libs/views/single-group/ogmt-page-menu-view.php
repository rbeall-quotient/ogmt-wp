<?php
  /**
   * Show page menu links
   */
  class page_menu_view
  {
    function __construct($cache)
    {
      $this->url_handler = new url_handler();
      $this->group = $cache['objectGroup'];
    }

    /**
     * display menu items for object group
     *
     * @return string return menu items to append to $content variable.
     */
    function get_content()
    {
      $content  = '';

      if($this->group)
      {
        $content .= '<h3>Contents: </h3>';

        foreach($this->group->menu as $menu)
        {
          $url = $this->url_handler->page_url($menu->url);
          $content .= "<a href=\"$url\">$menu->title</a><br/>";
        }
      }

      return $content;
    }
  }
?>
