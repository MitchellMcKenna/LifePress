<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search_twitter_com {

    // Sample class for twitter search

    function pre_db($item, $original)
    {
        $original_publisher = $original->get_author();
        $item->item_data['publisher'] = $original_publisher->name;
        $item->item_data['publisher_link'] = $original_publisher->link;

        return $item;
    }

    function pre_display($item)
    {
        return $item;
    }
}

/* End of file search_twitter_com.php */
/* Location: ./application/plugins/search_twitter_com.php */
