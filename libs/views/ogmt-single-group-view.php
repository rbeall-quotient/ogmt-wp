<?php
  /**
   * Class handling display for one object group
   */
  class single_group_view
  {
    /**
     * constructor extracts objectGroup and searchResults from ogmt cache
     *
     * @param array $ogmt_cache array of cached ogmt data
     */
    function __construct($ogmt_cache)
    {
        $this->objectGroup = $ogmt_cache['objectGroup'];
        $this->searchResults = $ogmt_cache['searchResults'];

        $this->url_handler = new ogmt_url_manager();
    }

    /**Main View**/

    /**
     * Place standard view and menu view into a grid.
     *
     * @return string content to append
     */
    function content()
    {
      $content  = '<div style="width: 100%; overflow: hidden;">';
      $content .= '<div style="width: 65%; float: left;">' . $this->get_objectGroup_content() . '</div>';
      $content .= '<div style="float: right;"><div>' . $this->get_menu_view() . '</div><hr/><div>'.$this->get_facets_menu().'</div></div>';
      $content .= '</div>';

      if($this->searchResults && $this->searchResults->{'numFound'} > 0)
      {
        $content .= $this->get_search_view();
      }

      return $content;
    }

    /****/

    /***Object Group Views***/

    /**
     * Display object group media and content
     *
     * @return string media and content to append to $content variable.
     */
    function get_objectGroup_content()
    {
      $content = "";

      //Validate that media exists to display
      if(property_exists($this->objectGroup->{'feature'},'media'))
      {
        $content .= $this->objectGroup->{'feature'}->{'media'};
      }

      //Validate if page->content is present. Display description instead
      if(property_exists($this->objectGroup->{'page'},'content'))
      {
        $content .= $this->objectGroup->{'page'}->{'content'};
      }
      elseif(property_exists($this->objectGroup, 'description'))
      {
        $content .= $this->objectGroup->{'description'};
      }

      return $content;
    }

    /**
     * display menu items for object group
     *
     * @return string return menu items to append to $content variable.
     */
    function get_menu_view()
    {
      $content = "<h3>Contents: </h3>";

      foreach($this->objectGroup->menu as $menu)
      {
        $url = $this->url_handler->page_url($menu->url);
        $content .= "<a href=\"$url\">$menu->title</a><br/>";
      }

      return $content;
    }

    /**
     * Function for generating object list search html
     *
     * @return string HTML string for the object list information
     */
    function get_search_view()
    {
      $options = new options_handler(get_option('ogmt_settings'));

      $name = $this->objectGroup->{'title'};
      //$pageName  = property_exists($this->objectGroup->{'page'}, 'title') ? ' - ' . $this->objectGroup->{'page'}->{'title'} : '';
      $page  = property_exists($this->objectGroup->{'page'}, 'title') ? $this->objectGroup->{'page'}->{'title'} : '';
      $count  = $this->searchResults->{'numFound'};

      //$content   = '<div id="edan-results-summary" class="edan-results-summary">"' . $groupName . $pageName . '" showing ' . $itemNums . ' items.</div>';
      $content   = '<div id="edan-results-summary" class="edan-results-summary">' . $options->get_results_message($count, $name, $page) . '</div>';
      $info = $this->obj_page_info();
      $content  .= '<div>Page ' . $info['current'] . ' ' . ($info['total'] ? 'of ' . $info['total'] : '') . '</div>';

      $content .= $this->get_top_nav();
      $content .= $this->get_object_list();
      $content .= $this->get_bottom_nav();

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
      $options = new options_handler(get_option('ogmt_settings'));

      $info = $this->obj_page_info();
      $navbar = array();

      $firstprev = $info['current'] != 1; //display "first" and "preview" links
      $nextlast  = $info['current'] != $info['total']; //display "next" and "last" links
      $expandall = $options->is_minimized(); //whether to add an "Expand All" link

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
      $info = $this->obj_page_info();
      $navbar = array();
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
      $options = new options_handler(get_option('ogmt_settings'));
      $rows = $options->get_rows();

      $info = array();
      $index = get_query_var('listStart');

      if($index && is_numeric($index) && $index < ($this->searchResults->{'numFound'}/$rows))
      {
        $info['current'] = ($index + 1);
      }
      else
      {
        $info['current'] = 1;
      }

      if($index < $this->searchResults->{'numFound'})
      {
        $num = $this->searchResults->{'numFound'}/$rows;

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
      $obs      = $this->searchResults->{'rows'};
      $index    = 0;

      foreach($obs as $row)
      {
        $content .= $this->get_object($row, $index++) . '<br/>';
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
    function get_object($row, $index)
    {
      $options = new options_handler(get_option('ogmt_settings'));
      $classname = $index;

      $content  = '<li id="' . $classname . '-container' . '" class="ogmt-object-container">';
      $content .= '<div class="obj-header">';

      if($options->is_minimized())
      {
        $content .= "<a id=\"$classname-expander\" onclick=\"toggle_non_minis('" . $classname . "')\" href=\"#/\" class=\"expander\">Expand</a>";
      }

      if(property_exists($row->{'content'}, 'descriptiveNonRepeating'))
      {
        if(property_exists($row->{'content'}->{'descriptiveNonRepeating'}, 'online_media'))
        {
          $src = $row->{'content'}->{'descriptiveNonRepeating'}->{'online_media'}->{'media'}[0]->{'thumbnail'};
          $content .= "<img src=\"$src\" />";
        }

        $content .= '<h4>' . $row->{'content'}->{'descriptiveNonRepeating'}->{'title'}->{'content'} . '</h4>';
      }
      elseif(property_exists($row->{'content'}, 'title'))
      {
        if(property_exists($row->{'content'}->{'title'}, 'content'))
        {
          $content .= '<h4>' . $row->{'content'}->{'title'}->{'content'} . '</h4>';
        }else
        {
          $content .= '<h4>' . $row->{'content'}->{'title'} . '</h4>';
        }

      }

      $content .= '<hr/></div>';

      if(property_exists($row->{'content'}, 'freetext'))
      {
        $labels = $options->get_display_data($row->{'content'}->{'freetext'});

        foreach($labels as $field => $vals)
        {
          if(!$options->is_minimized())
          {
            $fieldclass = $field;
            $display = 'block';
          }
          elseif($options->get_mini($field))
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
            $content .= '<div><strong>'. $options->replace_label($label) . '</strong></div>';

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

    /**
     * Get menu of facets to filter object search
     *
     * @return string html string of facet menu
     */
    function get_facets_menu()
    {
      $options = new options_handler(get_option('ogmt_settings'));
      $options->initialize_facet_arrays();

      $content = "";

      if($this->searchResults && property_exists($this->searchResults, 'facets'))
      {
        $edan_fqs = get_query_var('edan_fq');
        if($edan_fqs)
        {
          $content .= '<h4>' . $options->get_remove_message() . '</h4>';
          $content .= '<ul style="list-style:none;">';

          foreach($edan_fqs as $fq)
          {
            $content .= '<li><a href="' . $this->url_handler->remove_facet_url($fq) . '">[X]' . $fq . '</a></li>';
          }
          $content .= '</ul>';
        }

        $content .= '<h3>Filter Your Results</h3>';
        $content .= '<ul style="list-style:none;">';

        foreach($this->searchResults->{'facets'} as $key => $val)
        {
          if(count($val) != 0 && $options->ignore_facet($key))
          {
            $content .= '<li>';
            $content .= '<a href="#/" onclick="toggle_facet_view(' . "'$key'" . ')" id = "' . $key . '-link">&#9658;' . $options->replace_facet($key) . '</a>';
            $content .= $this->get_facet($key, $val);
            $content .= '</li>';
          }
        }

        $content .= '</ul>';

        return $content;
      }

    }

    /**
     * Get html string of specific facet
     *
     * @param  string $key   category of filter
     * @param  string $facet filter to retrieve link for
     *
     * @return string html for a specific filter
     */
    function get_facet($key, $facet)
    {
      $content = '<ul id="facet-'. $key.'" style="list-style:none; display: none;">';

      foreach($facet as $vals)
      {
        if($vals[0] != "")
        {
          $content .= '<span><div>';
          $content .= '<a href="' . $this->url_handler->add_facet_url($key, $vals[0]) . '">' . $vals[0] . '</a>  ';
          $content .= $vals[1]; ' </div>';
          $content .= '</span>';
        }
      }

      $content .= '</ul>';

      return $content;
    }
  }
?>
