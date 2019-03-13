<?php
  class object_group_call
  {
    function __construct()
    {
      $this->options = new options_handler();
      $this->edan = new edan_handler();

      $this->groupService = 'ogmt/v1.1/ogmt/getObjectGroup.htm';
      $this->searchService = 'metadata/v1.1/metadata/getObjectLists.htm';
    }

    /**
     * Function to retrieve objectGroup.
     * @return array array containing object group json or false on failure
     */
    function get()
    {
      $results = array();

      //if ogmt data is already cached, return cached value
      if(wp_cache_get('ogmt_cache'))
      {
        $cache = wp_cache_get('ogmt_cache');

        $group = $cache['objectGroup'];
        $search = $cache['searchResults'];

        if($group && $results)
        {
          $results['group'] = $group;
          $results['search'] = $search;

          return $results;
        }
      }

      $group_vars = $this->edan->get_vars();
      $objectGroup = json_decode($this->edan->edan_call($group_vars, $this->groupService));

      //if $objectGroup returned, cache it and call getObjectLists
      if($objectGroup)
      {
        $search_vars = array
        (
          'objectGroupId' => $objectGroup->{'objectGroupId'},
          'rows' => $this->options->get_rows(),//num of rows
          'facet' => 'true',//show facets
        );

        //if a pageId is present, add it to the query
        if(property_exists($objectGroup, 'page') && property_exists($objectGroup->{'page'}, 'pageId'))
        {
          $search_vars['pageId'] = $objectGroup->{'page'}->{'pageId'};
        }

        $search_vars['start'] = $this->edan->validate_list_index(get_query_var('listStart'), $objectGroup);

        $searchResults = json_decode($this->edan->edan_call($search_vars, $this->searchService, 1));
        //add objectGroup to ogmt_cache array
        $results['objectGroup'] = $objectGroup;
        $results['searchResults'] = $searchResults ? $searchResults : false;
        $results['featured'] = false;
        $results['groups'] = false;
        $results['object'] = false;

        wp_cache_set('ogmt_cache', $results);

        return $results;
      }

      //return false on failure
      return false;
    }
  }
?>
