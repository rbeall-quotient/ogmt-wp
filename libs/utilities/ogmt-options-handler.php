<?php
  /**
   * Options handler class processes and returns specific options data,
   * formatting it to be useful if need be.
   */
  class ogmt_options_handler
  {
    /**
     * Constructor for options_handler
     *
     * @param array  $options array of admin settings
     * @param boolean $facets selector for initializing facets arrays
     */
    function __construct()
    {
      $this->options = get_option('ogmt_settings');
      $this->core = new esw_options_handler();
    }

    /**
     * Get path to page used for rendering object groups
     *
     * @return string path to object group page
     */
    function get_path()
    {
      return $this->options['path'];
    }

    /**
     * Get title for page listing object groups
     *
     * @return string Title for object group page
     */
    function get_title()
    {
      return $this->options['title'];
    }

    /**
     * Get number of rows returned for search
     *
     * @return int number of objects to return
     */
    function get_rows()
    {
      return $this->options['rows'];
    }

    /**
     * Get the results message to display above search results with values
     * put in place of tokens
     *
     * @param  int $count Number of items
     * @param  string $name  Name of Object Group
     * @param  string $page  Name of Page
     * @return string Formatted Results Message.
     */
    function get_results_message($count, $name, $page='')
    {
      $message = $this->options['rmessage'];
      $message = str_replace('@count', $count, $message);
      $message = str_replace('@name', $name, $message);
      $message = str_replace('@page', $page, $message);

      return $message;
    }
  }
?>
