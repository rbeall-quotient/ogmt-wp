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
        $content .= $this->get_search_preview();
        $page = get_query_var('listStart');

        if(!$page)
        {
          $page = 0;
        }

        $content .= $this->get_object_list();
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
     * Function for generating prefix information above object list
     *
     * Note: Will build out once object list is implemented.
     *
     * @return string HTML string for the prefix.
     */
    function get_search_preview()
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
      $content  .= $this->get_top_nav($info);

      return $content;
    }

    /**
     * Display nav links above objects list
     *
     * @param  string $info current and total page numbers
     * @return string html string of nav links
     */
    function get_top_nav($info)
    {
      $content  = '<ul style="text-align: left">';

      if($info['total'] == 1)
      {
        $content .= "";
      }
      elseif($info['current'] == 1)
      {
        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url($info['current']).'>Next</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url($info['total']-1).'>Last</a>';
        $content .= '</li>';
      }
      elseif($info['current'] != $info['total'])
      {
        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url(0).'>First</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url($info['current']-2).'>Previous</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url($info['current']).'>Next</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url($info['total']-1).'>Last</a>';
        $content .= '</li>';
      }
      else
      {
        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url(0).'>First</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->url_handler->list_url($info['current']-2).'>Previous</a>';
        $content .= '</li>';
      }

      $content .= '</ul>';

      return $content;
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

      foreach($obs as $row)
      {
        $content .= $this->get_object($row) . '<br/>';
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
    function get_object($row)
    {
      $options = new options_handler(get_option('ogmt_settings'));
      $labels = $options->get_display_data($row->{'content'}->{'freetext'});
      $classname = $row->{'id'};

      $content  = '<li id="' . $classname . '" class="object">';
      $content .= '<div class="obj-header">';

      if($options->is_minimized())
      {
        $content .= "<a id=\"$classname-expander\" onclick=\"toggle_non_minis('" . $classname . "')\" href=\"#/\" class=\"expander\">Expand</a>";
      }

      if(property_exists($row->{'content'}->{'descriptiveNonRepeating'}->{'online_media'}, 'media'))
      {
        $src = $row->{'content'}->{'descriptiveNonRepeating'}->{'online_media'}->{'media'}[0]->{'thumbnail'};
        $content .= "<img src=\"$src\" />";
      }

      if(property_exists($row->{'content'}, 'descriptiveNonRepeating'))
      {
        $content .= '<h4>' . $row->{'content'}->{'descriptiveNonRepeating'}->{'title'}->{'content'} . '</h4>';
      }
      elseif(property_exists($row->{'content'}, 'title'))
      {
        $content .= '<h4>' . $row->{'content'}->{'title'} . '</h4>';
      }

      $content .= '<hr/></div>';

      foreach($labels as $field => $vals)
      {
        if(!$options->is_minimized())
        {
          console_log("Not minimized");
          $fieldclass = $field;
          $display = 'block';
        }
        elseif($options->get_mini($field))
        {
          console_log("$field will not display");
          $fieldclass = $row->{'id'};
          $display = 'none';
        }
        else
        {
          console_log("$field will display");
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
            $content .= '<p>' . $options->replace_facet($key) . '</p>';
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
      $content = '<ul style="list-style:none;">';

      foreach($facet as $vals)
      {
        $content .= '<span>';
        $content .= '<div><a href="' . $this->url_handler->add_facet_url($key, $vals[0]) . '">' . $vals[0] . '</a>  ';
        $content .= $vals[1]; ' </div>';
        $content .= '</span>';
      }

      $content .= '</ul>';

      return $content;
    }
  }
?>
