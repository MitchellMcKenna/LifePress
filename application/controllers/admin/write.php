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

class Write extends MY_Auth_Controller {

    /**
     * Builds the form validation constructors for editing and creating new
     * item_models.
     */
    function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
        $this->form_validation->set_rules('content', 'Content', 'trim');
        $this->form_validation->set_rules('tags', 'Tags', 'trim|xss_clean');
        $this->form_validation->set_rules('timestamp', 'Date', 'trim|xss_clean');

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    }

    /**
     * Edit an existing item
     *
     * @param int $item_id Primary key of item_model
     *
     * @return void
     */
    public function edit($item_id) {
        $item = $this->item_model->get_edit_item_by_id($item_id);

        $data = new stdClass();
        $data->page_name = 'Edit';
        $data->referer = $this->getReferer();
        $data->editing = TRUE;
        $data->tag_string = '';

        if (!empty($item->item_tags)) {
            $tags = array();

            foreach ($item->item_tags as $tag) {
                $tags[] = $tag->name;
            }

            $data->tag_string = implode(', ', $tags);
        }

        if ($this->form_validation->run()) {
            $new_post = $this->createItemFromPost();
            if ($this->input->post('timestamp') == 'make_current') {
                $new_post->item_date = time();
            } elseif ($this->input->post('timestamp') == 'make_current_publish') {
                $new_post->item_status = 'publish';
                $new_post->item_date = time();
            }

            $this->item_model->update_item($new_post, $item);

            header('Location: '.$this->input->post('referer'));
        }


        $data->item = $item;
        $this->load->view('admin/_header', $data);
        $this->load->view('admin/edit', $data);
        $this->load->view('admin/_footer');
    }

    /**
     * Create a new item_model
     *
     * @return void
     */
    public function index()
    {
        $data = new stdClass();
        $data->page_name = 'Write';

        if ($this->form_validation->run()) {
            // Prepare data
            $new_post = $this->createItemFromPost();
            $new_post->item_date = time();
            

            if ($this->input->post('draft')) {
                $new_post->item_status = 'draft';
            }

            $this->item_model->add_blog_post($new_post);
            redirect('admin/items', 'location');
        }

        $this->load->view('admin/_header', $data);
        $this->load->view('admin/write', $data);
        $this->load->view('admin/_footer');
    }

    /**
     * Retrieve the HTTP_REFERER for the form. This
     *
     * @return [type] [description]
     */
    private function getReferer() {
        if ($this->input->post('referer')) {
            return $this->input->post('referer');
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
    }

    /**
     * Creates a stdClass Object with data from the $_POST input. The title is
     * sluggified and the tags are stored as an array.
     * 
     * @return stdClass A new "item" record with values taken from $_POST input
     */
    private function createItemFromPost() {
        $new_post = new stdClass();
        $new_post->item_title = $this->input->post('title', TRUE);
        $new_post->item_content = $this->input->post('content');
        $new_post->item_name = url_title($this->input->post('title', TRUE), '-', TRUE);
        $new_post->item_data = array(
            'tags' => array()
        );

        if ($this->input->post('tags', TRUE)) {
            $tags = explode(',', $this->input->post('tags', TRUE));
            $tags = array_filter($tags);
            foreach ($tags as $key => $value) {
                $tags[$key] = trim($value);
            }

            $new_post->item_data['tags'] = $tags;
        }


        return $new_post;
    }
}

/* End of file write.php */
/* Location: ./application/controllers/admin/write.php */
