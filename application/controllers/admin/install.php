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

class Install extends CI_Controller {

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->load->library('lifepress');
        $this->lifepress->compatibility_check();
        $this->lifepress->install_check();

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('lifestream_title', 'Lifestream Title', 'trim|required');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[20]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        if ($this->form_validation->run() !== FALSE) {
            $this->load->library('migration');
            $this->migration->version(1);

            $cron_key = substr(md5(time() . rand(1,100) . $this->input->post('lifestream_title', TRUE).time()), 0, 8);

            $options = array();
            $options['lifestream_title'] = array('option_name' => 'lifestream_title', 'option_value' => $this->input->post('lifestream_title', TRUE));
            $options['admin_email'] = array('option_name' => 'admin_email', 'option_value' => $this->input->post('email', TRUE));
            $options['per_page'] = array('option_name' => 'per_page', 'option_value' => 9);
            $options['cron_type'] = array('option_name' => 'cron_type', 'option_value' => 'pseudo');
            $options['cron_key'] = array('option_name' => 'cron_key', 'option_value' => $cron_key);
            $options['theme'] = array('option_name' => 'theme', 'option_value' => 'sandbox');

            $this->load->model('option_model');

            foreach ($options as $option) {
                $this->option_model->add_option($option);
            }

            // Add user
            $username = $this->input->post('username', TRUE);
            $password = substr(md5(time().rand(1,100).$this->input->post('lifestream_title', TRUE)), 0, 8);

            $user->user_login = $username;
            $user->user_pass = md5($password);
            $user->user_email = $this->input->post('email', TRUE);
            $user->user_activation_key = md5(time());
            $this->db->insert('users', $user);

            // Send email
            $url = $this->config->item('base_url');
            $this->load->library('email');

            $this->email->from($this->input->post('email', TRUE), 'LifePress');
            $this->email->to($this->input->post('email', TRUE));
            $this->email->subject('[LifePress] Welcome to LifePress');
            $this->email->message('You have successfully installed LifePress!

Your login details are as follows:

Username: ' . $username . '
Password: ' . $password . '

Your LifePress site: ' . $url . '

Thanks and have fun!');

            $this->email->send();
            $data->success = TRUE;
            $data->username = $username;
            $data->password = $password;
        }

        $data->page_name = 'Install';

        $this->load->view('admin/_header', $data);
        $this->load->view('admin/install', $data);
        $this->load->view('admin/_footer');
    }
}

/* End of file install.php */
/* Location: ./application/controllers/admin/install.php */
