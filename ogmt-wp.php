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

  //utility functions
  require 'libs/utilities/ogmt-utilities.php';
  require 'libs/utilities/ogmt-page-handler.php';

  //PHP for handling routing to ogmt and calling EDAN
  require 'libs/object_group/ogmt-route-handler.php';
  require 'libs/object_group/ogmt-edan-handler.php';

  //PHP for serving html views for ogmt data
  require 'libs/views/ogmt-url-manager.php';
  require 'libs/views/ogmt-groups-list-view.php';
  require 'libs/views/ogmt-single-group-view.php';
  require 'libs/views/ogmt-view-manager.php';

  //Admin menu PHP
  require 'libs/admin/ogmt-admin-menu.php';

  //JS and CSS to include
  require 'libs/scripts/ogmt-include-scripts.php';
