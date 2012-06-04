<?php if (!defined('BASEPATH')) exit('No direct access allowed.');
/**
 * LifePress - Lifestream software built on the CodeIgniter PHP framework.
 * Copyright (c) 2012, Mitchell McKenna <mitchellmckenna@gmail.com>
 *
 * LifePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * LifePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with LifePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 * This file incorporates work covered by the following copyright and
 * permission notice:
 *
 *     Sweetcron - Self-hosted lifestream software based on the CodeIgniter framework.
 *     Copyright (c) 2008, Yongfook.com & Edible, Inc. All rights reserved.
 *
 *     Sweetcron is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     Sweetcron is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 *     You should have received a copy of the GNU General Public License 
 *     along with Sweetcron.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     LifePress
 * @author      Mitchell McKenna <mitchellmckenna@gmail.com>
 * @copyright   Copyright (c) 2012, Mitchell McKenna
 * @license     http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

class Lifepress_item {

    function __construct()
    {
    }

    //Return item feed components

    function get_feed_id()
    {
        return $this->feed_id;
    }

    function get_feed_title()
    {
        return $this->feed_title;
    }

    function get_feed_icon()
    {
        return $this->feed_icon;
    }

    function get_feed_url()
    {
        return $this->feed_url;
    }

    function get_feed_data()
    {
        return $this->feed_data;
    }

    function get_feed_status()
    {
        return $this->feed_status;
    }

    function get_feed_domain()
    {
        return $this->feed_domain;
    }

    function get_feed_class()
    {
        return $this->feed_class;
    }

    // Return item components

    function get_id()
    {
        return $this->ID;
    }

    function get_date()
    {
        return $this->item_date;
    }

    function get_nice_date()
    {
        return $this->nice_date;
    }

    function get_human_date()
    {
        return $this->human_date;
    }

    function get_content()
    {
        return $this->item_content;
    }

    function get_title()
    {
        return $this->item_title;
    }

    function get_permalink()
    {
        return $this->item_permalink;
    }

    function get_original_permalink()
    {
        return $this->item_original_permalink;
    }

    function get_status()
    {
        return $this->item_status;
    }

    function get_name()
    {
        return $this->item_name;
    }

    function get_parent()
    {
        return $this->item_parent;
    }

    function get_data()
    {
        return $this->item_data;
    }

    // "has" conditionals for item data
    function has_content()
    {
        if (isset($this->item_content) && $this->item_content != '') {
            return true;
        }
    }

    function has_permalink()
    {
        if (isset($this->item_permalink)) {
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
        if (isset($this->item_data['video'])) {
            return true;
        }
    }

    function has_audio()
    {
        if (isset($this->item_data['audio'])) {
            return true;
        }
    }

    function has_image()
    {
        if (isset($this->item_data['image']) && !empty($this->item_data['image'])) {
            return true;
        }
    }

    function has_tags()
    {
        if (isset($this->item_tags[0])) {
            return true;
        }
    }

    function has_tag($query = NULL)
    {
        $query = strtolower($query);
        if (isset($this->item_tags[0])) {
            foreach ($this->item_tags as $tag) {
                if ($tag->slug == $query) {
                    return true;
                }
            }
        }
    }

    function has_data($key = NULL)
    {
        if (isset($this->item_data[$key])) {
            return true;
        }
    }

    //get item data
    function get_video()
    {
        if ($this->has_video()) {
            return $this->item_data['video'];
        }
    }

    function get_audio()
    {
        if ($this->has_audio()) {
            return $this->item_data['audio'];
        }
    }

    function get_image()
    {
        if ($this->has_image()) {
            return $this->item_data['image'];
        }
    }

    function get_tags()
    {
        if ($this->has_tags()) {
            return $this->item_tags;
        } else {
            return array();
        }
    }

}

/* End of file lifepress_item.php */
/* Location: ./application/libraries/lifepress_item.php */
