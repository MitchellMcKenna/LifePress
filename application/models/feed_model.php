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

class Feed_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function _process($feeds)
    {
        if ($feeds) {
            foreach ($feeds as $key => $value) {
                $feeds[$key]->item_count = $this->db->select('ID')->get_where('items', array('item_status !=' => 'deleted', 'item_feed_id' => $feeds[$key]->feed_id))->num_rows();
            }

            return $feeds;
        } else {
            return array();
        }
    }

    function get_feeds()
    {
        return $this->_process($this->db->get('feeds')->result());
    }

    function count_active_feeds()
    {
        return $this->db->get_where('feeds', array('feed_status' => 'active'))->num_rows();
    }

    function get_active_feeds($group = FALSE)
    {
        if ($group) {
            return $this->_process($this->db->group_by('feed_domain')->get_where('feeds', array('feed_status' => 'active'))->result());
        } else {
            return $this->_process($this->db->get_where('feeds', array('feed_status' => 'active'))->result());
        }
    }

    function add_feed($feed)
    {
        $this->db->insert('feeds', $feed);
    }

    function delete_feed($feed_id)
    {
        $this->db->update('feeds', array('feed_status' => 'deleted'), array('feed_id' => $feed_id));
    }

}

/* End of file feed_model.php */
/* Location: ./application/models/feed_model.php */
