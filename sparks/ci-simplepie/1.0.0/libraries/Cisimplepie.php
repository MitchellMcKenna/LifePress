<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Simplepie Class
 *
 * Use simplepie in your CodeIgniter application
 *
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Libraries
 * @author Peter Nijssen <peter@peternijssen.nl>
 * @copyright Copyright (c) 2012, Peter Nijssen
 * @license MIT
 * @link https://github.com/PTish/Codeigniter-sparks-simplepie
 * @since 1.0
 * @version 1.0
 */
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'third_party' . DIRECTORY_SEPARATOR . 'SimplePie.php');

/**
 * We extend the SimplePie class to do some basic configuration and to make sure everything is loaded.
 * Since we already got a class named SimplePie, we need to use Cisimplepie as a class name :(
 */
class Cisimplepie extends SimplePie {

    /**
     * Setup SimplePie
     * @param array $config Array with config values. Use simplepie function names
     */
    public function __construct($config = array()) {
        parent::__construct();

        if (!empty($config)) {
            $this->initialize($config);
        }
    }

    /**
     * Initialize SimplePie
     * @param array $config Array with config values. Use simplepie function names
     */
    private function initialize($config = array()) {
        foreach($config as $k => $v) {
            $this->{$k}($v);
        }
    }
}

?>