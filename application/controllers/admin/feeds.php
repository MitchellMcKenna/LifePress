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

class Feeds extends MY_Auth_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
    }

    public function index()
    {
        $data = new StdClass();
        $data->page_name = 'Feeds';

        $data->feeds = $this->feed_model->get_active_feeds(TRUE);

        $this->load->view('admin/_header', $data);
        $this->load->view('admin/feeds', $data);
        $this->load->view('admin/_footer');
    }

    public function add()
    {
        $data = new StdClass();
        $data->page_name = 'Add Feed';

        $this->form_validation->set_rules('url', 'Url', 'trim|prep_url|required|xss_clean|callback_test_feed');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        if ($this->form_validation->run()) {
            $feed = new Feed();
            $feed->hydrate(array(
                'feed_title' => $this->simplepie->get_title(),
                'feed_icon' => $this->simplepie->get_favicon(),
                'feed_url' => $this->input->post('url', TRUE),
                'feed_status' => 'active'
            ));

            // Use permalink because sometimes feed is on subdomain which screws up plugin compatibility
            $url = parse_url($this->simplepie->get_permalink());
            if ( ! $url['host']) {
                $url = parse_url($this->input->post('url', TRUE));
            }
            if (substr($url['host'], 0, 4) === 'www.') {
                $feed->domain = substr($url['host'], 4);
            } else {
                $feed->domain = $url['host'];
            }

            if (!$feed->icon) {
                $feed->icon = 'http://' . $feed->domain . '/favicon.ico';
            }

            // Add new feed to database
            $feed = $this->feed_model->add_feed($feed);

            // Fetch items for the feed.
            $this->lifepress->fetch_item($feed);

            redirect('admin/feeds', 'location');
        }

        $this->load->view('admin/_header', $data);
        $this->load->view('admin/feed_add', $data);
        $this->load->view('admin/_footer');
    }

    public function delete($feed_id)
    {
        $this->feed_model->delete_feed($feed_id);
        redirect('admin/feeds', 'location');
    }

    public function test_feed($url)
    {
        if ( ! filter_var($url, FILTER_VALIDATE_URL)) {
            $this->form_validation->set_message('test_feed', 'Invalid url');
            return FALSE;
        }

        $this->simplepie->set_feed_url($url);
        $this->simplepie->enable_cache(FALSE);
        $this->simplepie->init();

        if ($this->simplepie->error()) {
            $this->form_validation->set_message('test_feed', $this->simplepie->error());
            return FALSE;
        }

        // Check if already in the db
        $feed = $this->db->get_where('feeds', array('feed_url' => $url))->row();
        if ($feed) {
            // If it was a deleted feed just reactivate it and forward to feed page
            if ($feed->feed_status === 'deleted') {
                $this->db->update('feeds', array('feed_status' => 'active'), array('feed_id' => $feed->feed_id));
                redirect('admin/feeds', 'location');
            } else {
                $this->form_validation->set_message('test_feed', 'You already added that feed...');
                return FALSE;
            }
        }

        // Looks like the feed is ok
        return TRUE;
    }
}

/* End of file feeds.php */
/* Location: ./application/controllers/admin/feeds.php */
