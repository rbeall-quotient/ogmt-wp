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

  require 'libs/utilities/ogmt-utilities.php';
  require 'libs/utilities/ogmt-page-handler.php';
  require 'libs/object_group/ogmt-route-handler.php';
  require 'libs/object_group/ogmt-edan-handler.php';
