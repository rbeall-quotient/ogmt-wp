<?php
  /**
   * Register OGMT settings link under the plugin listing
   */
  
  //add ogmt admin settings link
  add_filter( 'plugin_action_links_ogmt-wp/ogmt-wp.php', 'add_action_link' );

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
