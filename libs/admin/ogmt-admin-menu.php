<?php
  /**
   * File for rendering and processing admin menu data for OGMT
   */

   //add admin menu
  add_action( 'admin_menu', 'ogmt_add_menu');

  //register admin menu settings
  add_action( 'admin_init', 'ogmt_register_settings' );
  add_action( 'admin_init', 'ogmt_update_options' );

  /**
   * Function to add a submenu under "settings" that corresponds to OGMT plugin
   */
  function ogmt_add_menu()
  {
    add_menu_page('OGMT', 'OGMT Settings', 'manage_options', 'ogmt-settings', 'ogmt_admin_menu');
  }

  /**
   * Register OGMT settings options array
   */
  function ogmt_register_settings()
  {
    register_setting( 'ogmt_option_group', 'ogmt_settings', 'ogmt_sanitize_values' );
  }

  function ogmt_update_options()
  {
    if(!get_option('ogmt_settings'))
    {
      $options = array(
        'path' => '',
        'title' => '',
        'rmessage' => '',
        'rows' => 10
      );

      update_option('ogmt_settings', $options);
    }
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
    $sanitizer = new ogmt_sanitizer_handler();
    return $sanitizer->sanitize($settings);
  }

  /**
   * Echo ogmt html to admin plugin settings page with nmah settings
   */
   function ogmt_admin_menu()
   {
   	$settings = get_option( 'ogmt_settings' );
   	?>
    <h1>OGMT Settings</h1>
    <br/><br>
   	<form method="post" action="options.php" id="ogmt-admin">
   		<?php settings_fields( 'ogmt_option_group' ); ?>
      <fieldset>
        <legend class="ogmt-header"><strong>Object Groups Configuration:</strong></legend><br/>
        <div class=ogmt-field-label>Object Groups Path:</div>
        <div>
          <input type="text" name="ogmt_settings[path]" value="<?php echo $settings[ 'path' ]; ?>" />
          <div class="description">The base path for object group pages. If the Pathauto module is installed, those settings may override the base path.</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Object Groups Title:</div>
        <div>
          <input type="text" name="ogmt_settings[title]" value="<?php echo $settings[ 'title' ]; ?>" />
          <div class="description">The title used in breadcrumbs and menu.</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Object Groups Results Per Page:</div>
        <div>
          <input type="text" name="ogmt_settings[rows]" size=3 value="<?php echo $settings[ 'rows' ]; ?>" />
          <div class="description">A number between 1 and 100.</div>
        </div>
      </fieldset>
      <br/><hr>
      <div><?php echo submit_button(); ?></div>
   	</form>
   	<?php
   }
?>
