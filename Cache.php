<?php
/* Generated by neoan3-cli */

namespace Neoan3\Apps;


/**
 * Class Cache
 *
 * @package Neoan3\Apps
 */
class Cache {
    /**
     * @var bool
     */
    private static $_caching = false;
    /**
     * @var string
     */
    private static $_parameterString = '_';

    /**
     * Activate / deactivate caching
     * You may place a strtotime expression in here
     *
     * @param bool|string $delay
     */
    static function setCaching($delay) {
        self::$_caching = $delay;
        if($delay) {
            self::extractParameters();
            self::tryCaching();
        }
    }

    /**
     * Accounting for various get-parameters
     */
    private static function extractParameters() {
        if(!empty($_GET)) {
            foreach($_GET as $param) {
                self::$_parameterString .= str_replace(['/', '\\'], ['-'], $param) . '_';
            }
        }
    }

    /**
     * Providing cached content & invalidating in limited time
     */
    private static function tryCaching() {
        $folder = self::findComponent();
        if(file_exists($folder . self::$_parameterString . 'cached.html')) {
            $file = $folder . self::$_parameterString . 'cached.html';
            include $file;
            echo '<!-- cached neoan3 output -->';
            // performance: invalidate after
            if(is_string(self::$_caching) && strtotime(self::$_caching) < filemtime($file)) {
                unlink($file);
                echo '<!-- cache cleared/dev: reload -->';
            }
            exit();
        } else {
            ob_start();
        }
    }

    /**
     * Invalidates all cached files
     */
    static function invalidateAll() {
        if($root = self::findRoot()) {
            $components = scandir($root . DIRECTORY_SEPARATOR . 'component');
            foreach($components as $component) {
                $path = $root . DIRECTORY_SEPARATOR . 'component' . DIRECTORY_SEPARATOR . $component;
                if($component != '.' && $component !== '..') {
                    $list = glob($path . DIRECTORY_SEPARATOR . '*_cached.html');
                    foreach($list as $file) {
                        unlink($file);
                    }
                }
            }

        }
    }

    /**
     * Invalidates all cached files of a component
     * @param $ctrl
     */
    static function invalidate($ctrl) {
        if($root = self::findRoot()) {
            $path = $root . DIRECTORY_SEPARATOR . 'component' . DIRECTORY_SEPARATOR . $ctrl . DIRECTORY_SEPARATOR;
            $list = glob($path . DIRECTORY_SEPARATOR . '*_cached.html');
            foreach($list as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Writing cache-file in respective component-folder
     */
    static function write() {
        if(self::$_caching) {
            $cache = ob_get_contents();

            $folder = self::findComponent();
            file_put_contents($folder . self::$_parameterString . 'cached.html', $cache);
            ob_end_flush();
        }

    }

    /**
     * @return bool|string|null
     */
    private static function findRoot() {
        $root = null;
        $directory = dirname(__FILE__);
        do {
            $directory = dirname($directory);
            $composer = $directory . '/composer.json';
            if(file_exists($composer)) {
                $root = $directory;
            }
        } while(is_null($root) && $directory != DIRECTORY_SEPARATOR);
        if(!is_null($root)) {
            return $root;
        }
        return false;
    }

    /**
     * @return bool|string
     */
    private static function findComponent() {
        $trace = debug_backtrace();
        foreach($trace as $i => $file) {
            if($i > 0 && $file['file'] !== __FILE__ && strpos($file['file'], '.ctrl.php') !== false) {
                $parts = explode(DIRECTORY_SEPARATOR, $file['file']);
                $folder = substr($file['file'], 0, -1 * strlen(end($parts)));
                return $folder;
            }
        }
        return false;
    }
}
