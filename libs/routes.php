<?php

  add_action('init', 'add_tags');

  function add_tags()
  {
    add_rewrite_tag('%creds%', '(.*)');
    add_rewrite_tag('%_service%', '(.*)');
    add_rewrite_tag('%objectGroupUrl%', '(.*)');
  }

  add_filter( 'the_content', 'wpse6034_the_content');
  //apply_filters('the_content', 'content', $creds, $_service, $objectGroupUrl);

  function wpse6034_the_content( $content)
  {
    $creds=get_query_var('creds');
    $_service=get_query_var('_service');
    $objectGroupUrl=get_query_var('objectGroupUrl');

    console_log("creds: ".$creds);
    console_log("_service: ".$_service);
    console_log("objectGroupUrl: ".$objectGroupUrl);

    $content .= '<h3>creds:</h1>';
    $content .= '<p>'.$creds.'</p>';
    $content .= '<h3>_service:</h1>';
    $content .= '<p>'.$_service.'</p>';
    $content .= '<h3>objectGroupUrl:</h1>';
    $content .= '<p>'.$objectGroupUrl.'</p>';

    return $content;
  }

?>
