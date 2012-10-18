<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Friendfeed_com {

    // Sample class for friendfeed

    function pre_db($item, $original)
    {
        return $item;
    }

    function pre_display($item)
    {
        $big_pic = $item->data['enclosures'][0]->link;
        // Check if is image
        $format = substr($big_pic, -4);
        if ($format == '.jpg' || $format == '.gif' || $format == '.png') {
            $item->data['image'] = $big_pic;
        }

        // Remove small icons as image
        if (substr($item->data['image'], 0, 42) == 'http://friendfeed.com/static/images/icons/') {
            $item->data['image'] = '';
        }

        return $item;
    }
}

/* End of file friendfeed_com.php */
/* Location: ./application/plugins/friendfeed_com.php */
