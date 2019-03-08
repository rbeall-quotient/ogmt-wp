<?php
/**
 * Handles all sanitization of options_data
 */
  class sanitizer_handler
  {
    /**
     * Primary sanitization function. Extracts data and calls specific
     * sanitizer functions.
     *
     * @param  array $options array of raw options data
     * @return array array of sanitized options data
     */
    function sanitize($options)
    {
      if(array_key_exists('rows', $options))
      {
        $options['rows'] = $this->sanitize_rows($options['rows']);
      }

      if(array_key_exists('fnfield', $options))
      {
        $options['fnfield'] = $this->sanitize_replacement_entries($options['fnfield']);
        $options['fnames'] = $this->initialize_fnames($options['fnfield']);
      }
      else
      {
        $options['fnfield'] = '';
        $options['fnames'] = NULL;
      }

      if(array_key_exists('hffield', $options))
      {
        $options['hffield'] = $this->sanitize_single_entry($options['hffield']);
        $options['hfacets'] = $this->initialize_hfacets($options['hffield']);
      }
      else
      {
        $options['hffield'] = '';
        $options['hfacets'] = NULL;
      }

      if(array_key_exists('ffield', $options))
      {
        $options['ffield'] = $this->sanitize_single_entry($options['ffield']);
        $options['fields'] = $this->initialize_fields($options['ffield']);
      }
      else
      {
        $options['ffield'] = '';
        $options['fields'] = NULL;
      }

      if(array_key_exists('lfield', $options))
      {
        $options['lfield'] = $this->sanitize_replacement_entries($options['lfield']);
        $options['labels'] = $this->initialize_label_replacements($options['lfield']);
      }
      else
      {
        $options['lfield'] = '';
        $options['labels'] = NULL;
      }

      if(array_key_exists('mfield', $options))
      {
        $options['mfield'] = $this->sanitize_single_entry($options['mfield']);
        $options['mini'] = $this->initialize_minis($options['mfield']);
      }
      else
      {
        $options['mfield'] = '';
        $options['mini'] = NULL;
      }

      return $options;
    }

    /**
     * Ensure rows value is numeric and not empty
     *
     * @param  int $rows number of items to return
     * @return int sanitized row values
     */
    function sanitize_rows($rows)
    {
      if(is_numeric($rows))
      {
        if($rows > 100)
        {
          return 100;
        }
        elseif($rows < 1)
        {
          return 1;
        }
        else
        {
          return $rows;
        }
      }

      return 10;
    }

    /**
     * Sanitize facet replacements, removing duplicates and bad data
     * (multiple '|' and the like).
     *
     * @param  string $fnames raw facet replacement data
     * @return string sanitized replacement data
     */
    function sanitize_replacement_entries($entries)
    {
      $pairs = explode("\n", $entries);
      $entries = "";

      $dupes = array();

      for($index = 0; $index < count($pairs); $index++)
      {
        $set = explode('|', $pairs[$index]);

        if(count($set) == 2 && !in_array($set[0], $dupes) && $set[1] != "")
        {
          if($index > 0)
          {
            $entries .= "\n";
          }

          $entries .= trim($set[0]) . '|' . trim($set[1]);
          array_push($dupes, $set[0]);
        }
      }

      return $entries;
    }

    /**
     * Sanitize data containing facets to hide, removing empty lines and spaces.
     *
     * @param  string $hfacets raw data of facets to hide
     * @return string processed facets to hide data string
     */
    function sanitize_single_entry($entries)
    {
      $pairs = explode("\n", $entries);
      $entries = "";

      for($index = 0; $index < count($pairs); $index++)
      {
        if($pairs[$index] != '')
        {
          if($index > 0)
          {
            $entries .= "\n";
          }

          $entries .= $pairs[$index];
        }
      }

      return $entries;
    }

    /**
     * Split facet names data into array where original facet name
     * is the key and the replacement is the value in a series of
     * key:value pairs.
     *
     * @param string fnfield facet replacements fields string
     * @return array facet name pairs or NULL if none exist
     */
    function initialize_fnames($fnfield)
    {
      if(!$fnfield || $fnfield == '')
      {
        return NULL;
      }

      $fnames = array();
      $pairs = explode("\n", $fnfield);

      foreach($pairs as $p)
      {
        $fn = explode('|', $p);

        if(count($fn) > 1)
        {
          $fnames[$fn[0]] = $fn[1];
        }
      }

      return $fnames;
    }

    /**
     * Get each facet to be ignored and place them all in an array.
     *
     * @param string hffield hide facets field string
     * @return array facets to hide or NULL if none exist
     */
    function initialize_hfacets($hffield)
    {
      if(!$hffield || $hffield ==  '')
      {
        return NULL;
      }

      $hfacets = array();

      $pairs = explode("\n", $hffield);

      foreach($pairs as $p)
      {
        array_push($hfacets, trim($p));
      }

      return $hfacets;
    }

    /**
     * Initialize fields array
     *
     * @param string ffield fields field string
     * @return array field order or null if none provided
     */
    function initialize_fields($ffield)
    {
      if(!$ffield || $ffield == '')
      {
        return NULL;
      }

      $fields = array();

      $pairs = explode("\n", $ffield);

      foreach($pairs as $p)
      {
        array_push($fields, trim($p));
      }

      return $fields;
    }

    /**
     * Initialize labels array
     *
     * @param string lfield label field string
     * @return array label name pairs or null if none provided
     */
    function initialize_label_replacements($lfield)
    {
      if(!$lfield || $lfield == '')
      {
        return NULL;
      }

      $labels = array();
      $pairs = explode("\n", $this->options['lfield']);

      foreach($pairs as $p)
      {
        $lr = explode('|', $p);

        if(count($lr) > 1)
        {
          $labels[strtolower($lr[0])] = $lr[1];
        }
      }

      return $labels;
    }

    /**
     * Initialize mini array
     *
     * @param string mfield minis field string
     * @return array mini fields or null if none provided
     */
    function initialize_minis($mfield)
    {
      if(!$mfield || $mfield == '')
      {
        return NULL;
      }

      $mini = array();
      $pairs = explode("\n", $mfield);

      foreach($pairs as $p)
      {
        if($p != '')
        {
          array_push($mini, trim($p));
        }
      }

      return $mini;
    }
  }
?>
