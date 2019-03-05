<?php
/**
 * Handles all sanitization of options_data
 */
  class ogmt_sanitizer
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

      if(array_key_exists('fnames', $options))
      {
        $options['fnames'] = $this->sanitize_replacement_entries($options['fnames']);
      }
      else
      {
        $options['fnames'] = '';
      }

      if(array_key_exists('hfacets', $options))
      {
        $options['hfacets'] = $this->sanitize_single_entry($options['hfacets']);
      }
      else
      {
        $options['hfacets'] = '';
      }

      if(array_key_exists('fields', $options))
      {
        $options['fields'] = $this->sanitize_single_entry($options['fields']);
      }
      else
      {
        $options['fields'] = '';
      }

      if(array_key_exists('labels', $options))
      {
        $options['labels'] = $this->sanitize_replacement_entries($options['labels']);
      }
      else
      {
        $options['labels'] = '';
      }

      if(array_key_exists('mini', $options))
      {
        $options['mini'] = $this->sanitize_single_entry($options['mini']);
      }
      else
      {
        $options['mini'] = '';
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
  }
?>
