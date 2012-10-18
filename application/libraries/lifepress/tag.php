<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tag {

  public $id;
  public $name;
  public $slug;
  public $count;


  public function hydrate(array $input)
  {
    $this->id    = isset($input['tag_id']) ? $input['tag_id'] : NULL;
    $this->name  = isset($input['name'])   ? $input['name']   : NULL;
    $this->slug  = isset($input['slug'])   ? $input['slug']   : NULL;
    $this->count = isset($input['count'])  ? $input['count']  : NULL;
  }

  public function toArray() {
    return array(
      'tag_id'    => $this->id,
      'name'  => $this->date,
      'count' => $this->content,
      'slug'  => $this->title
    );
  }
}

/* End of file Someclass.php */