<?php

  add_action('admin_menu', 'ogmt_add_menu');
  add_action( 'admin_init', 'ogmt_register_settings' );

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

  function ogmt_register_settings()
  {
    register_setting( 'ogmt_option_group', 'ogmt_settings', 'ogmt_sanitize_values' );
  }

  /**
   * Echo ogmt html to admin plugin settings page with nmah settings
   */
   function ogmt_admin_menu()
   {
   	$settings = get_option( 'ogmt_settings' );
   	?>
    <h3>OGMT Settings</h3>
   	<form method="post" action="options.php">
   		<?php settings_fields( 'ogmt_option_group' ); ?>
   		<table>
   			<tr>
   				<td>Creds:</td>
   				<td>
            <input type="text" name="ogmt_settings[creds]" value="<?php echo (array_key_exists( 'creds' , $settings)) ? $settings[ 'creds' ] : ''; ?>" />
          </td>
   			</tr>
        <tr>
   				<td>Object Groups Path:</td>
   				<td>
            <input type="text" name="ogmt_settings[path]" value="<?php echo (array_key_exists( 'path' , $settings)) ? $settings[ 'path' ] : ''; ?>" />
          </td>
   			</tr>
        <tr>
   				<td>Object Groups Title:</td>
   				<td>
            <input type="text" name="ogmt_settings[title]" value="<?php echo (array_key_exists( 'title' , $settings)) ? $settings[ 'title' ] : ''; ?>" />
          </td>
   			</tr>
   				<td>"Search Results" Message:</td>
   				<td>
            <input type="text" name="ogmt_settings[rmessage]" value="<?php echo (array_key_exists( 'rmessage' , $settings)) ? $settings[ 'rmessage' ] : ''; ?>" />
          </td>
   			</tr>
      </tr>
        <td>Results Per Page:</td>
        <td>
          <input type="text" name="ogmt_settings[rows]" value="<?php echo (array_key_exists( 'rows' , $settings)) ? $settings[ 'rows' ] : 10; ?>" />
        </td>
      </tr>
   			<tr>
   				<td colspan="2"><?php echo submit_button(); ?></td>
   			</tr>
   		</table>
   	</form>
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

  /**
   * Sanitize settings data
   *
   * Note: need to build out later
   *
   * @param  array $settings array of settings for ogmt
   * @return array Array of sanitized data
   */
  function ogmt_sanitize_values($settings)
  {
    $sanitizer = new ogmt_sanitizer();
    return $sanitizer->sanitize($settings);
  }
?>
