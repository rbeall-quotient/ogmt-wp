<?php
  /**
   * Class for serving up urls based on query vars
   */
  class ogmt_url_manager
  {
    /**
     * Get the base url of the page
     *
     * @return String ogmt page url
     */
    function get_url()
    {
      $url = trim(esc_url_raw(add_query_arg([])), '/');
      $url = explode('?', $url, 2)[0];

      return $url;
    }

    /**
     * Validate query vars that are non-array values
     *
     * Note: all params besides edan fqs
     * @param  array $q_vars array of query vars
     * @return String query string
     */
    function validate_single_vars($q_vars)
    {
      $query = '?';
      $count  = 0;

      foreach($q_vars as $key => $val)
      {
        if($count > 0)
        {
          $query .= "&";
        }

        if($val)
        {
          $query .= "$key=$val";
        }

        $count++;
      }

      return $query;
    }

    /**
     * Gather edan_fq query vars array and process as query string
     * @param  array  $fqs   array of edan_fq vars
     * @param  boolean $facet name of facet to exclude or false if not necessary
     * @return String  query string of $edan_fq vars
     */
    function validate_fqs($fqs, $facet=false)
    {
      if(!$fqs)
      {
        return "";
      }

      $query = '';

      foreach($fqs as $fq)
      {
        if($fq != $facet)
        {
          $query .= '&edan_fq[]=' . $fq;
        }
      }

      return $query;
    }

    /**
     * Append pageUrl var to query and return url
     *
     * Note: Used for getting links to items in object group menu array
     *
     * @param  String $pageUrl pageUrl of menu item
     * @return String Url for menu item
     */
    function page_url($pageUrl)
    {
      $url = $this->get_url();

      $vars = array(
        'objectGroupUrl' => get_query_var('objectGroupUrl'),
        'pageUrl' => $pageUrl
      );

      $url .= $this->validate_single_vars($vars);

      return $url;
    }

    /**
     * Append the listStart var to the query
     *
     * Note: Used for getting links to specific pages of objects in bottom view
     *
     * @param  String $listStart page number for list of objects
     * @return String Url for list items
     */
    function list_url($listStart)
    {
      $url  = $this->get_url();

      $vars = array(
        'objectGroupUrl' => get_query_var('objectGroupUrl'),
        'pageUrl' => get_query_var('pageUrl'),
        'listStart' => $listStart
      );

      $url .= $this->validate_single_vars($vars);
      $url .= $this->validate_fqs(get_query_var('edan_fq'));

      return $url;
    }

    /**
     * Url for a specific facet filter
     *
     * @param String $key    Name of facet category
     * @param String $filter Name of filter
     * @return String url string with fqs attached
     */
    function add_facet_url($key, $filter)
    {
      $url  = $this->get_url();

      $vars = array(
        'objectGroupUrl' => get_query_var('objectGroupUrl'),
        'pageUrl' => get_query_var('pageUrl')
      );

      $url .= $this->validate_single_vars($vars);
      $url .= $this->validate_fqs(get_query_var('edan_fq'));
      $url .= '&edan_fq[]=' . $key . ':' . $filter;

      return $url;
    }

    /**
     * Remove a specific fqs variable from url
     * @param  String $facet facet to remove
     * @return String url string without passed facet
     */
    function remove_facet_url($facet)
    {
      $url  = $this->get_url();

      $vars = array(
        'objectGroupUrl' => get_query_var('objectGroupUrl'),
        'pageUrl' => get_query_var('pageUrl')
      );

      $url .= $this->validate_single_vars($vars);
      $url .= $this->validate_fqs(get_query_var('edan_fq'), $facet);

      return $url;
    }

    /**
     * Append objectGroupUrl to query and return url string
     *
     * @param  String $group objectGroupUrl
     * @return String url string with added objectGroupUrl
     */
    function group_url($group)
    {
      $url  = $this->get_url();

      $vars = array(
        'objectGroupUrl' => $group
      );

      $url .= $this->validate_single_vars($vars);

      return $url;
    }
  }
?>
