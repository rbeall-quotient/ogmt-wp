<?php
  /**
   * Class that serves up object groups list view
   *
   * Featured object groups are displayed first, followed by non-featured
   * object groups.
   */
  class show_groups_view
  {
    /**
     * Display featured and general object groups
     *
     * @return string html content with object group data
     */
    function get_content()
    {
      $featured = new featured_view();
      $groups   = new groups_list_view();

      $content  = '<div>';
      $content .= '<div>' . $featured->get_featured() . '</div>';
      $content .= '<div>' . $groups->get_content() . '</div>';
      $content .= '</div>';

      return $content;
    }
  }
?>
