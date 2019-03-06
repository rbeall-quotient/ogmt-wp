<?php
  /**
   * Inlcude JavaScript and CSS
   */

  add_action('admin_init', 'include_admin_scripts');
  add_action('init', 'include_object_group_scripts');

  /**
   * CSS and JavaScript for Admin Menu
   */
  function include_admin_scripts()
  {
    wp_enqueue_style('ogmt-admin-styles', plugin_dir_url(__FILE__) . 'css/ogmt-admin.css');
  }

  /**
   * CSS and JavaScript for Object Group Display
   */
  function include_object_group_scripts()
  {
    //option handler to get ogmt page path
    $options = new options_handler(get_option('ogmt_settings'));

    //if on ogmt page, include scripts and styles
    if(ogmt_name_from_url() == $options->get_path())
    {
      /*scripts*/
      wp_enqueue_script('ogmt-mini-field.js', plugin_dir_url(__FILE__) . 'js/ogmt-mini-field.js');//mini field javascript
      wp_enqueue_script('ogmt-facets-list.js', plugin_dir_url(__FILE__) . 'js/ogmt-facets-list.js');//facet list javascript

      /*styles*/
      wp_enqueue_style('ogmt-object-display.css', plugin_dir_url(__FILE__) . 'css/ogmt-object-display.css');//css for object display
      wp_enqueue_style('ogmt-navbar.css', plugin_dir_url(__FILE__) . 'css/ogmt-navbar.css');//css for object navbar
    }
  }
?>
