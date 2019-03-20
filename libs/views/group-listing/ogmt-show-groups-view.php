<?php
  /**
   * Class that serves up object groups list view
   *
   * Featured object groups are displayed first, followed by non-featured
   * object groups.
   */
  class ogmt_show_groups_view
  {
    function __construct($cache)
    {
      $this->cache = $cache;
    }
    /**
     * Display featured and general object groups
     *
     * @return string html content with object group data
     */
    function get_content()
    {
      $featured = new ogmt_featured_view($this->cache);
      $groups   = new ogmt_groups_list_view($this->cache);

      $content  = '<div>';
      $content .= '<div>' . $featured->get_content() . '</div>';
      $content .= '<div>' . $groups->get_content() . '</div>';
      $content .= '</div>';

      return $content;
    }
  }
?>
