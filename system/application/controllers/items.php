<?php if (!defined('BASEPATH')) exit('No direct access allowed.');
/**
 * Sweetcron
 *
 * Self-hosted lifestream software based on the CodeIgniter framework.
 *
 * Copyright (c) 2008, Yongfook.com & Edible, Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Sweetcron
 * @copyright 2008, Yongfook.com & Edible, Inc.
 * @author Yongfook
 * @link http://sweetcron/ Sweetcron
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

class Items extends Public_Controller {

	function __construct()
	{
		parent::Public_Controller();	
	}
	
	function do_search()
	{
		$this->sweetcron->do_search();	
	}

	function index()
	{		
		header('Location: '.$this->config->item('base_url'));
	}

	function search($query = NULL)
	{	
		if ($query) {	
			$this->sweetcron->get_items_page('search', $this->uri->segment(5,1), TRUE, $query);
		} else {
			header('Location: '.$this->config->item('base_url'));
		}
	}

	function tag($tag = NULL)
	{
		if ($tag) {
			$this->sweetcron->get_items_page('tag', $this->uri->segment(5,1), TRUE, $tag);
		} else {
			header('Location: '.$this->config->item('base_url'));			
		}
	}

	function site($feed_domain = NULL)
	{
		if ($feed_domain) {
			$this->sweetcron->get_items_page('site', $this->uri->segment(5,1), TRUE, $feed_domain);
		} else {
			header('Location: '.$this->config->item('base_url'));			
		}
	}
	
	function view($item_id = NULL)
	{
		if ($item_id) {
			$this->sweetcron->get_single_item_page($item_id);
		} else {
			header('Location: '.$this->config->item('base_url'));			
		}			
	}
	
}
?>