<?php
    add_action('admin_init', 'include_admin_scripts');

    function include_admin_scripts()
    {
        //wp_enqueue_script('ogmt', plugin_dir_url(__FILE__) . 'js/ogmt.js');
        wp_enqueue_style('ogmt-admin-styles', plugin_dir_url(__FILE__) . 'css/ogmt-admin.css');
    }
?>
