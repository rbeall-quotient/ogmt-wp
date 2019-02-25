<?php

  add_action('admin_menu', 'ogmt_add_menu');

  add_filter( 'plugin_action_links_ogmt-wp/ogmt-wp.php', 'add_action_link' );

  /**
   * Function to add a submenu under "settings" that corresponds to OGMT plugin
   */
  function ogmt_add_menu()
  {
      //add_submenu_page('options-general.php', 'OGMT', 'OGMT Settings', 'manage_options', 'ogmt-settings', 'ogmt_admin_menu');
      add_menu_page('OGMT', 'OGMT Settings', 'manage_options', 'ogmt-settings', 'ogmt_admin_menu');
      add_submenu_page('ogmt-settings', 'OGMT Basic Settings', 'Basic Settings', 'manage_options', 'ogmt-basics', 'ogmt_basics_menu');
  }

  /**
   * Echo ogmt html to admin plugin settings page.
   */
  function ogmt_admin_menu()
  {
    ?>
    	<div class="wrap">
    		<h2>OGMT Settings</h2>
    	</div>
    <?php
  }

  /**
   * Echo ogmt html to admin plugin settings page.
   */
  function ogmt_basics_menu()
  {
    ?>
    	<div class="wrap">
    		<h2>OGMT Basics</h2>
    	</div>
    <?php
  }

  /**
   * Add a settings link on the plugin page for OGMT linking to settings page
   *
   * @param array $links array of action links with new link appended.
   * @return array merged list of links
   */
  function add_action_link( $links )
  {
    $settings_link = array(
      '<a href="' . admin_url( 'admin.php?page=ogmt-settings') . '">' . __('Settings', 'ogmt-settings') . '</a>',
    );

    return array_merge( $links, $settings_link);
  }
?>
