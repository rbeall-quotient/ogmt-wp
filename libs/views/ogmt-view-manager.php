<?php
  /**
   * Class that serves content views for object groups depending on passed query vars
   */
  class ogmt_view_handler
  {
    /**
     * Retrieve the proper view based on passed query vars
     *
     * if jsonDump passed, print JSON to the page.
     *
     * @return string object group content
     */
    function get_content()
    {
      //check if jsonDump set
      if(get_query_var('jsonDump'))
      {
        $view = new ogmt_json_view();
        return $view->display_json();
      }
      else
      {
        $call = new ogmt_cache_handler();
        //if objectGroupUrl set, serve that particular object group
        if(get_query_var('objectGroupUrl'))
        {
          $view = new ogmt_single_group_view($call->get());
        }
        else
        {
          //otherwise, serve list of featured and general object groups
          $view = new ogmt_show_groups_view($call->get());
        }

        //serve up gathered content
        return $view->get_content();
      }
    }
  }
?>
