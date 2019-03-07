<?php
  /**
   * Show page menu links
   */
  class page_menu_view
  {
    function __construct()
    {
      $this->url_handler = new url_handler();

      $edan = new ogmt_edan_handler();
      $this->group = $edan->get_cache()['objectGroup'];
    }

    /**
     * display menu items for object group
     *
     * @return string return menu items to append to $content variable.
     */
    function get_menu()
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
