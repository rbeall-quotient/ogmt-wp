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
  require 'libs/utilities/ogmt-options-handler.php';
  require 'libs/utilities/ogmt-url-handler.php';
  require 'libs/utilities/ogmt-sanitizer-handler.php';

  //Page
  require 'libs/page/ogmt-content.php';
  require 'libs/page/ogmt-titles.php';
  require 'libs/page/ogmt-query-init.php';

  //EDAN
  require 'libs/edan/ogmt-edan-handler.php';

  //PHP for serving html views for ogmt data
  require 'libs/views/ogmt-view-manager.php';

  //single group views
  require 'libs/views/single-group/ogmt-single-group-view.php';
  require 'libs/views/single-group/ogmt-facet-view.php';
  require 'libs/views/single-group/ogmt-group-content-view.php';
  require 'libs/views/single-group/ogmt-page-menu-view.php';
  require 'libs/views/single-group/ogmt-search-view.php';

  //group listing views
  require 'libs/views/group-listing/ogmt-show-groups-view.php';
  require 'libs/views/group-listing/ogmt-featured-view.php';
  require 'libs/views/group-listing/ogmt_groups_list_view.php';

  //json views
  require 'libs/views/json-views/ogmt-json-view.php';

  //Admin menu PHP
  require 'libs/admin/ogmt-admin-menu.php';
  require 'libs/admin/ogmt-admin-links.php';

  //JS and CSS to include
  require 'libs/scripts/ogmt-include-scripts.php';
