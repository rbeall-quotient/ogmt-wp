<?php
  /**
   * Calls to EDAN Object Groups. Retrieve JSON.
   */
  require 'EDANInterface.php';

  /**
   * Retrieve decoded JSON of object group based on passed query vars
   * @param  array $edan_vars EDAN query vars
   */
  function get_object_group($edan_vars)
  {
    //if object group is already cached, return cached value
    if(wp_cache_get('objectGroup'))
    {
      return wp_cache_get('objectGroup');
    }

    //validate query vars
    if(validate_vars($edan_vars))
    {
      $config = parse_ini_file('.config.ini', TRUE);
      $_GET   = array();

      if (isset($edan_vars['creds']))
      {
        if (empty($edan_vars['creds']))
        {
          console_log('Empty creds.' . "\n");
          exit(0);
        }

        if(!isset($config[$edan_vars['creds']]))
        {
          console_log('Invalid creds specified. Check your config.' . "\n");
          exit(0);
        }
        else
        {
          $config = $config[ $edan_vars['creds']];
          unset($edan_vars['creds']);
        }
      }

      $_GET['_service'] = $edan_vars['_service'];
      $_GET['objectGroupUrl'] = $edan_vars['objectGroupUrl'];

      // Query/search details
      $uri = http_build_query($_GET);
      console_log("URI: ".$uri);
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
          $objectGroup = json_decode($results);
          if($objectGroup)
          {
            //if json is successfully retrieved and decoded, cache title and json
            wp_cache_set('objectGroup', $objectGroup);
            wp_cache_set('ogmt_title', $objectGroup->{'title'});
            return $objectGroup;
          }
          else
          {
            //if json fails to decode, return false
            return false;
          }

          exit;
        }
        else
        {
          //if EDAN call fails, return false
          console_log('Request failed: HTTP code ' . $info['http_code'] . ' returned' . "\n");
          return false;
          exit(1);
        }
      }
      else
      {
        //if no response, return false
        console_log('Request failed: ' . $info . "\n");
        return false;
        exit(1);
      }
    }
    else
    {
      //if query vars fail to validate, return false
      return false;
    }
  }

  /**
   * Test if query vars exist
   * @param  array $edan_vars EDAN Query Vars
   * @return boolean  true on validation, false on failure
   */
  function validate_vars($edan_vars)
  {
    if($edan_vars['creds'] && $edan_vars['_service'] && $edan_vars['objectGroupUrl'])
    {
      return true;
    }
    else
    {
      return false;
    }
  }
?>
