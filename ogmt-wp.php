<?php /*
  Plugin Name: OGMT
  Plugin URI:  https://github.com/rbeall-quotient/ogmt-wp
  Description: EDAN Object Groups WordPress Integration
  Version:     1.0
  Author:      Robert Beall
  Author URI:  https://github.com/rbeall-quotient
  License:     GPL3
  License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
  */

  date_default_timezone_set('UTC');

  add_action( 'admin_notices', 'ogmt_error_notice' );

  function ogmt_error_notice()
  {
    if(!class_exists( 'edan_handler' ))
    {
      ?>
      <div class="error notice">
          <p><?php _e( 'Required Plugin "edan-search-wp" Not Installed. Please Install Required Plugin', 'ogmt_textdomain' ); ?></p>
      </div>
      <?php
    }
  }

  /**
  * Checks if the system requirements are met
  *
  * @return bool True if system requirements are met, false if not
  */
  function ogmt_requirements_met ()
  {
    global $wp_version ;
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' ) ;  // to get is_plugin_active() early

    if ( ! is_plugin_active ( 'edan-search-wp/edan-search-wp.php' ) )
    {
      return false;
    }

    return true ;
  }

  if ( ogmt_requirements_met() )
  {
    //utility functions
    require 'libs/utilities/ogmt-options-handler.php';
    require 'libs/utilities/ogmt-url-handler.php';
    require 'libs/utilities/ogmt-sanitizer-handler.php';

    //Page
    require 'libs/page/ogmt-content.php';
    require 'libs/page/ogmt-titles.php';
    require 'libs/page/ogmt-query-init.php';

    //EDAN
    //require 'libs/edan/ogmt-edan-handler.php';
    require 'libs/edan/edan_calls/ogmt-object-group-call.php';
    require 'libs/edan/edan_calls/ogmt-groups-list-call.php';
    require 'libs/edan/edan_calls/ogmt-cache-handler.php';

    //PHP for serving html views for ogmt data
    require 'libs/views/ogmt-view-manager.php';

    //single group views
    require 'libs/views/single-group/ogmt-single-group-view.php';
    //require 'libs/views/single-group/ogmt-facet-view.php';
    require 'libs/views/single-group/ogmt-group-content-view.php';
    require 'libs/views/single-group/ogmt-page-menu-view.php';
    require 'libs/views/single-group/ogmt-object-list-view.php';

    //group listing views
    require 'libs/views/group-listing/ogmt-show-groups-view.php';
    require 'libs/views/group-listing/ogmt-featured-view.php';
    require 'libs/views/group-listing/ogmt_groups_list_view.php';

    //require 'libs/views/object/ogmt-object-view.php';

    //json views
    require 'libs/views/json-views/ogmt-json-view.php';

    //Admin menu PHP
    require 'libs/admin/ogmt-admin-menu.php';
    require 'libs/admin/ogmt-admin-links.php';

    //shortcodes
    require 'libs/shortcodes/ogmt-shortcodes.php';
  }
  else
  {
    add_action( 'admin_notices', 'ogmt_error_notice' );
  }
