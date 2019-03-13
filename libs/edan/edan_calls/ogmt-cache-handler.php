<?php
  class cache_handler
  {
    function get()
    {
      if(get_query_var('objectGroupUrl'))
      {
        $group_call = new object_group_call();
        return $group_call->get();
      }
      elseif(get_query_var('edanUrl'))
      {
        $object_call = new object_call();
        return $object_call->get();
      }
      else
      {
        $list_call = new groups_list_call();
        return $list_call->get();
      }
    }
  }
?>
