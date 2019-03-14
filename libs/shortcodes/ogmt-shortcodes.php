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

  //shortcode for json
  add_shortcode( 'ogmt-json', 'ogmt_json_shortcode');

  //shortcode for object display
  add_shortcode('ogmt-object', 'ogmt_object_shortcode');

  /**
   * show featured groups as a shortcode
   *
   * @return string featured groups html
   */
  function ogmt_featured_shortcode()
  {
    $call = new groups_list_call();
    $featured = new featured_view($call->get());

    return $featured->get_content();
  }

  /**
   * show general groups lists as a shortcode
   *
   * @return string general groups html
   */
  function ogmt_groups_list_shortcode()
  {
    $call = new groups_list_call();
    $groups = new groups_list_view($call->get());

    return $groups->get_content();
  }

  /**
   * show both featured and general object groups
   *
   * @return string all object groups html
   */
  function ogmt_show_groups_shortcode()
  {
    $call = new groups_list_call();
    $showall = new show_groups_view($call->get());

    return $showall->get_content();
  }

  /**
   * show facets menu as a shortcode
   *
   * @return string facets menu html
   */
  function ogmt_facet_shortcode()
  {
    $call = new object_group_call();
    $facet = new facet_view($call->get()['searchResults']);

    return $facet->get_content();
  }

  /**
   * show group content (image, title, description) as a shortcode
   *
   * @return string group content html
   */
  function ogmt_group_content_shortcode()
  {
    $call = new object_group_call();
    $content = new group_content_view($call->get());

    return $content->get_content();
  }

  /**
   * show page menu as a shortcode
   *
   * @return string page menu html
   */
  function ogmt_page_menu_shortcode()
  {
    $call = new object_group_call();
    $menu = new page_menu_view($call->get());

    return $menu->get_content();
  }

  /**
   * show search content as a shortcode
   *
   * @return string search content html
   */
  function ogmt_search_shortcode()
  {
    $call = new object_group_call();
    $search = new object_list_view($call->get());

    return $search->get_content();
  }

  /**
   * show an entire object group as a shortcode
   *
   * @return string object group html
   */
  function ogmt_single_group_shortcode()
  {
    $call = new object_group_call();
    $single = new single_group_view($call->get());

    return $single->get_content();
  }

  /**
   * show json shortcode
   *
   * @return string json string
   */
  function ogmt_json_shortcode()
  {
    $json = new json_view();
    return $json->get_string();
  }

  /**
   * Show EDAN object content
   *
   * @return string EDAN Object HTML
   */
  function ogmt_object_shortcode()
  {
    $call = new object_call();
    $object = new object_view($call->get());

    return $object->get_content();
  }
?>
