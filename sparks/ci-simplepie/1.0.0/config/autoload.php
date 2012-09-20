<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$autoload['libraries'] = array('cisimplepie');


spl_autoload_register('simplepie_autoload');

/**
 * Autoloader
 *
 * @param string $class The name of the class to attempt to load.
 */
function simplepie_autoload($class) {
    // Only load the class if it starts with "SimplePie"
    if (strpos($class, 'SimplePie') !== 0) {
        return;
    }

    $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'third_party' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    include $filename;
}

?>