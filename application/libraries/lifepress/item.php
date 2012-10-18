<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Item {

  public $id;
  public $date;
  public $content;
  public $title;
  public $permalink;
  public $status;
  public $name;
  public $parent;
  public $data;
  public $feed_id;

  public $tags = array();
  public $feed;

  public function hydrate(array $input)
  {
    $this->id        = isset($input['ID'])             ? $input['ID'] :             NULL;
    $this->date      = isset($input['item_date'])      ? $input['item_date'] :      NULL;
    $this->content   = isset($input['item_content'])   ? $input['item_content'] :   NULL;
    $this->title     = isset($input['item_title'])     ? $input['item_title'] :     NULL;
    $this->permalink = isset($input['item_permalink']) ? $input['item_permalink'] : NULL;
    $this->status    = isset($input['item_status'])    ? $input['item_status'] :    NULL;
    $this->name      = isset($input['item_name'])      ? $input['item_name'] :      NULL;
    $this->parent    = isset($input['item_parent'])    ? $input['item_parent'] :    NULL;
    $this->data      = isset($input['item_data'])      ? unserialize($input['item_data']) : NULL;
    $this->feed_id   = isset($input['item_feed_id'])   ? $input['item_feed_id'] :   NULL;

    if ($this->data instanceOf StdClass) {
      $this->data = (array)$this->data;
    }

    $this->feed = new Feed();
    $this->feed->hydrate($input);
  }

  public function toArray() {
    return array(
      'ID'             => $this->id,
      'item_date'      => $this->date,
      'item_content'   => $this->content,
      'item_title'     => $this->title,
      'item_permalink' => $this->permalink,
      'item_status'    => $this->status,
      'item_name'      => $this->name,
      'item_parent'    => is_null($this->parent) ? 0 : $this->parent,
      'item_data'      => serialize($this->data),
      'item_feed_id'   => $this->feed_id
    );
  }

  public function is_blog_post() {
    return !(bool)$this->feed_id;
  }

  public function set_tags(array $tags) {
    foreach ($tags as $tag) {
      $this->tags[$tag->id] = $tag;
    }
  }

  public function apply_markdown_filter() {
    return markdown($this->content);
  }

  public function get_autolink_filter() {
    $search = '/(?<!\S)((http(s?):\/\/)|(www\.))+([\w.\/&=?\-~%;]+)\b/i';
    $replace = '<a href="http$3://$4$5" rel="external">http$3://$4$5</a>';
    return preg_replace($search, $replace, $this->title);
  }

  public function get_local_permalink() {
    return $this->config->item('base_url') . 'items/view/' . $this->id;
  }

  public function get_human_date() {
    if ($this->date < strtotime('1 day ago')) {
      return date('F j Y, g:ia', $this->date);
    } else {
      return timespan($this->date) .' ago';
    }
  }


  /**
   * Code below here could use refactoring possibly
   */
  // "has" conditionals for item data
    function has_content()
    {
        if ($this->content != '') {
            return true;
        }
    }

    function has_permalink()
    {
        if ($this->permalink != '') {
            return true;
        }
    }

    function has_original_permalink()
    {
        if (isset($this->item_original_permalink)) {
            return true;
        }
    }

    function has_video()
    {
        if (isset($this->data['video'])) {
            return true;
        }
    }

    function has_audio()
    {
        if (isset($this->data['audio'])) {
            return true;
        }
    }

    function has_image()
    {
        if (isset($this->data['image']) && !empty($this->data['image'])) {
            return true;
        }
    }

    function has_tags()
    {
        if (isset($this->tags[0])) {
            return true;
        }
    }

    function has_tag($query = NULL)
    {
        $query = strtolower($query);
        if (isset($this->tags[0])) {
            foreach ($this->tags as $tag) {
                if ($tag->slug == $query) {
                    return true;
                }
            }
        }

        return false;
    }

    function has_data($key = NULL)
    {
        if (isset($this->data[$key])) {
            return true;
        }
    }

    //get item data
    function get_video()
    {
        if ($this->has_video()) {
            return $this->data['video'];
        }
    }

    function get_audio()
    {
        if ($this->has_audio()) {
            return $this->data['audio'];
        }
    }

    function get_image()
    {
        if ($this->has_image()) {
            return $this->data['image'];
        }
    }

    function get_tags()
    {
        if ($this->has_tags()) {
            return $this->tags;
        } else {
            return array();
        }
    }


  /**
   * __get
   *
   * Allows models to access CI's loaded classes using the same
   * syntax as controllers.
   *
   * @param string
   */
  public function __get($key)
  {
    $CI =& get_instance();
    return $CI->$key;
  }
}

/* End of file Someclass.php */