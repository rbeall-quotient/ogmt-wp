<?php
  /**
   * Show OGMT JSON
   */
  class json_view
  {
    function __construct()
    {
      $edan = new ogmt_edan_handler();
      $this->cache = $edan->get_cache();
    }

    /**
     * echo JSON on page and return '' for content.
     * 
     * @return string empty page content
     */
    function get_json()
    {
      //iterate through cache and print all JSON objects
      foreach($this->cache as $key => $val)
      {
        print_r("<pre>$key: ");
        echo htmlspecialchars(json_encode($val, JSON_PRETTY_PRINT));
        print_r("</pre>");
      }

      //return empty string
      return '';
    }
  }
?>
