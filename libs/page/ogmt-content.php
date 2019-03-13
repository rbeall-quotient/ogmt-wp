<?php
  /**
   * Filter page content if stripped down URL matches designated ogmt page.
   */

  //Filter page content
  add_filter( 'the_content', 'ogmt_insert_content');

  /**
  * Callback function for inserting EDAN content into
  * OGMT page
  */
  function ogmt_insert_content( $content )
  {
    //get options from admin menu and plug them into the options handler
    $options = new options_handler();

    /*Using stripped down url instead of page title because we
    * we are changing the title and this title filter might be called before
    * we access content.
    */
    if(ogmt_name_from_url() == $options->get_path())
    {
      $view_handler = new ogmt_view_handler();
      $content = $view_handler->get_content();
    }

    return $content;
  }
?>
