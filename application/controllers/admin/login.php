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

class Login extends MY_Controller {

    function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');


        if (!$this->data->user === FALSE) {
            header('Location: '.$this->config->item('base_url').'admin');
        }
    }

    function index() {
        $data = new StdClass();
        $data->page_name = 'Login';
        $this->load->view('admin/_header', $data);
        if ($_POST) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $rules = array(
                array('field' => 'username', 'rules' => 'required|trim'),
                array('field' => 'password', 'rules' => 'required|trim')
            );

            $this->form_validation->set_rules($rules);

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/login', $data);
            } else {
                // Passed form_validation but need to check if can log in
                if (!$this->auth->try_login(array('user_login' => $this->input->post('username', TRUE), 'user_pass' => md5($this->input->post('password', TRUE))))) {
                    // Authentication error
                    $data->errors = 'Usernames / Password incorrect';
                    $this->load->view('admin/login', $data);
                } else {
                    header('Location: '.$this->config->item('base_url').'admin');
                }
            }
        } else {
            $this->load->view('admin/login');
        }

        $this->load->view('admin/_footer');
    }

    function forgot() {
        $data = new StdClass();
        $data->page_name = 'Password Reset';
        $this->load->view('admin/_header', $data);
        if ($_POST) {
            $this->form_validation->set_rules('email', 'Email', "required|trim|valid_email|callback__is_admin_email");
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/forgot', $data);
            } else {
                // Change activation key and send a mail
                $key = substr(md5(time().rand(1,100)),0,10);
                $user = new stdClass();
                $user->user_activation_key = $key;
                $this->db->update('users', $user, array('user_email' => $this->input->post('email', TRUE)));
                $link = $this->config->item('base_url').'admin/login/reset_password/'.$key;

                // Send email
                $this->load->library('email');

                $this->email->from($this->input->post('email', TRUE), 'LifePress');
                $this->email->to($this->input->post('email', TRUE));

                $this->email->subject('[LifePress] Reset Password');
                $this->email->message('You have initiated a password reset request.

Click this link to reset your password:

'.$link.'

If you feel you have received this message in error, ignore this message and do not click the link.');

                $this->email->send();
                $data->success = TRUE;
                $this->load->view('admin/forgot',$data);
            }
        } else {
            $this->load->view('admin/forgot');
        }

        $this->load->view('admin/_footer');
    }

    function reset_password($key)
    {
        if ($user = $this->db->get_where('users', array('user_activation_key' => $key))->row()) {
            // Reset the activation key
            $key = substr(md5(time().rand(1,100)),0,10);
            $edited = new stdClass();
            $edited->user_activation_key = $key;

            // Reset users password
            $password = substr(md5(time().rand(1,100).$this->config->item('lifestream_title')), 0, 8);
            $edited->user_pass = md5($password);

            $this->db->update('users', $edited, array('user_email' => $user->user_email));

            $this->load->library('email');

            $this->email->from($user->user_email, 'LifePress');
            $this->email->to($user->user_email);

            $this->email->subject('[LifePress] New Password');
            $this->email->message('You initiated and confirmed a password reset request.

Your login details are as follows:

Username: '.$user->user_login.'
Password: '.$password.'

Thanks and have fun!');

            $this->email->send();

            die('Your password was reset - please check your email');
        } else {
            die("Uh uh uh, you didn't say the magic word!");
        }
    }

    function _is_admin_email($email)
    {
        if ($this->db->get_where('users', array('user_email' => $email))->row()) {
            return true;
        } else {
            $this->form_validation->set_message('_is_admin_email', 'That email is not registered with LifePress');
            return false;
        }
    }

    function bye()
    {
        $this->auth->logout();
    }
}

/* End of file login.php */
/* Location: ./application/controllers/admin/login.php */
