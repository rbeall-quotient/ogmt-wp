<?php
  /**
   * Show featured object groups list
   */
  class featured_view
  {
    function __construct()
    {
      $edan = new ogmt_edan_handler();
      $this->url_handler = new url_handler();
      $this->featured = $edan->get_cache()['featured'];
    }

    /**
     * Display featured object groups in a horizontal list
     *
     * @return string html string of featured groups content
     */
    function get_featured()
    {
      $content  = '';

      if($this->featured)
      {
        $content .= '<h5>Featured Groups</h5>';
        $content .= '<ul style="list-style:none;">';

        foreach($this->featured->{'objectGroups'} as $group)
        {
          $content .= '<li style="display:inline-block; padding: 20px;">';
          $content .= '<a href="' . $this->url_handler->group_url($group->{'url'}). '">';
          $content .= '<img style="height:350px; width:350px;" alt="' . $group->{'feature'}->{'alt'} . '" src="' . $group->{'feature'}->{'url'} . '"/>';
          $content .= '<figcaption>' . $group->{'title'} . '</figcaption>';
          $content .= '</a>';
          $content .= '</li>';
        }
        $content .= '</ul>';
      }

      return $content;
    }
  }
?>
