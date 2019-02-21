<?php

  /**
   * Class designed to handle displaying views for object groups. Methods to
   * append html for media and description block as well as menu blocks.
   */
  class ogmt_view_manager
  {
    public $object_group;

    function __construct($ogmt_cache)
    {
      $this->object_group = $ogmt_cache['objectGroup'];
      $this->search_results = $ogmt_cache['searchResults'];
    }

    /**Main View**/

    /**
     * Place standard view and menu view into a grid.
     *
     * @return String content to append
     */
    function get_content()
    {
      $content  = '<div style="width: 100%; overflow: hidden;">';
      $content .= '<div style="width: 65%; float: left;">' . $this->get_object_group_content() . '</div>';
      $content .= '<div style="float: right;"><div>' . $this->get_menu_view() . '</div><hr/><div>'.$this->get_facets_menu().'</div></div>';
      $content .= '</div>';

      if($this->search_results && $this->search_results->{'numFound'} > 0)
      {
        $content .= $this->get_search_preview();
        $page = get_query_var('objIndex');

        if(!$page)
        {
          $page = 0;
        }

        console_log('page: '.$page);

        $content .= $this->get_object_list($page);
      }

      return $content;
    }

    /****/

    /***Object Group Views***/

    /**
     * Display object group media and content
     *
     * @return String media and content to append to $content variable.
     */
    function get_object_group_content()
    {
      $content = "";

      //Validate that media exists to display
      if(property_exists($this->object_group->{'feature'},'media'))
      {
        $content .= $this->object_group->{'feature'}->{'media'};
      }

      //Validate if page->content is present. Display description instead
      if(property_exists($this->object_group->{'page'},'content'))
      {
        $content .= $this->object_group->{'page'}->{'content'};
      }
      elseif(property_exists($this->object_group, 'description'))
      {
        $content .= $this->object_group->{'description'};
      }

      return $content;
    }

    /**
     * display menu items for object group
     *
     * @return String return menu items to append to $content variable.
     */
    function get_menu_view()
    {
      $content = "<h3>Contents: </h3>";

      foreach($this->object_group->menu as $menu)
      {
        $url = $this->get_menu_url($menu->url);
        $content .= "<a href=\"$url\">$menu->title</a><br/>";
      }

      return $content;
    }

    //get object group url and append the correct menu url
    function get_menu_url($q_var)
    {
      $_service = get_query_var('_service');
      $creds = get_query_var('creds');
      $objectGroupUrl = get_query_var('objectGroupUrl');

      $url = trim(esc_url_raw(add_query_arg([])), '/');
      $url = explode('?', $url, 2)[0];
      $url .= "?creds=$creds&_service=$_service&objectGroupUrl=$objectGroupUrl&pageUrl=$q_var";

      return $url;
    }

    /*******/

    /**Search Displays**/

    /**
     * Function for generating prefix information above object list
     *
     * Note: Will build out once object list is implemented.
     *
     * @return String HTML string for the prefix.
     */
    function get_search_preview()
    {
      $groupName = $this->object_group->{'title'};
      $pageName  = property_exists($this->object_group->{'page'}, 'title') ? ' - ' . $this->object_group->{'page'}->{'title'} : '';
      $itemNums  = $this->search_results->{'numFound'};

      //$content   = '<div id="search-results-prefix"></div>';
      $content   = '<div id="edan-results-summary" class="edan-results-summary">"' . $groupName . $pageName . '" showing ' . $itemNums . ' items.</div>';
      $info = $this->obj_page_info();
      $content  .= '<div>Page ' . $info['current'] . ' ' . ($info['total'] ? 'of ' . $info['total'] : '') . '</div>';
      $content  .= $this->get_top_nav($info);

      return $content;
    }

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
        $content .= '<a href='.$this->get_list_url($info['current']).'>Next</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->get_list_url($info['total']-1).'>Last</a>';
        $content .= '</li>';
      }
      elseif($info['current'] != $info['total'])
      {
        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->get_list_url(0).'>First</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->get_list_url($info['current']-2).'>Previous</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->get_list_url($info['current']).'>Next</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->get_list_url($info['total']-1).'>Last</a>';
        $content .= '</li>';
      }
      else
      {
        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->get_list_url(0).'>First</a>';
        $content .= '</li>';

        $content .= '<li style="display: inline-block; padding: 5px;">';
        $content .= '<a href='.$this->get_list_url($info['current']-2).'>Previous</a>';
        $content .= '</li>';
      }

      $content .= '</ul>';

      return $content;
    }

    function obj_page_info()
    {
      $info = array();
      $index = get_query_var('listStart');

      if($index && is_numeric($index) && $index < ($this->search_results->{'numFound'}/10))
      {
        $info['current'] = ($index + 1);
      }
      else
      {
        $info['current'] = 1;
      }

      if($index < $this->search_results->{'numFound'})
      {
        $num = $this->search_results->{'numFound'}/10;

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

    function get_list_url($num)
    {
      $creds = get_query_var('creds');
      $objectGroupUrl = get_query_var('objectGroupUrl');
      $pageUrl = get_query_var('pageUrl');
      $listStart = $num;

      $url  = trim(esc_url_raw(add_query_arg([])), '/');
      $url  = explode('?', $url, 2)[0];
      $url .= "?creds=$creds&objectGroupUrl=$objectGroupUrl&";

      if($pageUrl)
      {
          $url .= "pageUrl=$pageUrl&";
      }

      $url .= "listStart=$num";

      $edan_fq = get_query_var('edan_fq');

      if($edan_fq)
      {
        foreach($edan_fq as $fq)
        {
          $url .= '&edan_fq[]=' . $fq;
        }
      }

      return $url;
    }

    function get_object_list($page)
    {
      $index = ($page * 10);
      console_log("INDEX LENGTH: $index");

      $content  = '<ul style="list-style:none;">';
      $obs      = $this->search_results->{'rows'};

      foreach($obs as $row)
      {
        $content .= $this->get_object($row);
      }

      $content .= '</ul>';

      return $content;
    }

    function get_object($row)
    {
      $content = '';
      if(property_exists($row->{'content'}, 'descriptiveNonRepeating'))
      {
        $content .= '<li>';
        $content .= '<hr/>';
        $content .= '<h5>' . $row->{'content'}->{'descriptiveNonRepeating'}->{'title'}->{'content'} . '</h5>';
        $content .= '</li>';
      }

      return $content;
    }

    /***facets***/

    function get_facets_menu()
    {
      $content = "";

      if($this->search_results && property_exists($this->search_results, 'facets'))
      {
        $edan_fqs = get_query_var('edan_fq');
        if($edan_fqs)
        {
          $content .= '<ul style="list-style:none;">';
          foreach($edan_fqs as $fq)
          {
            $content .= '<li><a href="' . $this->remove_facet_link($fq) . '">[X]' . $fq . '</a></li>';
          }
          $content .= '</ul>';
        }

        $content .= '<h3>Filter Your Results</h3>';
        $content .= '<ul style="list-style:none;">';

        foreach($this->search_results->{'facets'} as $key => $val)
        {
          if(count($val) != 0)
          {
            $content .= '<li>';
            $content .= '<p>' . $key . '</p>';
            $content .= $this->get_facet($key, $val);
            $content .= '</li>';
          }
        }

        $content .= '</ul>';

        return $content;
      }

    }

    function get_facet($key, $facet)
    {
      $content = '<ul style="list-style:none;">';

      foreach($facet as $vals)
      {
        $content .= '<span>';
        $content .= '<div><a href="' . $this->add_facet_link($key, $vals[0]) . '">' . $vals[0] . '</a>  ';
        $content .= $vals[1]; ' </div>';
        $content .= '</span>';
      }

      $content .= '</ul>';

      return $content;
    }

    function add_facet_link($key, $filter)
    {
      $url  = trim(esc_url_raw(add_query_arg([])), '/');
      $url  = explode('?', $url, 2)[0];

      $url .= '?creds=' . get_query_var('creds');
      $url .= '&objectGroupUrl=' . get_query_var('objectGroupUrl');

      $pageUrl = get_query_var('pageUrl');

      if($pageUrl)
      {
        $url .= '&pageUrl=' . $pageUrl;
      }

      $edan_fqs = get_query_var('edan_fq');

      if($edan_fqs)
      {
        foreach($edan_fqs as $fq)
        {
          $url .= '&edan_fq[]=' . $fq;
        }
      }

      $url .= '&edan_fq[]=' . $key . ':' . $filter;

      return $url;
    }

    function remove_facet_link($facet)
    {
      $url  = trim(esc_url_raw(add_query_arg([])), '/');
      $url  = explode('?', $url, 2)[0];

      $url .= '?creds=' . get_query_var('creds');
      $url .= '&objectGroupUrl=' . get_query_var('objectGroupUrl');

      $pageUrl = get_query_var('pageUrl');

      if($pageUrl)
      {
        $url .= '&pageUrl=' . $pageUrl;
      }

      $edan_fqs = get_query_var('edan_fq');

      if($edan_fqs)
      {
        foreach($edan_fqs as $fq)
        {
          if($fq != $facet)
          {
            $url .= '&edan_fq[]=' . $fq;
          }
        }
      }
      return $url;
    }
    /************/

  }

?>
