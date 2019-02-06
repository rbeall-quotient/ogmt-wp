<?php
  /**
   * Calls to EDAN Object Groups. Retrieve JSON. 
   */

  require 'EDANInterface.php';

  function generic_call($creds, $_service, $objectGroupUrl)
  {
    $config = parse_ini_file('.config.ini', TRUE);
    $_GET   = array();

    if (isset($creds))
    {
      if (empty($creds))
      {
        console_log('Empty creds.' . "\n");
        exit(0);
      }

      if(!isset($config[$creds]))
      {
        console_log('Invalid creds specified. Check your config.' . "\n");
        exit(0);
      }
      else
      {
        $config = $config[ $creds ];
        unset($creds);
      }
    }

    $_GET['_service'] = $_service;
    $_GET['objectGroupUrl'] = $objectGroupUrl;

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
    $results = $edan->sendRequest($uri, $_service, FALSE, $info);

    if (is_array($info))
    {
      if ($info['http_code'] == 200)
      {
        /*if (isset($info['content_type']))
        {
          header('Content-Type: ' . $info['content_type']);
        }*/

        return $results;
        exit;
      }
      else
      {
        console_log('Request failed: HTTP code ' . $info['http_code'] . ' returned' . "\n");
        console_json($results);
        return $results;
        exit(1);
      }
    }
    else
    {
      console_log('Request failed: ' . $info . "\n");
      return "{}";
      exit(1);
    }
  }
?>
