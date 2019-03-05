<?php
    add_action('admin_init', 'include_admin_scripts');
    add_action('init', 'include_object_group_scripts');

    function include_admin_scripts()
    {
        //wp_enqueue_script('ogmt', plugin_dir_url(__FILE__) . 'js/ogmt.js');
        wp_enqueue_style('ogmt-admin-styles', plugin_dir_url(__FILE__) . 'css/ogmt-admin.css');
    }

    function include_object_group_scripts()
    {
      $options = new options_handler(get_option('ogmt_settings'));

      if(ogmt_name_from_url() == $options->get_path())
      {
        wp_enqueue_script('ogmt-mini-field.js', plugin_dir_url(__FILE__) . 'js/ogmt-mini-field.js');
        wp_enqueue_style('ogmt-object-display.css', plugin_dir_url(__FILE__) . 'css/ogmt-object-display.css');
      }
    }
?>
