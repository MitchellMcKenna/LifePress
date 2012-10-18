<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Twitter_com {

    // Sample class for twitter

    function pre_db($item, $original)
    {
        $original_publisher = $original->get_permalink();
        $twitter_username = explode('/', $original_publisher);
        $twitter_username = $twitter_username[3];

        // Remove username from front of posts
        $item->title = trim(str_replace($twitter_username.':', '', $item->item_title));

        // Filter out @replies (set as unpublished)
        if (substr($item->title, 0, 1) == '@') {
            $item->status = 'draft';
        }

        // Remove item_content as it's just the same as the title anyway
        $item->content = '';

        return $item;
    }

    function pre_display($item)
    {
        return $item;
    }


}

/* End of file twitter_com.php */
/* Location: ./application/plugins/twitter_com.php */
