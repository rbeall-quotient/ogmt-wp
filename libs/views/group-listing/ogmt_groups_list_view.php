<?php
  /**
   * Display generalized object group list
   */
  class groups_list_view
  {
    function __construct()
    {
      $edan = new ogmt_edan_handler();
      $this->url_handler = new url_handler();
      $this->options = new options_handler(get_option('ogmt_settings'));
      $this->groups = $edan->get_cache()['groups'];
    }

    function get_content()
    {
      $content = $this->get_top_nav();
      $content .= $this->get_groups();
      $content .= $this->get_bottom_nav();

      return $content;
    }

    /**
     * Display vertical list of object groups
     *
     * @return string html string of object group content
     */
    function get_groups()
    {
      $content  = '';

      if($this->groups)
      {
        $content .= '<h5>All Object Groups</h5>';
        $content .= '<ul style="list-style:none;">';

        foreach($this->groups->{'objectGroups'} as $group)
        {
          $url = $this->url_handler->group_url($group->{'url'});

          $content .= '<li><span style="display:inline-block;">';
          $content .= '<a href="' . $url . '">';

          if(property_exists($group->{'feature'}, 'media'))
          {
            $content .= $group->{'feature'}->{'media'};
          }
          else
          {
            $content .= '<img src="' . $group->{'feature'}->{'url'} . '"/>';
          }

          $content .= '</a>';

          $content .= '<div><div><a href="' . $url . '">';
          $content .= '<h4>' . $group->{'title'} . '</h4></a></div>';

          if(property_exists($group, 'description'))
          {
              $content .= '<div>' . $group->{'description'} . '</div>';
          }

          $content .= '</div></span></li>';
        }

        $content .= '</ul>';
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
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url(0).'>First</a>');
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url($info['current']-2).'>Previous</a>');
      }

      if($nextlast)
      {
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url($info['current']).'>Next</a>');
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url($info['total']-1).'>Last</a>');
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
     * Get navigation for bottom of object group list
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
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url(0).'>First</a>');
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url($info['current']-2).'>Previous</a>');
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
          array_push($navbar, '<a href='.$this->url_handler->groups_list_url($page-1).'>' . $page . '</a>');
        }
      }

      if($maxdots)
      {
        array_push($navbar, '...');
      }

      if($nextlast)
      {
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url($info['current']).'>Next</a>');
        array_push($navbar, '<a href='.$this->url_handler->groups_list_url($info['total']-1).'>Last</a>');
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

    /**
     * Get list of Object Groups Pages
     *
     * @param  string $info current page and total number of pages
     * @return array       array of numbers
     */
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
     * info[current] = page of object groups user is on
     * info[total]   = total number of pages of object groups (20 objects per page)
     *
     * @return array array of page values
     */
    function obj_page_info()
    {
      $info = array();
      $index = get_query_var('ogmtStart');

      if($index && is_numeric($index) && $index < ($this->groups->{'total'}/20))
      {
        $info['current'] = ($index + 1);
      }
      else
      {
        $info['current'] = 1;
      }

      if($index < $this->groups->{'total'})
      {
        $num = $this->groups->{'total'}/20;

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
  }
?>
