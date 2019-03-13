<?php
  class groups_list_call
  {
    function __construct()
    {
      $this->options = new options_handler();
      $this->edan = new edan_handler();
      $this->service = '/ogmt/v1.1/ogmt/getObjectGroups.htm';
    }

    function get()
    {
      $results = array();

      //if ogmt data is already cached, return cached value
      if(wp_cache_get('ogmt_cache'))
      {
        $cache = wp_cache_get('ogmt_cache');
        $featured = $cache['featured'];
        $groups = $cache['groups'];

        if($featured && $groups)
        {
          $results['featured'] = $featured;
          $results['groups'] = $groups;

          return $results;
        }
      }

      if(get_query_var('ogmtStart'))
      {
        $start = get_query_var('ogmtStart') * 20;
      }
      else
      {
        $start = 0;
      }

      $group_vars = array(
        'start' => $start,
      );

      $feature_vars = array(
        'featured' => 1,
      );

      $results['featured'] = json_decode($this->edan->edan_call($feature_vars, $this->service));
      $results['groups'] = json_decode($this->edan->edan_call($group_vars, $this->service));
      $results['objectGroup'] = false;
      $results['searchResults'] = false;
      $results['object'] = false;

      wp_cache_set('ogmt_cache', $results);
      return $results;
    }
  }
?>
