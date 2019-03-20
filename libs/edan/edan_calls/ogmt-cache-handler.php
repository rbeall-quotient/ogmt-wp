<?php
  class ogmt_cache_handler
  {
    function get()
    {
      if(get_query_var('objectGroupUrl'))
      {
        $group_call = new ogmt_object_group_call();
        return $group_call->get();
      }
      else
      {
        $list_call = new ogmt_groups_list_call();
        return $list_call->get();
      }
    }
  }
?>
