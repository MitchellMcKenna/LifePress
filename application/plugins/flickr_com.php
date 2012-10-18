<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Flickr_com {

    // Sample class for flickr

    function pre_db($item, $original)
    {
        // Set to TRUE if you want to use the date a photo was taken (as opposed to time uploaded) as the timestamp in LifePress
        $use_date_taken = FALSE;

        // Do not edit below this line

        if ($use_date_taken) {
            //override with date taken
            $date = $original->get_item_tags('http://purl.org/dc/elements/1.1/', 'date.Taken');
            $item->date = strtotime(str_replace('T', ' ', substr($date[0]['data'], 0, -6)));
        }

        // Remove username etc
        $flickr_username = $original->get_item_tags('http://www.w3.org/2005/Atom', 'author');
        $flickr_username = $flickr_username[0]['child']['http://www.w3.org/2005/Atom']['name'][0]['data'];
        $remove_this = $flickr_username.' posted a photo:';
        $item->content = trim(str_replace($remove_this, '', $item->content));

        // Some flickr feeds have different tag formatting
        if (isset($item->data['categories'])) {
            foreach ($item->data['categories'] as $key => $value) {
                $item->data['tags'][$key] = $value->term;
            }
        }

        return $item;
    }

    function pre_display(Item $item)
    {
        $item->data['flickr_com']['image']['m'] = substr($item->data['image'], 0, -6).'.jpg';
        $item->data['flickr_com']['image']['l'] = substr($item->data['image'], 0, -5).'b.jpg';
        $item->data['flickr_com']['image']['o'] = substr($item->data['image'], 0, -5).'o.jpg';
        return $item;
    }
}

/* End of file flick_com.php */
/* Location: ./application/plugins/flickr_com.php */
