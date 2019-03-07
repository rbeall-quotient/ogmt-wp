<?php
  /**
   * Show search results and navigation
   */
  class search_view
  {
    function __construct()
    {
      $this->url_handler = new url_handler();
      $this->options = new options_handler(get_option('ogmt_settings'));

      $edan = new ogmt_edan_handler();
      $results = $edan->get_cache();

      $this->group = $results['objectGroup'];
      $this->search = $results['searchResults'];

      if($this->search)
      {
        console_log("TRue");
      }
      else{console_log("FAlse");}
    }

    /**
     * Function for generating object list search html
     *
     * @return string HTML string for the object list information
     */
    function get_search()
    {
      $content = '';

      if($this->search && $this->search->{'numFound'} > 0)
      {
        $name = $this->group->{'title'};
        $page  = property_exists($this->group->{'page'}, 'title') ? $this->group->{'page'}->{'title'} : '';
        $count  = $this->search->{'numFound'};

        $info = $this->obj_page_info();

        $content   .= '<div>' . $this->options->get_results_message($count, $name, $page) . '</div>';
        $content  .= '<div>Page ' . $info['current'] . ' ' . ($info['total'] ? 'of ' . $info['total'] : '') . '</div>';

        $content .= $this->get_top_nav();
        $content .= $this->get_object_list();
        $content .= $this->get_bottom_nav();
      }

      return $content;
    }

    /**
     * Display nav links above objects list
     *
     * @param  string $info current and total page numbers
     * @return string html string of nav links
     */
    function get_top_nav()
    {
      $info = $this->obj_page_info();
      $navbar = array();

      $firstprev = $info['current'] != 1; //display "first" and "preview" links
      $nextlast  = $info['current'] != $info['total']; //display "next" and "last" links
      $expandall = $this->options->is_minimized(); //whether to add an "Expand All" link

      if($firstprev)
      {
        array_push($navbar, '<a href='.$this->url_handler->list_url(0).'>First</a>');
        array_push($navbar, '<a href='.$this->url_handler->list_url($info['current']-2).'>Previous</a>');
      }

      if($nextlast)
      {
        array_push($navbar, '<a href='.$this->url_handler->list_url($info['current']).'>Next</a>');
        array_push($navbar, '<a href='.$this->url_handler->list_url($info['total']-1).'>Last</a>');
      }

      if($expandall)
      {
        array_push($navbar, '<a href="#/" onclick="toggle_all()" id="ogmt-expandall">Expand All</a>');
      }

      $content = '<ul class="ogmt-navbar">';

      foreach($navbar as $item)
      {
        $content .= '<li class="ogmt-navbar">';
        $content .= $item;
        $content .= '</li>';
      }

      $content .= '</ul>';

      return $content;
    }

    /**
     * Get navigation for bottom of object list
     * @return string navigation content
     */
    function get_bottom_nav()
    {
      $navbar = array();

      $info = $this->obj_page_info();
      $pagelist = $this->get_page_list($info);

      $min = $pagelist[0];
      $max = $pagelist[count($pagelist) - 1];

      $firstprev = $info['current'] != 1;//display "first" and "preview" links
      $mindots   = ($min > 1); //display "..." prior to num list
      $maxdots   = ($max < $info['total']); //display "..." after num list
      $nextlast  = $info['current'] != $info['total'];//display "next" and "last" links

      if($firstprev)
      {
        array_push($navbar, '<a href='.$this->url_handler->list_url(0).'>First</a>');
        array_push($navbar, '<a href='.$this->url_handler->list_url($info['current']-2).'>Previous</a>');
      }

      if($mindots)
      {
        array_push($navbar, '...');
      }

      foreach($pagelist as $page)
      {
        if($page == $info['current'])
        {
          array_push($navbar, $page);
        }
        else
        {
          array_push($navbar, '<a href='.$this->url_handler->list_url($page-1).'>' . $page . '</a>');
        }
      }

      if($maxdots)
      {
        array_push($navbar, '...');
      }

      if($nextlast)
      {
        array_push($navbar, '<a href='.$this->url_handler->list_url($info['current']).'>Next</a>');
        array_push($navbar, '<a href='.$this->url_handler->list_url($info['total']-1).'>Last</a>');
      }

      $content = "";

      if($info["total"] > 1)
      {
        $content .= '<ul class="ogmt-navbar">';

        foreach($navbar as $item)
        {
          $content .= '<li class="ogmt-navbar">';
          $content .= $item;
          $content .= '</li>';
        }

        $content .= '</ul>';
      }

      return $content;
    }

    function get_page_list($info)
    {
      $total   = $info['total'];
      $current = $info['current'];

      $median = 5;

      $nums = array();

      if($current <= 4)
      {
        for($i = 1; $i <= 9; $i++)
        {
          if($i <= $total)
          {
            array_push($nums, $i);
          }
        }
      }
      elseif(($current + 4) >= $info['total'])
      {
        for($i = $info['total'] - 8; $i <= $info['total']; $i++)
        {
          if($i > 0)
          {
            array_push($nums, $i);
          }
        }
      }
      else
      {
        for($i = ( $current - 4 ); $i <= ($current + 4); $i++)
        {
          if($i > 0 && $i <= $total)
          {
            array_push($nums, $i);
          }
        }
      }

      return $nums;
    }

    /**
     * Get array with current page number and total number of pages
     *
     * Note:
     * info[current] = page of objects user is on
     * info[total]   = total number of pages of objects (10 objects per page)
     *
     * @return array array of page values
     */
    function obj_page_info()
    {
      $rows = $this->options->get_rows();

      $info = array();
      $index = get_query_var('listStart');

      if($index && is_numeric($index) && $index < ($this->search->{'numFound'}/$rows))
      {
        $info['current'] = ($index + 1);
      }
      else
      {
        $info['current'] = 1;
      }

      if($index < $this->search->{'numFound'})
      {
        $num = $this->search->{'numFound'}/$rows;

        if(($num - intval($num)) > 0)
        {
          $num = intval($num) + 1;
        }
        else
        {
          $num = intval($num);
        }

        $info['total'] = $num;
      }
      else
      {
        $info['total'] = false;
      }

      return $info;
    }

    /**
     * Retrieve html string of objects
     *
     * @return string html string of objects
     */
    function get_object_list()
    {
      $content  = '<ul style="list-style:none;">';
      $obs      = $this->search->{'rows'};
      $index    = 0;

      foreach($obs as $row)
      {
        console_log((property_exists($row, 'content')) ? "content" : "no content");
        $content .= $this->get_object($row->{'content'}, $index++) . '<br/>';
      }

      $content .= '</ul>';

      return $content;
    }

    /**
     * Get html for individual objects
     *
     * @param  object $row row of decoded json data for a particular object
     * @return string html string for object data
     */
    function get_object($object, $index)
    {
      $classname = $index;

      $content  = '<li id="' . $classname . '-container' . '" class="ogmt-object-container">';
      $content .= '<div class="obj-header">';

      if($this->options->is_minimized())
      {
        $content .= "<a id=\"$classname-expander\" onclick=\"toggle_non_minis('" . $classname . "')\" href=\"#/\" class=\"expander\">Expand</a>";
      }

      if(property_exists($object, 'descriptiveNonRepeating'))
      {
        if(property_exists($object->{'descriptiveNonRepeating'}, 'online_media'))
        {
          $src = $object->{'descriptiveNonRepeating'}->{'online_media'}->{'media'}[0]->{'thumbnail'};
          $content .= "<img src=\"$src\" />";
        }

        $content .= '<h4>' . $object->{'descriptiveNonRepeating'}->{'title'}->{'content'} . '</h4>';
      }
      elseif(property_exists($object, 'title'))
      {
        if(property_exists($object->{'title'}, 'content'))
        {
          $content .= '<h4>' . $object->{'title'}->{'content'} . '</h4>';
        }else
        {
          $content .= '<h4>' . $object->{'title'} . '</h4>';
        }

      }

      $content .= '<hr/></div>';

      $content .= $this->get_fields($classname, $object);

      $content .= '</li>';

      return $content;
    }

    function get_fields($classname, $object)
    {
      $content = '';

      if(property_exists($object, 'freetext'))
      {
        $labels = $this->options->get_display_data($object->{'freetext'});

        foreach($labels as $field => $vals)
        {
          if(!$this->options->is_minimized())
          {
            $fieldclass = $field;
            $display = 'block';
          }
          elseif($this->options->get_mini($field))
          {
            $fieldclass = "ogmt-object-fields";
            $display = 'none';
          }
          else
          {
            $fieldclass = 'mini';
            $display = 'block';
          }

          $content .= "<div id=\"$field\" class=\"" . $fieldclass . "\" style=\"display:$display\">";

          foreach($vals as $label => $lns)
          {
            $content .= '<div><strong>'. $this->options->replace_label($label) . '</strong></div>';

            foreach($lns as $txt)
            {
              $content .= '<div>' . $txt . '</div>';
            }
          }

          $content .= "</div>";
        }
      }

      $content .= '</li>';

      return $content;
    }
  }
?>
