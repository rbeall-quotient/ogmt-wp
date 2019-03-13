<?php
  /**
   * Show OGMT JSON
   */
  class json_view
  {
    function __construct()
    {
      $cache_handler = new cache_handler();
      $this->cache = $cache_handler->get();
    }

    /**
     * echo JSON on page and return '' for content.
     *
     * @return string empty page content
     */
    function display_json()
    {
      //iterate through cache and print all JSON objects
      foreach($this->cache as $key => $val)
      {
        if($val != false)
        {
          print_r("<pre>$key: ");
          echo htmlspecialchars(json_encode($val, JSON_PRETTY_PRINT));
          print_r("</pre>");
        }
      }

      //return empty string
      return '';
    }

    /**
     * Get a string of HTML nestling JSON strings in <pre> tags
     *
     * @return string HTML string with processed JSON
     */
    function get_string()
    {
      $json = '';

      //iterate through cache and print all JSON objects
      foreach($this->cache as $key => $val)
      {
        if($val != false)
        {
          $json .= "<pre>$key: ";
          $json .= htmlspecialchars(json_encode($val, JSON_PRETTY_PRINT));
          $json .= "</pre>";
        }
      }

      return $json;
    }
  }
?>
