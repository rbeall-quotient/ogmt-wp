<?php
  /**
   * Options handler class processes and returns specific options data,
   * formatting it to be useful if need be.
   */
  class options_handler
  {
    /**
     * Constructor takes and saves options array
     *
     * @param array $options options array
     */
    function __construct($options)
    {
      $this->options = $options;
    }

    /**
     * Get site creds
     *
     * @return String site creds
     */
    function get_creds()
    {
      return $this->options['creds'];
    }

    /**
     * Get path to page used for rendering object groups
     *
     * @return String path to object group page
     */
    function get_path()
    {
      return $this->options['path'];
    }

    /**
     * Get title for page listing object groups
     *
     * @return String Title for object group page
     */
    function get_title()
    {
      return $this->options['title'];
    }
  }
?>
