<?php

  //Add ogmt page on init if not already installed
  add_action('init', 'ogmt_add_page');

  /**
   * Code required for post_exists() function to work
   */
  if ( ! function_exists( 'post_exists' ) )
  {
      require_once( ABSPATH . 'wp-admin/includes/post.php' );
  }

  /**
   * Function tests if OGMT page is present and if not, creates the
   * OGMT page on init.
   */
  function ogmt_add_page()
  {
    if(!post_exists('OGMT'))
    {
      // Create post object
      $ogmt_post = array();
      $ogmt_post['post_type']      = 'page';
      $ogmt_post['post_title']     = 'OGMT';
      $ogmt_post['post_content']   = '';
      $ogmt_post['post_status']    = 'publish';
      $ogmt_post['post_author']    = 1;
      $ogmt_post['post_category']  = array(0);
      $ogmt_post['comment_status'] = 'closed';
      // Insert the post into the database
      wp_insert_post( $ogmt_post );
    }
  }
?>
