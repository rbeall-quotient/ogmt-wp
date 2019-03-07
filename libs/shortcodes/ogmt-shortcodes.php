<?php
  /**
   * Register OGMT Shortcodes
   */

  //shortcodes associated with object groups lists
  add_shortcode( 'ogmt-groups-list', 'ogmt_groups_list_shortcode' );
  add_shortcode( 'ogmt-featured', 'ogmt_featured_shortcode' );
  add_shortcode( 'ogmt-show-all-groups', 'ogmt_show_groups_shortcode' );

  //shortcodes associated with display of a single object group
  add_shortcode( 'ogmt-facets', 'ogmt_facet_shortcode');
  add_shortcode( 'ogmt-group-content', 'ogmt_group_content_shortcode');
  add_shortcode( 'ogmt-page-menu', 'ogmt_page_menu_shortcode');
  add_shortcode( 'ogmt-search', 'ogmt_search_shortcode');
  add_shortcode( 'ogmt-full-object-group', 'ogmt_single_group_shortcode');

  /**
   * show featured groups as a shortcode
   *
   * @return string featured groups html
   */
  function ogmt_featured_shortcode()
  {
    $featured = new featured_view();
    return $featured->get_featured();
  }

  /**
   * show general groups lists as a shortcode
   *
   * @return string general groups html
   */
  function ogmt_groups_list_shortcode()
  {
    $groups = new groups_list_view();
    return $groups->get_groups();
  }

  /**
   * show both featured and general object groups
   *
   * @return string all object groups html
   */
  function ogmt_show_groups_shortcode()
  {
    $showall = new show_groups_view();
    return $showall->get_content();
  }

  /**
   * show facets menu as a shortcode
   *
   * @return string facets menu html
   */
  function ogmt_facet_shortcode()
  {
    $facet = new facet_view();
    return $facet->show_facets();
  }

  /**
   * show group content (image, title, description) as a shortcode
   *
   * @return string group content html
   */
  function ogmt_group_content_shortcode()
  {
    $content = new group_content_view();
    return $content->get_content();
  }

  /**
   * show page menu as a shortcode
   *
   * @return string page menu html
   */
  function ogmt_page_menu_shortcode()
  {
    $menu = new page_menu_view();
    return $menu->get_menu();
  }

  /**
   * show search content as a shortcode
   *
   * @return string search content html
   */
  function ogmt_search_shortcode()
  {
    $search = new search_view();
    return $search->get_search();
  }

  /**
   * show an entire object group as a shortcode
   *
   * @return string object group html
   */
  function ogmt_single_group_shortcode()
  {
    $single = new single_group_view();
    return $single->get_content();
  }
?>
