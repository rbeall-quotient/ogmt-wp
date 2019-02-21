<?php
  /**
   * Class Handling Calls to EDAN Object Groups. Retrieve JSON.
   */

  require 'edan_core/EDANInterface.php';

  class ogmt_edan_handler
  {
    /**
     * Method to call EDAN API (modified to make a variety of calls based on passed edan_vars)
     * @param  String $edan_vars EDAN query vars
     * @return String            JSON results
     */
    function edan_call($edan_vars, $service, $issearch=false)
    {
        $config = parse_ini_file('.config.ini', TRUE);
        $_GET   = array();

        if (isset($edan_vars['creds']))
        {
          if (empty($edan_vars['creds']))
          {
            console_log('Empty creds');
            exit(0);
          }

          if(!isset($config[$edan_vars['creds']]))
          {
            console_log('Invalid creds specified. Check your config.');
            exit(0);
          }
          else
          {
            $config = $config[ $edan_vars['creds']];
            unset($edan_vars['creds']);
          }
        }

        $uri_string = "";
        $COUNT=0;

        foreach($edan_vars as $key => $var)
        {
          if($COUNT!=0)
          {
            $uri_string .= "&";
          }

          $uri_string .= "$key=$var";

          $_GET[$key] = $var;
          $COUNT++;
        }

        $edan_fqs = get_query_var('edan_fq');

        if($edan_fqs && $issearch)
        {
          $fqs = array();

          foreach($edan_fqs as $fq)
          {
            console_log("fq: $fq");
            $fq = explode(':', $fq, 2);

            array_push($fqs, $fq[0] . ":\"" . str_replace(' ', '+', $fq[1]) . "\"");
          }

          $uri_string .= '&fqs=' . json_encode($fqs);
        }

        //$uri = http_build_query($_GET);
        // Solr doesn't use array syntax; it allows parameters to be passed multiple
        // times. As a workaround, just remove any encoded PHP indexed-array syntax.
        //$uri = preg_replace('/%5B[0-9]+%5D=/', '=', $uri);
        //console_log($uri);
        // Execute
        $edan = new EDANInterface($config['edan_server'], $config['edan_app_id'], $config['edan_auth_key'], $config['edan_tier_type']);

        // Response
        $info = '';
        $results = $edan->sendRequest($uri_string, $service, FALSE, $info);

        if (is_array($info))
        {
          /*print_r('<pre>');
          var_dump($info);
          print_r("</pre>");*/

          if ($info['http_code'] == 200)
          {
            return $results;
            exit;
          }
          else
          {
            //if EDAN call fails, return false
            console_log('Request failed: HTTP code ' . $info['http_code'] . ' returned');
            return false;
            exit(1);
          }
        }
        else
        {
          //if no response, return false
          console_log('Request failed: ' . $info);
          return false;
          exit(1);
        }
    }

    /**
     * Function to retrieve objectGroup.
     * @return array array containing object group json or false on failure
     */
    function get_ogmt_data()
    {
      $ogmt_cache = array();

      $objService = 'ogmt/v1.1/ogmt/getObjectGroup.htm';
      $searchService = 'metadata/v1.1/metadata/getObjectLists.htm';

      //if ogmt data is already cached, return cached value
      if(wp_cache_get('ogmt_cache'))
      {
        return wp_cache_get('ogmt_cache');
      }

      $group_vars = $this->get_vars();

      $objectGroup = json_decode($this->edan_call($group_vars, $objService));

      //if $objectGroup returned, cache it and call getObjectLists
      if($objectGroup)
      {
        $search_vars = array
        (
          'creds' => get_query_var('creds'),
          'objectGroupId' => $objectGroup->{'objectGroupId'},
          'facet' => 'true',//show facets
        );

        //if a pageId is present, add it to the query
        if(property_exists($objectGroup, 'page') && property_exists($objectGroup->{'page'}, 'pageId'))
        {
          $search_vars['pageId'] = $objectGroup->{'page'}->{'pageId'};
        }

        $search_vars['start'] = $this->validate_list_index(get_query_var('listStart'), $objectGroup);

        $searchResults = json_decode($this->edan_call($search_vars, $searchService, 1));
        //add objectGroup to ogmt_cache array
        $ogmt_cache['objectGroup'] = $objectGroup;
        $ogmt_cache['searchResults'] = $searchResults ? $searchResults : false;

        wp_cache_set('ogmt_cache', $ogmt_cache);
        return $ogmt_cache;
      }

      //return false on failure
      return false;
    }

    function get_object_groups()
    {
      //if ogmt data is already cached, return cached value
      if(wp_cache_get('ogmt_cache'))
      {
        return wp_cache_get('ogmt_cache');
      }

      $ogmt_cache = array();
      $service = '/ogmt/v1.1/ogmt/getObjectGroups.htm';

      $group_vars = array(
        'creds' => 'nmah'
      );

      $feature_vars = array(
        'cred' => 'nmah',
        'featured' => 0,
      );

      $ogmt_cache['featured'] = json_decode($this->edan_call($feature_vars, $service));
      $ogmt_cache['groups'] = json_decode($this->edan_call($group_vars, $service));

      wp_cache_set('ogmt_cache', $ogmt_cache);
      return $ogmt_cache;
    }

    /**
     * Get array containing query vars from url
     * @return array EDAN query vars
     */
    function get_vars()
    {
      $vars = array();

      foreach($_GET as $key => $value)
      {
        if(gettype($value) != 'array')
        {
          $vars[$key] = $value;
        }
      }

      return $vars;
    }

    function validate_list_index($index, $objectGroup)
    {
      if($index)
      {
        if(is_numeric($index) && ($index >= 0))
        {
          if(property_exists($objectGroup->{'objects'}, 'size'))
          {
            if($index * 10 < $objectGroup->{'objects'}->{'size'})
            {
              return $index;
            }
          }
        }
      }

      return 0;
    }
  }
?>
