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

class Lifepress {

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->set_item('lifepress_version', '0.1');
    }

    function fetch_items()
    {
        if(!ini_get('safe_mode')){
            // Requires php to not be running in "safe mode"
            set_time_limit(0);
        }

        // Soz
        $option['option_name'] = 'last_fetch';
        $option['option_value'] = time();
        $this->CI->option_model->add_option($option);

        $feeds = $this->CI->feed_model->get_active_feeds();
        if ($feeds) {
            foreach ($feeds as $feed) {
                $this->fetch_item($feed);
            }
        }
    }

    /**
     * Fetch items from a feed and store them in the database.
     *
     * @param  Feed_model $feed Feed to fetch items for
     *
     * @return void
     */
    public function fetch_item($feed) {
        if (!is_array($feed)) {
            // Convert feed from stdClass to an array for a consistent interface.
            $feed = (array)$feed;
        }

        $this->CI->simplepie->set_feed_url($feed['feed_url']);
        $this->CI->simplepie->enable_cache(FALSE);
        $this->CI->simplepie->init();

        $items = $this->CI->simplepie->get_items();

        $this->add_new_items($items, $feed);
    }

    function add_new_items($items, $feed)
    {
        foreach ($items as $item) {
            $new->item_data = array();
            $new->item_data['title'] = $item->get_title();
            $new->item_data['permalink'] = $item->get_permalink();
            $new->item_data['content'] = $item->get_content();
            $new->item_data['enclosures'] = $item->get_enclosures();
            $new->item_data['categories'] = $item->get_categories();
            $new->item_data['tags'] = $this->get_tags($new->item_data);
            $new->item_data['image'] = $this->get_image($item->get_content());

            // Build out clean item
            $new->item_status = 'publish';
            $new->item_date = strtotime($item->get_date());
            $new->item_title = $this->CI->security->xss_clean(trim(strip_tags($item->get_title())));
            $new->item_permalink = $item->get_permalink();
            $new->item_content = $this->CI->security->xss_clean(trim(strip_tags($item->get_content())));
            $new->item_name = url_title($new->item_title);
            $new->item_feed_id = $feed['feed_id'];

            $new = $this->extend('pre_db', $feed['feed_domain'], $new, $item);

            $this->CI->item_model->add_item($new);
        }
    }

    function pseudo_cron()
    {
        if ($this->CI->config->item('cron_type') == 'pseudo') {
            // Time in seconds between each pseudo cron
            // If you want more frequent cron updates it's better to rely on "true cron" and to turn off pseudo cron
            $interval = 1800; //1800 = 30 minutes
            if (($this->CI->config->item('last_access') - $this->CI->config->item('last_fetch')) > $interval) {
                $this->fetch_items();
            }
        }
    }

    function extend($method = 'pre_db', $feed_domain = NULL, $item = NULL, $simplepie_object = NULL)
    {
        // We can extend what LifePress does at various points in the import / output process by using plugin architecture
        // See application/plugins for example plugins
        if ($feed_domain && $item) {
            $class = str_replace('.', '_', $feed_domain);
            $plugin = APPPATH.'plugins/'.$class.'.php';

            if (file_exists($plugin)) {
                // Check if already loaded
                if (!method_exists($class, $method)) {
                    include(APPPATH.'plugins/'.$class.'.php');
                }

                $plugin = new $class;

                return $plugin->$method($item, $simplepie_object);
            } else {
                return $item;
            }
        }
    }

    function get_tags($data)
    {
        $tags = '';
        $other_tags = '';
        // Attempt to pull from enclosures
        if (isset($data['enclosures'][0]->categories[0]->term)) {
            $tags = html_entity_decode($data['enclosures'][0]->categories[0]->term);
            $tags = explode(' ', $tags);
        }

        // Attempt to pull from categories
        if (isset($data['categories'][0]->term)) {
            foreach ($data['categories'] as $category) {
                // Sometimes a tag is an ugly url that I don't think we want...
                if (substr($category->term, 0, 7) != 'http://') {
                    $other_tags[] = html_entity_decode($category->term);
                }
            }
        }

        $tags_count = count($tags);
        $other_tags_count = count($other_tags);

        // Lets go with whichever has the most...
        if ($tags_count > $other_tags_count) {
            $tags = $tags;
        } else {
            $tags = $other_tags;
        }

        // Clean before returning...

        return $tags;
    }

    function get_image($html) {
        // Credit: http://zytzagoo.net/blog/2008/01/23/extracting-images-from-html-using-regular-expressions/
        if (stripos($html, '<img') !== false) {
            $imgsrc_regex = '#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im';
            preg_match($imgsrc_regex, $html, $matches);
            unset($imgsrc_regex);
            unset($html);

            if (is_array($matches) && !empty($matches)) {
                return $matches[2];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function is_current_page($page_name)
    {
        // Just a simple tab highlighter for the admin panel
        if ($this->CI->uri->segment(2) == strtolower($page_name)) {
            return true;
        }
    }

    function integrity_check()
    {
        if (!$this->CI->db->table_exists('feeds') || !$this->CI->db->table_exists('items') || !$this->CI->db->table_exists('options') || !$this->CI->db->table_exists('tags') || !$this->CI->db->table_exists('tag_relationships') || !$this->CI->db->table_exists('users')) {
            if (file_exists(BASEPATH.'../.htaccess')) {
                die('Whoo Hoo! Almost there - now just run the <a href="'.$this->CI->config->item('base_url').'admin/install'.'">install script</a>.');
            } else {
                die('Looks like you are missing an .htaccess file...<br />For instructions on creating one, please see <a href="http://github.com/mitchellmckenna/lifepress/">the installation documentation</a>');
            }
        }
    }

    function install_check()
    {
        if ($this->CI->db->table_exists('feeds') || $this->CI->db->table_exists('items') || $this->CI->db->table_exists('options') || $this->CI->db->table_exists('tags') || $this->CI->db->table_exists('tag_relationships') || $this->CI->db->table_exists('users')) {
            die('LifePress is already (or partially) installed.  If you wish to reinstall, please clear your database first.');
        }
    }

    function compatibility_check()
    {
        // Checks php version
        if (version_compare(PHP_VERSION, '5.0.0', '<')) {
            die('Sorry, LifePress is for PHP5 and above.  Your version of PHP is lower than that.  Time to upgrade?');
        }
    }

    function do_search()
    {
        if ($this->CI->input->post('query')) {
            // Strip out stuff and send to page
            $query = urlencode($this->CI->input->post('query'));

            header('Location: '.$this->CI->config->item('base_url').'items/search/'.$query);
        } else {
            show_error('You must type some keywords to search');
        }
    }

    function get_single_item_page($item_id = NULL)
    {
        if ($item_id) {
            // Remove query string (some 3rd party commenting services refer back after commenting)
            $item_id = explode('?', $item_id);
            $item_id = $item_id[0];

            $data->item = $this->CI->item_model->get_public_item_by_id($item_id);
            $data->page_name = $data->item->get_title();
            $data->popular_tags = $this->CI->tag_model->get_all_tags('count', 50);
            $data->all_tags = $this->CI->tag_model->get_all_tags('count');

            $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/_header', $data);
            $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/single', $data);
            $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/_footer', $data);
        }
    }

    function query_items($type = 'site', $query = NULL, $offset = 0, $limit = 10)
    {
        return $this->get_items_page($type, NULL, TRUE, $query, NULL, TRUE, $offset, $limit);
    }

    function get_items_page($type = 'index', $current_page_num = 1, $public = FALSE, $query = NULL, $rss_filter = NULL, $query_items = NULL, $offset = NULL, $limit = NULL)
    {
        // Return raw items for query_items()
        if ($query_items) {
            if ($type == 'site') {
                return $this->CI->item_model->get_items_by_feed_domain($offset, $limit, $query, $public);
            } elseif ($type == 'tag') {
                return $this->CI->item_model->get_items_by_tag($offset, $limit, $query, $public);
            } elseif ($type == 'search') {
                return $this->CI->item_model->get_items_by_search($offset, $limit, $query, $public);
            }
            exit();
        }

        $data->blog_posts = $this->CI->item_model->get_items_by_feed_domain(0, 10, 'lifepress', $public);
        $data->active_feeds = $this->CI->feed_model->get_active_feeds(TRUE);
        $data->popular_tags = $this->CI->tag_model->get_all_tags('count', 50);
        $data->all_tags = $this->CI->tag_model->get_all_tags('count');
        $data->page_type = $type;

        if ($type == 'rss_feed') {
            if (!$rss_filter) {
                $data->page_name = '';
                $data->items = $this->CI->item_model->get_all_items(0, 20, $public);
            } elseif ($rss_filter == 'tag') {
                $data->page_name = '- tagged with '.$query;
                $data->items = $this->CI->item_model->get_items_by_tag(0, 20, $query, $public);
            } elseif ($rss_filter == 'search') {
                $data->page_name = '- search for '.$query;
                $data->items = $this->CI->item_model->get_items_by_search(0, 20, $query, $public);
            } elseif ($rss_filter == 'site') {
                $data->page_name = '- imported from '.$query;
                $data->items = $this->CI->item_model->get_items_by_feed_domain(0, 20, $query, $public);
            }

            $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/rss_feed', $data);
        } elseif ($type == 'static_page') {
            $page = $this->CI->uri->segment(2);

            $data->page_name = ucwords($page);

            if (file_exists('application/views/themes/'.$this->CI->config->item('theme').'/'.$page.'.php')) {
                $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/_header', $data);
                $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/'.$page, $data);
                $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/_footer', $data);
            } else {
                show_404();
            }
        } else {
            $this->CI->page->SetItemsPerPage($this->CI->config->item('per_page'));
            $this->CI->page->SetQueryStringVar('page');
            $this->CI->page->SetLinksFormat('&lsaquo;',' ','&rsaquo;');
            $this->CI->page->SetLinksToDisplay(10);
            $this->CI->page->SetCurrentPage($current_page_num);

            if ($public) {
                $admin = '';
            } else {
                $admin = 'admin/';
            }

            // Conditionals depending on page type
            if ($type == 'index') {
                $data->page_name = 'Home';

                $this->CI->page->SetItemCount($this->CI->item_model->count_all_items($public));

                if ($public) {
                    $this->CI->page->SetLinksHref($this->CI->config->item('base_url').$admin);
                } else {
                    $this->CI->page->SetLinksHref($this->CI->config->item('base_url').$admin.'items/');
                }

                $data->items = $this->CI->item_model->get_all_items($this->CI->page->GetOffset(), $this->CI->page->GetSqlLimit(), $public);
            } elseif ($type == 'search') {
                $data->page_name = 'Items Search';

                $this->CI->page->SetItemCount($this->CI->item_model->count_items_by_search($query, $public));
                $this->CI->page->SetLinksHref($this->CI->config->item('base_url').$admin.'items/search/'.$query.'/');

                $data->items = $this->CI->item_model->get_items_by_search($this->CI->page->GetOffset(), $this->CI->page->GetSqlLimit(), $query, $public);
            } elseif ($type == 'tag') {
                $data->page_name = 'Items Tag';

                $this->CI->page->SetItemCount($this->CI->item_model->count_items_by_search($query, $public));
                $this->CI->page->SetLinksHref($this->CI->config->item('base_url').$admin.'items/tag/'.$query.'/');

                $data->items = $this->CI->item_model->get_items_by_tag($this->CI->page->GetOffset(), $this->CI->page->GetSqlLimit(), $query, $public);
            } elseif ($type == 'site') {
                $data->page_name = 'Items Site';

                $this->CI->page->SetItemCount($this->CI->item_model->count_items_by_feed_domain($query, $public));
                $this->CI->page->SetLinksHref($this->CI->config->item('base_url').$admin.'items/site/'.$query.'/');

                $data->items = $this->CI->item_model->get_items_by_feed_domain($this->CI->page->GetOffset(), $this->CI->page->GetSqlLimit(), $query, $public);
            }

            $data->page_query = $query;

            if ($query && $type == 'search') {
                $data->query = $query;
            }

            if ($query && $type == 'tag') {
                $data->tag = urldecode($query);
            }

            if ($query && $type == 'site') {
                $data->site = $query;
            }

            // Load view
            if ($public) {
                $data->pages = $this->CI->page->GetPageLinks();

                $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/_header', $data);
                $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/items', $data);
                $this->CI->load->view('themes/'.$this->CI->config->item('theme').'/_footer', $data);
            } else {
                $data->pages = $this->CI->page->GetPageLinks();

                $this->CI->load->view('admin/_header', $data);
                $this->CI->load->view('admin/items', $data);
                $this->CI->load->view('admin/_footer');
            }
        }
    }

}

/* End of file lifepress.php */
/* Location: ./application/libraries/lifepress.php */
