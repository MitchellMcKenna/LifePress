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

class Option_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function _process($options)
    {
        return $options;
    }

    function load_config_options()
    {
        $options = $this->db->get_where('options', array('autoload' => 'yes'))->result();

        foreach ($options as $option) {
            $this->config->set_item($option->option_name, $option->option_value);
        }

        // Add theme folder
        $theme_folder = $this->config->item('base_url') . 'application/views/themes/' . $this->config->item('theme') . '/';
        $this->config->set_item('theme_folder', $theme_folder);
    }

    function add_option($option = NULL)
    {
        if ($this->db
            ->get_where('options', array('option_name' => $option['option_name']), 1)
            ->num_rows() > 0
        ) {
            $this->db->update('options', $option, array('option_name' => $option['option_name']));
        } else {
            $this->db->insert('options', $option);
        }
    }

}

/* End of file option_model.php */
/* Location: ./application/models/options_model.php */
