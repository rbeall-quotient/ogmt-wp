<?php
      add_action('init', 'include_scripts');

    function include_scripts()
    {
      if(ogmt_name_from_url() == 'ogmt')
      {
        console_log(plugin_dir_url(__FILE__));
        wp_enqueue_script('ogmt', plugin_dir_url(__FILE__) . 'js/ogmt.js');
        wp_enqueue_script('ogmt-test', plugin_dir_url(__FILE__) . 'css/ogmt-test.js');
      }
    }
?>
