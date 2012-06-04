<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Blogsearch_google_com {

    // Sample class for google blog search

    function pre_db($item, $original)
    {
        // Add publisher data
        $original_publisher = $original->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'publisher');

        if (isset($original_publisher[0]['data'])) {
            $item->item_data['publisher'] = $original_publisher[0]['data'];
        }

        return $item;
    }

    function pre_display($item)
    {
        return $item;
    }
}

/* End of file blogsearch_google_com.php */
/* Location: ./application/plugins/blogsearch_google_com.php */
