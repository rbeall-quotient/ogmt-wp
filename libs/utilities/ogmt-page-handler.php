<?php

  add_action('init', 'ogmt_add_page');

  if ( ! function_exists( 'post_exists' ) )
  {
      require_once( ABSPATH . 'wp-admin/includes/post.php' );
  }

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
