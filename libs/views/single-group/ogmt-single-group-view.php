<?php
  /**
   * Class handling display for one object group
   */
  class single_group_view
  {
    /**
     * Place standard view and menu view into a grid.
     *
     * @return string content to append
     */
    function get_content()
    {
      $group = new group_content_view();
      $facet = new facet_view();
      $menu  = new page_menu_view();
      $search = new search_view();

      $content  = '<div style="width: 100%; overflow: hidden;">';
      $content .= '<div style="width: 65%; float: left;">' . $group->get_content() . '</div>';
      $content .= '<div style="float: right;"><div>' . $menu->get_menu() . '</div><hr/><div>'.$facet->show_facets().'</div></div>';
      $content .= '</div>';

      $content .= $search->get_search();

      return $content;
    }
  }
?>
