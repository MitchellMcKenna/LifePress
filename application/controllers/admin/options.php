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

class Options extends MY_Auth_Controller {

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->load->library('form_validation');

        $this->load->helper('file');
        $this->load->helper('inflector');

        $data = new StdClass();
        $data->page_name = 'Options';

        $theme_folder = get_dir_file_info('application/views/themes', FALSE, TRUE);

        $themes = array();
        foreach ($theme_folder as $key => $value) {
            $themes[$key] = new StdClass();
            $themes[$key]->folder = $key;
            $themes[$key]->name = humanize($key);
        }
        $data->themes = $themes;

        if ($_POST) {
            $this->form_validation->set_rules('lifestream_title', 'Lifestream Title', 'trim|required');
            $this->form_validation->set_rules('admin_email', 'Admin Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|matches[new_password_confirm]');
            $this->form_validation->set_rules('new_password_confirm', 'New Password Confirm', 'trim');
            $this->form_validation->set_rules('per page', 'Items Per Page', 'numeric');

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/_header', $data);
                $this->load->view('admin/options', $data);
                $this->load->view('admin/_footer');
            } else {
                //set new password if required
                if ($this->input->post('new_password', TRUE) && $this->input->post('new_password', TRUE) != '') {
                    $password = md5($this->input->post('new_password', TRUE));
                    $this->db->update('users', array('user_pass' => $password), array('ID' => $this->data->user->ID));
                }

                //set admin email
                $this->db->update('users', array('user_email' => $this->input->post('admin_email', TRUE)), array('ID' => $this->data->user->ID));

                unset($_POST['new_password']);
                unset($_POST['new_password_confirm']);

                //save options
                foreach ($_POST as $key => $value) {
                    $option_array[$key]['option_name'] = $key;
                    $option_array[$key]['option_value'] = $value;
                }
                foreach ($option_array as $option) {
                    $this->option_model->add_option($option);
                }
                header('Location: '.$this->config->item('base_url').'admin/options');
            }
        } else {
            $this->load->view('admin/_header', $data);
            $this->load->view('admin/options', $data);
            $this->load->view('admin/_footer');
        }

    }
}

/* End of file options.php */
/* Location: ./application/controllers/admin/options.php */
