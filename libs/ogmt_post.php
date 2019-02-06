<?php

  if ( ! function_exists( 'post_exists' ) )
  {
      require_once( ABSPATH . 'wp-admin/includes/post.php' );
  }

  add_action('init', 'ogmt_custom_post_type');
  add_filter('single_template', 'my_custom_template', 20);

  /* Filter the single_template with our custom function*/


  function ogmt_custom_post_type()
  {
    register_post_type('ogmt_call',
                       array(
                           'labels'      => array(
                               'name'          => __('OGMT Calls'),
                               'singular_name' => __('OGMT Call'),
                           ),
                           'public'      => true,
                           'has_archive' => true,
                           'rewrite'     => array( 'slug' => 'ogmt_call' ), // my custom slug
                       )
    );

    add_ogmt_call('generic');
  }

  function add_ogmt_call($call_type)
  {
    console_log($call_type);
    if(post_exists($call_type) == 0)
    {
      // Create post object
      $ogmt_call = array();
      $ogmt_call['post_type']      = 'ogmt_call';
      $ogmt_call['post_title']     = $call_type;
      $ogmt_call['post_content']   = '';
      $ogmt_call['post_status']    = 'publish';
      $ogmt_call['post_author']    = 1;
      $ogmt_call['post_category']  = array(0);
      $ogmt_call['comment_status'] = 'closed';
      // Insert the post into the database
      wp_insert_post( $ogmt_call );
    }
  }

  function my_custom_template($single)
  {
    console_log('my_custom_template fired');
    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'ogmt_call' )
    {
      console_log("post type found");
      if ( file_exists( ABSPATH . 'wp-content/plugins/ogmt-wp/templates/single-ogmt_call.php' ) )
      {
        console_log("file exists. returning template file");
        return ABSPATH . 'wp-content/plugins/ogmt-wp/templates/single-ogmt_call.php';
      }
    }

    console_log("template not found, using default single template");
    return $single;
  }

?>
