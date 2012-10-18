<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed {

  public $id;
  public $title;
  public $icon;
  public $url;
  public $data;
  public $status;
  public $domain;

  public $items = array();

  public function hydrate(array $input, array $extra_columns = array())
  {
    $this->id     = isset($input['feed_id'])     ? $input['feed_id']     : NULL;
    $this->title  = isset($input['feed_title'])  ? $input['feed_title']  : NULL;
    $this->icon   = isset($input['feed_icon'])   ? $input['feed_icon']   : NULL;
    $this->url    = isset($input['feed_url'])    ? $input['feed_url']    : NULL;
    $this->data   = isset($input['feed_data'])   ? unserialize($input['feed_data']) : NULL;
    $this->status = isset($input['feed_status']) ? $input['feed_status'] : NULL;
    $this->domain = isset($input['feed_domain']) ? $input['feed_domain'] : NULL;

    if ($this->is_blog()) {
      $this->icon = '/favicon.ico';

      $url = parse_url($this->config->item('base_url'));
      if (substr($url['host'], 0, 4) == 'www.') {
          $this->domain = substr($url['host'], 4);
      } else {
          $this->domain = $url['host'];
      }
    }

    foreach ($extra_columns as $column_name) {
      $this->$column_name = $input[$column_name];
    }
  }

  public function toArray() {
    return array(
      'feed_id'     => $this->id,
      'feed_title'  => $this->title,
      'feed_icon'   => $this->icon,
      'feed_url'    => $this->url,
      'feed_data'   => serialize($this->data),
      'feed_status' => $this->status,
      'feed_domain' => $this->domain
    );
  }

  public function addItem(Item $item) {
    $this->items[$item->id] = $item;
  }

  public function get_class() {
    return str_replace('.', '_', $this->domain);
  }

  public function is_blog() {
    return is_null($this->id);
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