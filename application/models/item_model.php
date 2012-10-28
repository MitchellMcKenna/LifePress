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

class Item_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function _process($items, $return_single = FALSE)
    {
        if ($items) {
            foreach ($items as $key => $value) {
                $new_item = new Lifepress_item();

                // Item feed components
                $new_item->feed_id = $items[$key]->feed_id;
                $new_item->feed_title = $items[$key]->feed_title;
                $new_item->feed_icon = $items[$key]->feed_icon;
                $new_item->feed_url = $items[$key]->feed_url;
                $new_item->feed_data = $items[$key]->feed_data;
                $new_item->feed_status = $items[$key]->feed_status;
                $new_item->feed_domain = $items[$key]->feed_domain;

                // Standard item components
                $new_item->ID = $items[$key]->ID;
                $new_item->item_date = $items[$key]->item_date;

                // Convert Markdown
                $new_item->item_content = markdown($items[$key]->item_content);

                $new_item->item_title = $this->_autolink($items[$key]->item_title);
                $new_item->item_original_permalink = $items[$key]->item_permalink;
                $new_item->item_permalink = site_url(array('post', $new_item->ID, $items[$key]->item_name));
                $new_item->item_status = $items[$key]->item_status;
                $new_item->item_name = $items[$key]->item_name;
                $new_item->item_data = unserialize($items[$key]->item_data);
                $new_item->item_tags = $this->get_tags($items[$key]->ID);

                // Make adjustments if blog post
                if (!$items[$key]->feed_id) {
                    $new_item->feed_icon = '/favicon.ico';
                    $url = parse_url($this->config->item('base_url'));
                    if (substr($url['host'], 0, 4) == 'www.') {
                        $new_item->feed_domain = substr($url['host'], 4);
                    } else {
                        $new_item->feed_domain = $url['host'];
                    }
                }

                $new_item->feed_class = str_replace('.','_',$new_item->feed_domain);

                // Extended item components
                if ($new_item->feed_id) {
                    $new_item = $this->lifepress->extend('pre_display', $new_item->feed_domain, $new_item);
                }

                // Custom item components
                $new_item->nice_date = timespan($items[$key]->item_date);
                if ($new_item->item_date < strtotime('1 day ago')) {
                    $new_item->human_date = date('F j Y, g:ia', $items[$key]->item_date);
                } else {
                    $new_item->human_date = $new_item->nice_date.' ago';
                }

                $items[$key] = $new_item;
            }

            if ($return_single) {
                return $items[0];
            } else {
                return $items;
            }
        } else {
            return false;
        }
    }

    function _autolink($text)
    {
        return preg_replace('/(?<!\S)((http(s?):\/\/)|(www\.))+([\w.\/&=?\-~%;]+)\b/i', '<a href="http$3://$4$5" rel="external">http$3://$4$5</a>', $text);
    }

    function get_public_item_by_id($item_id)
    {
        $item = $this->db
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left outer')
            ->get_where('items', array('ID' => $item_id, 'item_status' => 'publish'))
            ->result();

        if ($item) {
            return $this->_process($item, TRUE);
        } else {
            show_404();
        }
    }

    function count_all_items($public = FALSE)
    {
        if ($public) {
            $where = array('item_status' => 'publish');
        } else {
            $where = array('item_status !=' => 'deleted');
        }
        return $this->db
            ->select('ID')
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left outer')
            ->get_where('items', $where)
            ->num_rows();
    }

    function get_all_items($offset = 0, $limit = 10, $public = FALSE)
    {
        if ($public) {
            $where = array('item_status' => 'publish');
        } else {
            $where = array('item_status !=' => 'deleted');
        }
        $items = $this->db
            ->limit($limit)
            ->offset($offset)
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left outer')
            ->order_by('item_date', 'DESC')
            ->get_where('items', $where)
            ->result();

        return $this->_process($items);
    }

    function count_items_by_search($query, $public = FALSE)
    {
        if ($public) {
            $where = 'item_status = "publish"';
        } else {
            $where = 'item_status != "deleted"';
        }

        // Add query wild cards and replace spaces (+ sign) with wildcards
        $query = '%' . str_replace(' ', '%', $query) . '%';
        $query = $this->db->escape($query);

        return $this->db
            ->select('*')
            ->where($where)
            ->where("(items.item_title LIKE " . $query . " OR items.item_content LIKE " . $query . ")")
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')
            ->order_by('item_date', 'desc')
            ->get('items')
            ->num_rows();
    }

    function get_items_by_search($offset = 0, $limit = 10, $query, $public = FALSE)
    {
        if ($public) {
            $where = 'item_status = "publish"';
        } else {
            $where = 'item_status != "deleted"';
        }

        // Add query wild cards and replace spaces with wildcards
        $query = '%' . str_replace(' ', '%', $query) . '%';
        $query = $this->db->escape($query);

        $items = $this->db
            ->select('*')
            ->where($where)
            ->where("(items.item_title LIKE " . $query . " OR items.item_content LIKE " . $query . ")")
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')
            ->order_by('item_date', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get('items')
            ->result();

        return $this->_process($items);
    }

    function count_items_by_tag($query, $public = FALSE)
    {
        if ($public) {
            $where = array('tags.slug' => $query, 'item_status' => 'publish');
        } else {
            $where = array('tags.slug' => $query, 'item_status !=' => 'deleted');
        }

        return $this->db
            ->join('tag_relationships', 'items.ID = tag_relationships.item_id')
            ->join('tags', 'tags.tag_id = tag_relationships.tag_id', 'left outer')
            ->get_where('items', $where)
            ->num_rows();
    }

    function get_items_by_tag($offset = 0, $limit = 10, $query, $public = FALSE)
    {
        if ($public) {
            $where = array('tags.slug' => $query, 'item_status' => 'publish');
        } else {
            $where = array('tags.slug' => $query, 'item_status !=' => 'deleted');
        }

        $items = $this->db
            ->join('tag_relationships', 'items.ID = tag_relationships.item_id')
            ->join('tags', 'tags.tag_id = tag_relationships.tag_id')
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left outer')
            ->order_by('item_date', 'DESC')
            ->limit($limit, $offset)
            ->get_where('items', $where)
            ->result();

        return $this->_process($items);
    }

    function count_items_by_feed_domain($feed_domain, $public = FALSE)
    {
        if ($public) {
            if ($feed_domain == 'lifepress') {
                $where = array('item_status' => 'publish', 'item_feed_id' => 0);
            } else {
                $where = array('item_status' => 'publish', 'feed_domain' => $feed_domain);
            }
        } else {
            if ($feed_domain == 'lifepress') {
                $where = array('item_status !=' => 'deleted', 'item_feed_id' => 0);
            } else {
                $where = array('item_status !=' => 'deleted', 'feed_domain' => $feed_domain);
            }
        }

        return $this->db
            ->select('ID')
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left outer')
            ->get_where('items', $where)
            ->num_rows();
    }

    function get_items_by_feed_domain($offset = 0, $limit = 10, $feed_domain, $public = FALSE)
    {
        $url = parse_url($this->config->item('base_url'));

        if (substr($url['host'], 0, 4) == 'www.') {
            $domain = substr($url['host'], 4);
        } else {
            $domain = $url['host'];
        }

        if ($public) {
            if ($feed_domain == 'lifepress' || $feed_domain == $domain) {
                $where = array('item_status' => 'publish', 'item_feed_id' => 0);
            } else {
                $where = array('item_status' => 'publish', 'feed_domain' => $feed_domain);
            }
        } else {
            if ($feed_domain == 'lifepress' || $feed_domain == $domain) {
                $where = array('item_status !=' => 'deleted', 'item_feed_id' => 0);
            } else {
                $where = array('item_status !=' => 'deleted', 'feed_domain' => $feed_domain);
            }
        }

        $items = $this->db
            ->limit($limit)
            ->offset($offset)
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left outer')
            ->order_by('item_date', 'DESC')
            ->get_where('items', $where)
            ->result();

        return $this->_process($items);
    }

    function get_tags($item_id = NULL)
    {
        return $this->db
            ->join('tag_relationships', 'tags.tag_id = tag_relationships.tag_id')
            ->get_where('tags', array('item_id' => $item_id))
            ->result();
    }

    function add_item($item = NULL)
    {
        // We assume that if an item has the exact same timestamp and origin as one in the db, it's a dupe
        if (!$this->db
            ->join('feeds', 'feeds.feed_id = items.item_feed_id')
            ->get_where('items', array('item_feed_id' => $item->item_feed_id, 'item_date' => $item->item_date))
            ->row()
        ) {
            $tags = $item->item_data['tags'];
            $item->item_data = serialize($item->item_data);
            $this->db->insert('items', $item);
            $this->tag_item($tags, $this->db->insert_id());
        }
    }

    function add_blog_post($item = NULL)
    {
        $tags = !empty($item->item_data['tags']) ? $item->item_data['tags'] : array();

        $item->item_data = serialize($item->item_data);

        $this->db->insert('items', $item);

        $this->tag_item($tags, $this->db->insert_id());
    }

    function tag_item($tags = array(), $item_id = NULL)
    {
        $this->db->delete('tag_relationships', array('item_id' => $item_id));

        // Nuke all item tags from orbit. It's the only way to be sure.
        $this->clean_tags();

        if (isset($tags[0])) {
            foreach ($tags as $tag) {
                // Lets just get rid of some typical meh stuff from tags
                $disallow = array('(',')',',','.','*','\'','"','|');
                $tag = $this->security->xss_clean(str_replace($disallow,'',$tag));

                $slug = url_title($tag);

                // For unicode characters the slug might be blank, so...
                if ($slug == '') {
                    $slug = urlencode($tag);
                }

                if (!$this->db->get_where('tags', array('slug' => $slug))->row()) {
                    $new->name = $tag;
                    $new->slug = $slug;
                    $this->db->insert('tags', $new);
                }

                $tag = $this->db->get_where('tags', array('slug' => $slug))->row();

                $criteria = array('tag_id' => $tag->tag_id, 'item_id' => $item_id);

                if (!$this->db->get_where('tag_relationships', $criteria)->row()) {
                    $this->db->insert('tag_relationships', $criteria);
                }

                // Update tag count
                $count = $this->db
                    ->get_where('tag_relationships', array('tag_id' => $tag->tag_id))
                    ->num_rows();

                $this->db->update('tags', array('count' => $count), array('tag_id' => $tag->tag_id));
            }
        }
    }

    function clean_tags()
    {
        // Sweeps through your tags and removes ones that are not associated with
        // any posts (e.g. for when you delete tags when editing an item).
        $tags = $this->db
            ->select('*, tags.tag_id AS tag_id')
            ->join('tag_relationships', 'tag_relationships.tag_id = tags.tag_id', 'left outer')
            ->get('tags')
            ->result();

        foreach ($tags as $tag) {
            $count = $this->db
                ->get_where('tag_relationships', array('tag_id' => $tag->tag_id))
                ->num_rows();

            $this->db->update('tags', array('count' => $count), array('tag_id' => $tag->tag_id));

            if ($tag->item_id == '') {
                $this->db->delete('tags', array('tag_id' => $tag->tag_id));
            }
        }
    }

    function delete_item($item_id)
    {
        $this->db->update('items', array('item_status' => 'deleted'), array('ID' => $item_id));
    }

    function get_edit_item_by_id($item_id)
    {
        $item = $this->db->get_where('items', array('ID' => $item_id))->row();
        $item->item_tags = $this->get_tags($item->ID);
        $item->item_data = unserialize($item->item_data);
        return $item;
    }

    function update_item($item = NULL, $old = NULL)
    {
        $tags = $item->item_data['tags'];

        $item->item_data = serialize($item->item_data);

        $this->db->update('items', $item, array('ID' => $old->ID));

        $this->tag_item($tags, $old->ID);
    }
}

/* End of file item_model.php */
/* Location: ./application/models/item_model.php */
