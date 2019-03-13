<?php
  class object_call
  {
    function __construct()
    {
      $this->edan = new edan_handler();
      $this->service = 'content/v1.1/content/getContent.htm';
    }

    /**
     * Get object JSON Data
     *
     * @return array object json
     */
    function get()
    {
      $results = array();

      //if ogmt data is already cached, return cached value
      if(wp_cache_get('ogmt_cache'))
      {
        $cache = wp_cache_get('ogmt_cache');
        $object = $cache['object'];

        if($object)
        {
          $results['object'] = $object;

          return $results;
        }
      }

      $obj_vars = array(
        'url' => get_query_var('edanUrl'),
      );

      $results['object'] = json_decode($this->edan->edan_call($obj_vars, $this->service));
      $results['featured'] = false;
      $results['groups'] = false;
      $results['objectGroup'] = false;
      $results['searchResults'] = false;

      wp_cache_set('ogmt_cache', $results);

      return $results;
    }
  }
?>
