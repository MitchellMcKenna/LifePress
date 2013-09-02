<?php defined('BASEPATH') OR exit('No direct script access allowed');
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
 * @package     LifePress
 * @author      Mitchell McKenna <mitchellmckenna@gmail.com>
 * @copyright   Copyright (c) 2012, Mitchell McKenna
 * @license     http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

class Migration_Install_Lifepress extends CI_Migration {

    public function up()
    {
        // Create feeds table
        $this->dbforge->add_field(array(
            'feed_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'feed_title' => array(
                'type' => 'TEXT'
            ),
            'feed_icon' => array(
                'type' => 'VARCHAR',
                'constraint' => 255
            ),
            'feed_url' => array(
                'type' => 'TEXT'
            ),
            'feed_data' => array(
                'type' => 'LONGTEXT'
            ),
            'feed_status' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'active'
            ),
            'feed_domain' => array(
                'type' => 'VARCHAR',
                'constraint' => 255
            ),
        ));
        $this->dbforge->add_key('feed_id', TRUE);
        $this->dbforge->add_key('feed_status');
        $this->dbforge->create_table('feeds');

        // Create items table
        $this->dbforge->add_field(array(
            'ID' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'item_date' => array(
                'type' => 'BIGINT'
            ),
            'item_content' => array(
                'type' => 'LONGTEXT'
            ),
            'item_title' => array(
                'type' => 'TEXT'
            ),
            'item_permalink' => array(
                'type' => 'VARCHAR',
                'constraint' => 255
            ),
            'item_status' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'publish'
            ),
            'item_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 200,
                'default' => ''
            ),
            'item_parent' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => '0'
            ),
            'item_data' => array(
                'type' => 'LONGTEXT'
            ),
            'item_feed_id' => array(
                'type' => 'INT',
                'constraint' => 11
            ),
        ));
        $this->dbforge->add_key('ID', TRUE);
        $this->dbforge->add_key('item_name');
        $this->dbforge->add_key(array('item_status', 'item_date', 'ID'));
        $this->dbforge->create_table('items');

        // Create the options table
        $this->dbforge->add_field(array(
            'option_id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'option_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 64
            ),
            'option_value' => array(
                'type' => 'LONGTEXT'
            ),
            'autoload' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'yes'
            ),
        ));
        $this->dbforge->add_key('option_id', TRUE);
        $this->dbforge->add_key('option_name', TRUE);
        $this->dbforge->add_key('option_name');
        $this->dbforge->create_table('options');

        // Create the tags table
        $this->dbforge->add_field(array(
            'tag_id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 55
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => 200
            ),
            'count' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'default' => 0
            ),
        ));
        $this->dbforge->add_key('tag_id', TRUE);
        $this->dbforge->add_key('slug');
        $this->dbforge->create_table('tags');

        // Create tag_relationships table
        $this->dbforge->add_field(array(
            'item_id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
            ),
            'tag_id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
            ),
        ));
        $this->dbforge->add_key('item_id', TRUE);
        $this->dbforge->add_key('tag_id', TRUE);
        $this->dbforge->create_table('tag_relationships');

        // Create the users table
        $this->dbforge->add_field(array(
            'ID' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_login' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
                'default' => ''
            ),
            'user_pass' => array(
                'type' => 'VARCHAR',
                'constraint' => 64,
                'default' => ''
            ),
            'user_email' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => ''
            ),
            'user_activation_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
                'default' => ''
            ),
        ));
        $this->dbforge->add_key('ID', TRUE);
        $this->dbforge->add_key('user_login');
        $this->dbforge->create_table('users');
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
        $this->dbforge->drop_table('tag_relationships');
        $this->dbforge->drop_table('tags');
        $this->dbforge->drop_table('options');
        $this->dbforge->drop_table('items');
        $this->dbforge->drop_table('feeds');
    }
}

/* End of file 001_install_lifepress.php */
/* Location: ./application/migrations/001_install_lifepress.php */
