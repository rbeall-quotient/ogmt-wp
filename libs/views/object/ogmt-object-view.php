<?php
  /**
   * View class for displaying a standalone object on a page
   */
  class object_view
  {
    function __construct()
    {
      $this->options = new options_handler(get_option('ogmt_settings'));

      $edan = new ogmt_edan_handler();
      $cache = $edan->get_cache();

      $this->object = $cache['object'];
    }

    /**
     * Get html for EDAN object
     *
     * @param  object $row row of decoded json data for a particular object
     * @return string html string for object data
     */
    function get_content()
    {
      $content = '';

      if($this->object && property_exists($this->object->{'content'}, 'descriptiveNonRepeating'))
      {
        $content .= '<div class="obj-header">';

        if(property_exists($this->object->{'content'}->{'descriptiveNonRepeating'}, 'online_media'))
        {
          $src = $this->object->{'content'}->{'descriptiveNonRepeating'}->{'online_media'}->{'media'}[0]->{'content'};
          $content .= "<img src=\"$src\" />";
        }

        $content .= '<hr/></div>';
        $content .= $this->get_fields();
      }

      return $content;
    }

    /**
     * Generates and returns HTML for object field labels and values
     *
     * @return string html of object field data
     */
    function get_fields()
    {
      $content = '';

      if(property_exists($this->object->{'content'}, 'freetext'))
      {
        $labels = $this->compile_field_values($this->object->{'content'}->{'freetext'});

        $content .= "<div>";

        foreach($labels as $key => $vals)
        {
            $content .= '<div><strong>'. $this->options->replace_label($key) . '</strong></div>';

            foreach($vals as $txt)
            {
              $content .= '<div>' . $txt . '</div>';
            }

            $content .= '<br/>';
        }

        $content .= "</div>";
      }

      return $content;
    }

    /**
     * compile a two dimensional array that stores all lines offield text as
     * separate array entries under their corresponding label.
     * @return [type] [description]
     */
    function compile_field_values()
    {
      $freetext = $this->object->{'content'}->{'freetext'};

      $display = array();

      foreach($freetext as $key => $val)
      {
        //$display[$key] = array();

        foreach($val as $set)
        {
          if(!array_key_exists($set->{'label'}, $display))
          {
            $display[$set->{'label'}] = array();
          }

          array_push($display[$set->{'label'}], $set->{'content'});
        }
      }

      return $display;
    }
  }
?>
