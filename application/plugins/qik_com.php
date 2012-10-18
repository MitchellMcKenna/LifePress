<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Qik_com {

    // Sample class for qik

    function pre_db($item, $original)
    {
        return $item;
    }

    function pre_display($item)
    {
        // Get streamname
        $streamname = explode('/', $item->data['enclosures'][0]->link);
        $streamname = str_replace('.flv','',$streamname[4]);

        // Get video id
        $videoid = explode('/', $item->data['permalink']);
        $videoid = $videoid[4];

        // Get username
        $username = explode('/', $item->feed->url);
        $username = $username[3];

        $item->data['video'] = '<object width="425" height="319"><param name="movie" value="http://qik.com/swfs/qik_player.swf?streamname='.$streamname.'&vid='.$videoid.'&playback=false&polling=false&user='.$username.'&displayname='.$username.'&safelink='.$username.'&userlock=true&islive=&username=anonymous" ></param><param name="wmode" value="transparent" ></param><param name="allowScriptAccess" value="always" ><embed src="http://qik.com/swfs/qik_player.swf?streamname='.$streamname.'&vid='.$videoid.'&playback=false&polling=false&user='.$username.'&displayname='.$username.'&safelink='.$username.'&userlock=true&islive=&username=anonymous" type="application/x-shockwave-flash" wmode="transparent" width="425" height="319" allowScriptAccess="always"></embed></object>';

        return $item;
    }
}

/* End of file qik_com.php */
/* Location: ./application/plugins/qik_com.php */
