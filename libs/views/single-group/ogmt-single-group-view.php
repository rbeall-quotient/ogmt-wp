<?php
  /**
   * Class handling display for one object group
   */
  class single_group_view
  {
    function __construct($cache)
    {
      $this->cache = $cache;
    }
    /**
     * Place standard view and menu view into a grid.
     *
     * @return string content to append
     */
    function get_content()
    {
      $group = new group_content_view($this->cache);
      $facet = new facet_view($this->cache['searchResults']);
      $menu  = new page_menu_view($this->cache);
      $search = new object_list_view($this->cache);

      $content  = '<div style="width: 100%; overflow: hidden;">';
      $content .= '<div style="width: 65%; float: left;">' . $group->get_content() . '</div>';
      $content .= '<div style="float: right;"><div>' . $menu->get_content() . '</div><hr/><div>'.$facet->get_content().'</div></div>';
      $content .= '</div>';

      $content .= $search->get_content();

      return $content;
    }
  }
?>
