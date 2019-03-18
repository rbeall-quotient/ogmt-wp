<?php
  /**
   * Initialize query data and variables
   */

  //register ogmt query vars.
  add_action('init', 'ogmt_add_tags');

  /**
  * Callback for adding custom query variables corresponding to
  * EDAN call.
  */
  function ogmt_add_tags()
  {
    //Object Group Url
    add_rewrite_tag('%objectGroupUrl%', '(.*)');

    //Page Url
    add_rewrite_tag('%pageUrl%', '(.*)');

    //Object Groups List Page Index
    add_rewrite_tag('%ogmtStart%', '(.*)');

    //Object Listing Page Index
    add_rewrite_tag('%listStart%', '(.*)');

    //EDAN Query Fqs
    add_rewrite_tag('%edan_fq%', '(.*)');

    //Object EDAN Url
    add_rewrite_tag('%edanUrl%', '(.*)');

    //Display Result JSON Instead
    add_rewrite_tag('%jsonDump%', '(.*)');

    //EDAN search term
    add_rewrite_tag("%edan_q%", '(.*)');
  }

?>
