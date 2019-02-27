<?php
  /**
   * Class that serves content views for object groups depending on passed query vars
   */
  class ogmt_view_handler
  {
    /**
     * Constructor makes EDAN call and retrieves ogmt cache.
     */
    function __construct()
    {
       $edan = new ogmt_edan_handler();
       $this->cache = $edan->get_ogmt_cache();
    }

    /**
     * Retrieve the proper view based on passed query vars
     *
     * if jsonDump passed, print JSON to the page.
     *
     * @return string object group content
     */
    function get_ogmt_content()
    {
      //check if jsonDump set
      if(get_query_var('jsonDump'))
      {
        //iterate through cache and print all JSON objects
        foreach($this->cache as $key => $val)
        {
          print_r("<pre>$key: ");
          echo htmlspecialchars(json_encode($val, JSON_PRETTY_PRINT));
          print_r("</pre>");
        }

        //return empty string
        return '';
      }
      else
      {
        //if objectGroupUrl set, serve that particular object group
        if(get_query_var('objectGroupUrl'))
        {
          $view = new single_group_view($this->cache);
        }
        else
        {
          //otherwise, serve list of featured and general object groups
          $view = new ogmt_groups_list_view($this->cache);
        }

        //serve up gathered content
        return $view->content();
      }
    }
  }
?>
