<?php
  /**
   * Simple functions for testing, debugging, and other simple,
   * modularized tasks
   */

  /**
   * function for logging php strings to browser console for testing and
   * debugging
   * @param  String $content content to be logged
   */
  function console_log($content)
  {
    echo '<script>console.log("'.$content.'");</script>';
  }

  function console_json($json)
  {
    echo '<script>console.log("hello world")</script>';
    echo '<script>console.log(JSON.parse('.$json.'))</script>';
  }

?>
