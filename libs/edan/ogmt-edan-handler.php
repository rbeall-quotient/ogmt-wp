<?php
  /**
   * Class Handling Calls to EDAN Object Groups. Retrieve JSON.
   */

  require 'edan_core/EDANInterface.php';

  class edan_handler
  {
    /**
     * Method to call EDAN API (modified to make a variety of calls based on passed edan_vars)
     * @param  string $edan_vars EDAN query vars
     * @return string            JSON results
     */
    function edan_call($edan_vars, $service, $issearch=false)
    {
      //$config = parse_ini_file('.config.ini', TRUE);

      //get creds from options_handler
      $options = new options_handler();

      $config = $options->get_config();

      //$edan_vars['creds'] = get_option( 'ogmt_settings' )['creds'];
      $edan_vars['creds'] = $options->get_creds();
      $_GET   = array();

      if (isset($edan_vars['creds']))
      {
        if (empty($edan_vars['creds']))
        {
          console_error('Empty creds');
          return false;
        }

        if(!isset($config[$edan_vars['creds']]))
        {
          console_error('Invalid creds specified. Check your config.');
          return false;
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
          $fq = explode(':', $fq, 2);

          array_push($fqs, $fq[0] . ":\"" . str_replace(' ', '+', $fq[1]) . "\"");
        }

        $uri_string .= '&fqs=' . json_encode($fqs);
      }

      // Execute
      $edan = new EDANInterface($config['edan_server'], $config['edan_app_id'], $config['edan_auth_key'], $config['edan_tier_type']);

      // Response
      $info = '';
      $results = $edan->sendRequest($uri_string, $service, FALSE, $info);

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
          console_error('Request failed: HTTP code ' . $info['http_code'] . ' returned');
          return false;
        }
      }
      else
      {
        //if no response, return false
        console_error('Request failed: ' . $info);
        return false;
      }
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
