<?php
  /**
   * should basic object group content (image, title, description)
   */
  class group_content_view
  {
    function __construct($cache)
    {
      $this->group = $cache['objectGroup'];
    }

    /**
     * Display object group media and content
     *
     * @return string media and content to append to $content variable.
     */
    function get_content()
    {
      $content = "";

      if($this->group)
      {
        //Validate that media exists to display
        if(property_exists($this->group->{'feature'},'media'))
        {
          $content .= $this->group->{'feature'}->{'media'};
        }

        //Validate if page->content is present. Display description instead
        if(property_exists($this->group->{'page'},'content'))
        {
          $content .= $this->group->{'page'}->{'content'};
        }
        elseif(property_exists($this->group, 'description'))
        {
          $content .= $this->group->{'description'};
        }
      }

      return $content;
    }
  }
?>
