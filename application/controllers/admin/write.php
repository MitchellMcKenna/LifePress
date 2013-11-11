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

    function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
    }

    function index()
    {
        $data = new StdClass();
        $data->page_name = 'Write';
        $new_post = new StdClass();

        if ($this->uri->segment(3) == 'edit') {
            $data->referer = $this->config->item('base_url') . 'admin';

            $data->editing = TRUE;

            // Get item
            $data->item = $this->item_model->get_edit_item_by_id($this->uri->segment(4));

            if (isset($data->item->item_tags[0])) {
                foreach ($data->item->item_tags as $tag) {
                    $tags[] = $tag->name;
                }
                $data->tag_string = implode(', ', $tags);
            }

            $new_post->item_data = $data->item->item_data;
        }

        if ($_POST) {
            $this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', 'Date', 'trim|xss_clean');
            $this->form_validation->set_rules('content', 'Content', 'trim');
            $this->form_validation->set_rules('tags', 'Tags', 'trim|xss_clean');

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/_header', $data);
                $this->load->view('admin/write', $data);
            } else {
                // Prepare data
                if (!isset($data->editing)) {
                    $new_post->item_data = array();
                }

                if ($this->input->post('tags', TRUE)) {
                    $tags = explode(',', $this->input->post('tags', TRUE));

                    foreach ($tags as $key => $value) {
                        $tags[$key] = trim($value);
                    }

                    if (!empty($tags)) {
                        $new_post->item_data['tags'] = $tags;
                    }
                } else {
                    $new_post->item_data['tags'] = array();
                }

                $new_post->item_title = $this->input->post('title', TRUE);

                if (!$this->input->post('content')) {
                    $new_post->item_content = '';
                } else {
                    $new_post->item_content = $this->input->post('content');
                }

                if ($this->input->post('save_edit') == 'true') {
                    // Save edits
                    if ($this->input->post('timestamp') == 'make_current') {
                        $new_post->item_date = time();
                    } elseif ($this->input->post('timestamp') == 'make_current_publish') {
                        $new_post->item_status = 'publish';
                        $new_post->item_date = time();
                    }

                    $this->item_model->update_item($new_post, $data->item);

                    header('Location: '.$this->input->post('referer'));
                } else {
                    // Add new item
                    $new_post->item_name = url_title($this->input->post('title', TRUE));
                    $new_post->item_date = time();

                    if ($this->input->post('draft') == 'true') {
                        $new_post->item_status = 'draft';
                    }

                    $this->item_model->add_blog_post($new_post);

                    header('Location: '.$this->config->item('base_url').'admin/items');
                }
            }
        } else {
            $this->load->view('admin/_header', $data);
            $this->load->view('admin/write', $data);
        }

        $this->load->view('admin/_footer');
    }
}

/* End of file write.php */
/* Location: ./application/controllers/admin/write.php */
