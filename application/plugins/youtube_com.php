<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Youtube_com {

    // Sample class for youtube

    function pre_db(Item $item, $original)
    {
        // If content is not automatically found, try to grab it from enclosure description
        if (empty($item->content) && !empty($item->data['enclosures'][0]->description)) {
		    $item->content	= $item->data['enclosures'][0]->description;
        }

        // If image is not automatically found, try to grab it from enclosure thumbnails
        if (empty($item->data['image']) && !empty($item->data['enclosures'][0]->thumbnails[0])) {
            $item->data['image'] = $item->data['enclosures'][0]->thumbnails[0];
        }

        return $item;
    }

    function pre_display(Item $item)
    {

        // var_dump($item);
        // exit;

        $link = $item->data['permalink'];
		$link = str_replace('?v=', '/v/', $link);
		$link = str_replace('watch/', '', $link);

        $item->data['video'] = '<object width="212" height="178"><param name="movie" value="'.$link.'&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><embed src="'.$link.'&hl=en&fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="212" height="178"></embed></object>';
        return $item;
    }
}

/* End of file youtube_com.php */
/* Location: ./application/plugins/youtube_com.php */
