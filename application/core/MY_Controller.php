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

class MY_Controller extends CI_Controller {
    function __construct() {
        parent::__construct();

        $this->load->library('lifepress');
        $this->load->library('lifepress_item');
        $this->lifepress->compatibility_check();
        $this->lifepress->integrity_check();

        $this->load->file('application/libraries/markdown.php');
        $this->load->helper('text');
        $this->load->helper('url');
        $this->load->library('simplepie');
        $this->load->library('page');
        $this->load->model('feed_model');
        $this->load->model('item_model');
        $this->load->model('tag_model');
        $this->load->model('option_model');
        $this->load->helper('date');
        $this->load->library('auth');

        // Update last access
        $option['option_name'] = 'last_access';
        $option['option_value'] = time();
        $this->option_model->add_option($option);

        // Set config items
        $this->option_model->load_config_options();

        // Initiate pseudo-cron
        $this->lifepress->pseudo_cron();
        $this->data = new StdClass();
        $this->data->user = $this->auth->get_user($this->session->userdata('user_id'));
    }

}


/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
