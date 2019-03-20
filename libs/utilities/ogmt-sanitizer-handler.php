<?php
/**
 * Handles all sanitization of options_data
 */
  class ogmt_sanitizer_handler
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
      if(!array_key_exists('path', $options))
      {
        $options['path'] = '';
      }

      if(!array_key_exists('title', $options))
      {
        $options['title'] = '';
      }

      if(!array_key_exists('rmessage', $options))
      {
        $options['rmessage'] = '';
      }

      if(array_key_exists('rows', $options))
      {
        $options['rows'] = $this->sanitize_rows($options['rows']);
      }
      else
      {
        $options['rows'] = 10;
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
  }
?>
