<?php
  /**
   * Class for serving up urls based on query vars
   */
  class ogmt_url_handler
  {

    function __construct()
    {
      $this->core = new esw_url_handler();
    }

    /**
     * Append pageUrl var to query and return url
     *
     * Note: Used for getting links to items in object group menu array
     *
     * @param  string $pageUrl pageUrl of menu item
     * @return string Url for menu item
     */
    function page_url($pageUrl)
    {
      $url = $this->core->get_url();

      $vars = array(
        'objectGroupUrl' => get_query_var('objectGroupUrl'),
        'pageUrl' => $pageUrl
      );

      $url .= $this->core->validate_single_vars($vars);

      return $url;
    }

    /**
     * Append the listStart var to the query
     *
     * Note: Used for getting links to specific pages of objects in bottom view
     *
     * @param  string $listStart page number for list of objects
     * @return string Url for list items
     */
    function groups_list_url($ogmtStart)
    {
      $url  = $this->core->get_url();

      $vars = array(
        'ogmtStart' => $ogmtStart
      );

      $url .= $this->core->validate_single_vars($vars);

      return $url;
    }

    /**
     * Append objectGroupUrl to query and return url string
     *
     * @param  string $group objectGroupUrl
     * @return string url string with added objectGroupUrl
     */
    function group_url($group)
    {
      $options = new ogmt_options_handler();

      $url = $this->core->get_url();
      $url = str_replace(edan_search_name_from_url(), $options->get_path(), $url);

      $vars = array(
        'objectGroupUrl' => $group
      );

      $url .= $this->core->validate_single_vars($vars);

      return $url;
    }
  }
?>
