<?php
  /**
   * File for rendering and processing admin menu data for OGMT
   */

   //add admin menu
  add_action( 'admin_menu', 'ogmt_add_menu');

  //register admin menu settings
  add_action( 'admin_init', 'ogmt_register_settings' );

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
    $sanitizer = new sanitizer_handler();
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
        <div class=ogmt-field-label>Creds:</div>
        <div>
          <input type="text" name="ogmt_settings[creds]" value="<?php echo (array_key_exists( 'creds' , $settings)) ? $settings[ 'creds' ] : ''; ?>" />
          <div class="description">Enter creds for the specific repository</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Object Groups Path:</div>
        <div>
          <input type="text" name="ogmt_settings[path]" value="<?php echo (array_key_exists( 'path' , $settings)) ? $settings[ 'path' ] : ''; ?>" />
          <div class="description">The base path for object group pages. If the Pathauto module is installed, those settings may override the base path.</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Object Groups Title:</div>
        <div>
          <input type="text" name="ogmt_settings[title]" value="<?php echo (array_key_exists( 'title' , $settings)) ? $settings[ 'title' ] : ''; ?>" />
          <div class="description">The title used in breadcrumbs and menu.</div>
        </div>
      </fieldset>
      <br/><hr><br/><br/>
      <fieldset>
        <legend class="ogmt-header"><strong>Search Configuration:</strong></legend><br/>
 				<div class=ogmt-field-label>"Search Results" Message:</div>
 				<div>
          <input type="text" name="ogmt_settings[rmessage]" size=50 value="<?php echo (array_key_exists( 'rmessage' , $settings)) ? $settings[ 'rmessage' ] : ''; ?>" />
          <div class="description">The message that's shown when a search returns results. Tokens for @count (number of items), @name (object group name), @page (object group page name)</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Results Per Page:</div>
        <div>
          <input type="text" name="ogmt_settings[rows]" size=3 value="<?php echo (array_key_exists( 'rows' , $settings)) ? $settings[ 'rows' ] : 10; ?>" />
          <div class="description">A number between 1 and 100.</div>
        </div>
      </fieldset>
      <br/><hr><br/><br/>
      <fieldset>
        <legend class="ogmt-header"><strong>Facets Configuration: </strong></legend><br/>
        <div class=ogmt-field-label>Remove facets message:</div>
        <div>
          <input type="text" name="ogmt_settings[remove]" size=50 value="<?php echo (array_key_exists( 'remove' , $settings)) ? $settings[ 'remove' ] : ''; ?>" />
          <div class="description">You can modify the message that gets displayed above the list of currently selected facets.</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Facet Names:</div>
        <div>
          <textarea form ="ogmt-admin" name="ogmt_settings[fnfield]" id="fnfield" cols="100"><?php echo (array_key_exists( 'fnfield' , $settings)) ? $settings[ 'fnfield' ] : ''; ?></textarea>
          <div class="description">Use this box to change the order of facets and replace facet names with different names. Use the facet name and the new name/label for the facet, separated by a pipe character. Enter one facet per line. For example to rename the facet name data_source enter "data_source | Data Source" without the quotes. Notice the pipe "|" character between the name and desired replacement. Replacements are case sensitive. By default, any facets not listed here will be shown at the end of the list. You can explicitly remove facets using the "Facets to Hide" box below.</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Facets To Hide:</div>
        <div>
          <textarea form ="ogmt-admin" name="ogmt_settings[hffield]" id="hffield" cols="100"><?php echo (array_key_exists( 'hffield' , $settings)) ? $settings[ 'hffield' ] : ''; ?></textarea>
          <div class="description">Use this box to indicate any facets which should be hidden. Enter one facet per line, and enter only the facet name such as "data_source" without the quotes.</div>
        </div>
      </fieldset>
      <br/><hr><br/><br/>
      <fieldset>
        <legend class="ogmt-header"><strong>Fields and Labels Configuration: </strong></legend><br/>
        <div class=ogmt-field-label>Field Order:</div>
        <div>
          <textarea form ="ogmt-admin" name="ogmt_settings[ffield]" id="ffield" cols="100"><?php echo (array_key_exists( 'ffield' , $settings)) ? $settings[ 'ffield' ] : ''; ?></textarea>
          <div class="description">Metadata to show in search results. Each field should be on its own line. Leave blank to hide all field, or * to show all fields. If you want to specify a set of fields and then show the remaining add an * as the last line. Examples of topics: creditLine dataSource objectType.</div>
        </div>
        <br/>
        <div class=ogmt-field-label>Label Replacements:</div>
        <div>
          <textarea form ="ogmt-admin" name="ogmt_settings[lfield]" id="labels" cols="100"><?php echo (array_key_exists( 'lfield' , $settings)) ? $settings[ 'lfield' ] : ''; ?></textarea>
          <div class="description">Replace the labels shown with a different label. When making this list, do not list the "facet" name, but the "label." For example the metadata facet, physicalDescription, has a label "Physical Description" -- For this to appear on the object listing as "Phys. Descr." you enter the following line (without quotes) "Physical Description | Phys. Descr." -- notice the pipe "|" character between the label and desired replacement. Replacements are not case sensitive.</div>
        </div>
        <div class=ogmt-field-label>Mini Fields:</div>
        <div>
          <textarea form ="ogmt-admin" name="ogmt_settings[mfield]" id="mini" cols="100"><?php echo (array_key_exists( 'mfield' , $settings)) ? $settings[ 'mfield' ] : ''; ?></textarea>
          <div class="description">Fields listed here will be marked with the mini class. By default this will cause non-mini fields to be hidden and add a "expand" button to each record to show non-mini fields. Each field should be on its own line. Leave blank for all.</div>
        </div>
        <br/>
      </fieldset>
      <br/><hr>
      <div><?php echo submit_button(); ?></div>
   	</form>
   	<?php
   }
?>
