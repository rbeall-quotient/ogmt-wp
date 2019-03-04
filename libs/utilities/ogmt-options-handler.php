<?php
  /**
   * Options handler class processes and returns specific options data,
   * formatting it to be useful if need be.
   */
  class options_handler
  {
    /**
     * Constructor for options_handler
     *
     * @param array  $options array of admin settings
     * @param boolean $facets selector for initializing facets arrays
     */
    function __construct($options)
    {
      $this->options = $options;

      //Set to null to check for intialization
      $this->fnames  = NULL;
      $this->hfacets = NULL;
      $this->fields  = NULL;
      $this->labels  = NULL;
    }

    /**
     * Get site creds
     *
     * @return string site creds
     */
    function get_creds()
    {
      if(!array_key_exists('creds', $this->options))
      {
        return '';
      }

      return $this->options['creds'];
    }

    /**
     * Get path to page used for rendering object groups
     *
     * @return string path to object group page
     */
    function get_path()
    {
      if(!array_key_exists('path', $this->options))
      {
        return '';
      }

      return $this->options['path'];
    }

    /**
     * Get title for page listing object groups
     *
     * @return string Title for object group page
     */
    function get_title()
    {
      if(!array_key_exists('title', $this->options))
      {
        return '';
      }

      return $this->options['title'];
    }

    /**
     * Get message above selected facets
     *
     * @return string message for removal of selected facets
     */
    function get_remove_message()
    {
      if(!array_key_exists('remove', $this->options))
      {
        return '';
      }

      return $this->options['remove'];
    }

    /**
     * Get number of rows returned for search
     *
     * @return int number of objects to return
     */
    function get_rows()
    {
      if(!array_key_exists('rows', $this->options))
      {
        return 10;
      }

      return $this->options['rows'];
    }

    /**
     * Get the results message to display above search results with values
     * put in place of tokens
     *
     * @param  int $count Number of items
     * @param  string $name  Name of Object Group
     * @param  string $page  Name of Page
     * @return string Formatted Results Message.
     */
    function get_results_message($count, $name, $page='')
    {
      if(!array_key_exists('rmessage', $this->options))
      {
        return '';
      }

      $message = $this->options['rmessage'];
      $message = str_replace('@count', $count, $message);
      $message = str_replace('@name', $name, $message);
      $message = str_replace('@page', $page, $message);

      return $message;
    }

    /**
     * If replacement for facet exists, return it for display
     *
     * @param  string $facet original facet name
     * @return string replacement facet name or original if no replacement exists
     */
    function replace_facet($facet)
    {
      if(!$this->fnames || !array_key_exists($facet, $this->fnames))
      {
        return $facet;
      }
      else
      {
        return $this->fnames[$facet];
      }
    }

    /**
     * Check if label has a replacement
     *
     * Note: flatten $label to lowercase (replacements are case insensitive)
     *
     * @param  string $label object field label
     * @return string        replacement label
     */
    function replace_label($label)
    {
      if(!$this->labels || !array_key_exists(strtolower($label), $this->labels))
      {
        return $label;
      }
      else
      {
        return $this->labels[strtolower($label)];
      }
    }

    /**
     * Check if the facet should be ignored, tracking against the list of
     * facets to ignore.
     *
     * @param  string $facet facet value to check
     * @return string        false to ignore facet, true to show it
     */
    function ignore_facet($facet)
    {
      if($this->hfacets && in_array($facet, $this->hfacets))
      {
        return false;
      }
      else
      {
        return true;
      }
    }

    /**
     * Parse through object fields and append to an array
     *
     * @param  object $freetext object of ordered object fields and field values
     * @return array           array of parsed field values
     */
    function get_display_data($freetext)
    {
      $this->initialize_fields();
      $this->initialize_label_replacements();

      $display  = array();

      if(count($this->fields) >= 0 && $this->fields[0] != '')
      {
        $show_all = false;

        if(in_array('*', $this->fields))
        {
          $show_all = true;
        }

        foreach($this->fields as $f)
        {
          if($f != '*' && property_exists($freetext, $f))
          {
            foreach($freetext->{$f} as $set)
            {
              if(!array_key_exists($set->{'label'}, $display))
              {
                $display[$set->label] = array();
              }

              array_push($display[$set->{'label'}], $set->{'content'});
            }

            unset($freetext->{$f});
          }
        }

        if($show_all)
        {
          foreach($freetext as $key => $val)
          {
            foreach($val as $set)
            {
              if(!array_key_exists($set->{'label'}, $display))
              {
                $display[$set->label] = array();
              }

              array_push($display[$set->{'label'}], $set->{'content'});
            }
          }
        }
      }

      return $display;
    }


    /**
     * Initialize both facet arrays for use in view manager
     */
    function initialize_facet_arrays()
    {
      $this->initialize_fnames();
      $this->initialize_hfacets();
    }

    /**
     * Split facet names data into array where original facet name
     * is the key and the replacement is the value in a series of
     * key:value pairs.
     */
    function initialize_fnames()
    {
      if(array_key_exists('fnames', $this->options))
      {
        $this->fnames = array();
        $pairs = explode("\n", $this->options['fnames']);

        foreach($pairs as $p)
        {
          $fn = explode('|', $p);

          if(count($fn) > 1)
          {
            $this->fnames[$fn[0]] = $fn[1];
          }
        }
      }
    }

    /**
     * Get each facet to be ignored and place them all in an array.
     */
    function initialize_hfacets()
    {
      if(array_key_exists('hfacets', $this->options))
      {
        $this->hfacets = array();
        $pairs = explode("\n", $this->options['hfacets']);

        foreach($pairs as $p)
        {
          array_push($this->hfacets, trim($p));
        }
      }
    }

    /**
     * Initialize fields array
     */
    function initialize_fields()
    {
      if(array_key_exists('fields', $this->options))
      {
        $this->fields = array();
        $pairs = explode("\n", $this->options['fields']);

        foreach($pairs as $p)
        {
          array_push($this->fields, trim($p));
        }
      }
    }

    /**
     * Initialize labels array
     */
    function initialize_label_replacements()
    {
      if(array_key_exists('labels', $this->options))
      {
        $this->labels = array();

        $pairs = explode("\n", $this->options['labels']);

        foreach($pairs as $p)
        {
          $lr = explode('|', $p);

          if(count($lr) > 1)
          {
            $this->labels[strtolower($lr[0])] = $lr[1];
          }
        }
      }
    }
  }
?>
