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
    function edan_call($edan_vars)
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

        foreach($edan_vars as $key => $var)
        {
          $_GET[$key] = $var;
        }

        // Query/search details
        $uri = http_build_query($_GET);
        // Solr doesn't use array syntax; it allows parameters to be passed multiple
        // times. As a workaround, just remove any encoded PHP indexed-array syntax.
        $uri = preg_replace('/%5B[0-9]+%5D=/', '=', $uri);
        // Execute
        $edan = new EDANInterface($config['edan_server'], $config['edan_app_id'], $config['edan_auth_key'], $config['edan_tier_type']);

        // Response
        $info = '';
        $results = $edan->sendRequest($uri, $edan_vars['_service'], FALSE, $info);

        if (is_array($info))
        {
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
      //console_log("encoded service: ".urlencode($objService));
      //$searchService = 'metadata%2Fv1.1%2Fmetadata%2FgetObjectLists.htm';

      //if ogmt data is already cached, return cached value
      if(wp_cache_get('ogmt_cache'))
      {
        return wp_cache_get('ogmt_cache');
      }

      $group_vars = $this->get_vars();
      $group_vars['_service'] = $objService;

      $objectGroup = json_decode($this->edan_call($group_vars));

      if($objectGroup)
      {
        $ogmt_cache['objectGroup'] = $objectGroup;

        /*$search_vars = array
        (
          'creds' => get_query_var('creds'),
          '_service' => $searchService,
          'objectGroupId' => $objectGroup->{'objectGroupId'},
        );

        $searchJSON = $this->edan_call($search_vars);

        if($searchJSON)
        {
          $ogmt_cache['searchJSON'] = $searchJSON;
        }
        else
        {
          $ogmt_cache['searchJSON'] = false;
        }*/

        wp_cache_set('ogmt_cache', $ogmt_cache);
        return $ogmt_cache;
        /*if(property_exists($objectGroup, 'objects'))
        {

        }*/
      }

      return false;
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
        $vars[$key] = $value;
      }

      return $vars;
    }
  }
?>
