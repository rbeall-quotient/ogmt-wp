<?php


  require 'EDANInterface.php';

  function proper_parse_str($str)
  {
    # result array
    $arr = array();

    # split on outer delimiter
    $pairs = explode('&', $str);

    # loop through each pair
    foreach ($pairs as $i)
    {
      if (strpos($i, '=') === FALSE)
      {
        $name = $i;
        $value = '';
      }
      else
      {
        # split into name and value
        list($name, $value) = explode('=', $i, 2);
      }

      # if name already exists
      if( isset($arr[$name]) )
      {
        # stick multiple values into an array
        if( is_array($arr[$name]) )
        {
          $arr[$name][] = $value;
        }
        else
        {
          $arr[$name] = array($arr[$name], $value);
        }
      }
      # otherwise, simply stick it in a scalar
      else
      {
        $arr[$name] = $value;
      }
    }

    # return result array
    return $arr;
  }

  function edan_call()
  {
    $_SERVER = array("argv"=>array("generic.php", "creds=nmah", "_service=ogmt/v1.1/ogmt/getObjectGroup.htm", "objectGroupUrl=19th-century-survey-prints"));
    // Get all configs
    $config = parse_ini_file('.config.ini', TRUE);

    // When used through the command line, parse arguments as query parameters
    if (isset($_SERVER["argv"]))
    {
      $_GET = proper_parse_str(implode('&', array_slice($_SERVER["argv"], 1)));
      ksort($_GET);
    }

    // Check to see if we're using non-default creds
    if (isset($_GET['creds']))
    {
      // Fail if creds is empty
      if (empty($_GET['creds']))
      {
        debug_to_console('Empty creds.' . "\n");
        exit(0);
      }

      // Fail if the creds don't exsis
      if (!isset($config[ $_GET['creds'] ]))
      {
        debug_to_console('Invalid creds specified. Check your config.' . "\n");
        exit(0);
      }
      else
      {
        $config = $config[ $_GET['creds'] ];
        unset($_GET['creds']);
      }
    }

    // Set EDAN service to query against
    if (!isset($_GET['_service']))
    {
      debug_to_console('Service missing' . "\n");
      exit(1);
    }

    $service = $_GET['_service'];
    unset($_GET['_service']);

    // Allow dumping the final generated request URL
    $debug = false;
    if (isset($_GET['debug']))
    {
      $debug = true;
      unset($_GET['debug']);
    }

    // Query/search details
    $uri = http_build_query($_GET);
    // Solr doesn't use array syntax; it allows parameters to be passed multiple
    // times. As a workaround, just remove any encoded PHP indexed-array syntax.
    $uri = preg_replace('/%5B[0-9]+%5D=/', '=', $uri);

    if ($debug)
    {
      fwrite(STDERR, $uri . "\n\n");
      fwrite(STDERR, str_replace('&', "\n", urldecode($uri)) . "\n");
      exit(0);
    }

    // Execute
    $edan = new EDANInterface($config['edan_server'], $config['edan_app_id'], $config['edan_auth_key'], $config['edan_tier_type']);

    // Response
    $info = '';
    $results = $edan->sendRequest($uri, $service, FALSE, $info);

    if (is_array($info))
    {
      if ($info['http_code'] == 200)
      {
        if (isset($info['content_type']))
        {
          header('Content-Type: ' . $info['content_type']);
        }
        echo $results;
        //debug_to_console($results);

        exit;
      }
      else
      {
        debug_to_console('Request failed: HTTP code ' . $info['http_code'] . ' returned' . "\n");
        debug_to_console($results);
        exit(1);
      }
    }
    else
    {
      debug_to_console('Request failed: ' . $info . "\n");
      exit(1);
    }
  }
  
?>
