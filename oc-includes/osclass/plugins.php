<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    class Plugins
    {
        private static $hooks ;

        function __construct() {}

        static function runHook($hook)
        {
            $args = func_get_args();
            array_shift($args);
            if(isset(self::$hooks[$hook])) {
                for($priority = 0;$priority<=10;$priority++) {
                    if(isset(self::$hooks[$hook][$priority]) && is_array(self::$hooks[$hook][$priority])) {
                        foreach(self::$hooks[$hook][$priority] as $fxName) {
                            if(is_callable($fxName)) {
                                call_user_func_array($fxName, $args);
                            }
                        }
                    }
                }
            }
        }

        static function applyFilter($hook, $content)
        {
            if(isset(self::$hooks[$hook])) {
                for($priority = 0;$priority<=10;$priority++) {
                    if(isset(self::$hooks[$hook][$priority]) && is_array(self::$hooks[$hook][$priority])) {
                        foreach(self::$hooks[$hook][$priority] as $fxName) {
                            if(is_callable($fxName)) {
                                $content = call_user_func($fxName, $content);
                            }
                        }
                    }
                }
            }
            return $content;
        }

        static function isInstalled($plugin)
        {
            if( in_array($plugin, self::listInstalled()) ) {
                return true ;
            }

            return false ;
        }

        static function isEnabled($plugin)
        {
            if( in_array($plugin, self::listEnabled()) ) {
                return true ;
            }

            return false ;
        }

        static function listAll()
        {
            $plugins = array();
            $pluginsPath = osc_plugins_path();
            $dir = opendir($pluginsPath);
            while($file = readdir($dir)) {
                if(preg_match('/^[a-zA-Z0-9_]+$/', $file, $matches)) {
                    // This has to change in order to catch any .php file
                    $pluginPath = $pluginsPath . "$file/index.php";
                    if(file_exists($pluginPath)) {
                        $plugins[] = $file."/index.php";
                    } else {
                        trigger_error(sprintf(__('Plugin %s is missing the index.php file %s'), $file, $pluginPath));
                    }
                }
            }
            closedir($dir);
            return $plugins;
        }

        static function loadActive()
        {
            $data['s_value'] = osc_active_plugins() ;
            $plugins_list = unserialize($data['s_value']);
            if(is_array($plugins_list)) {
                foreach($plugins_list as $plugin_name) {
                    $pluginPath = osc_plugins_path() . $plugin_name;
                    if(file_exists($pluginPath)) {
                        //This should include the file and adds the hooks
                        include_once $pluginPath;
                    }
                }
            }
        }

        static function listInstalled()
        {
            $p_array = array();

            $data['s_value'] = osc_installed_plugins() ;
            $plugins_list    = unserialize($data['s_value']) ;
            if( is_array($plugins_list) ) {
                foreach($plugins_list as $plugin_name) {
                    $p_array[] = $plugin_name ;
                }
            }

            return $p_array;
        }

        static function listEnabled()
        {
            $p_array = array() ;

            $data['s_value'] = osc_active_plugins() ;
            $plugins_list = unserialize($data['s_value']) ;
            if( is_array($plugins_list) ) {
                foreach($plugins_list as $plugin_name) {
                    $p_array[] = $plugin_name;
                }
            }

            return $p_array;
        }

        static function findByUpdateURI($uri) {
            $plugins = Plugins::listAll();
            foreach($plugins as $p) {
                $info = Plugins::getInfo($p);
                if($info['plugin_update_uri']==$uri) {
                    return $p;
                }
            }
            return false;
        }

        static function resource($path)
        {
            $fullPath = osc_plugins_path() . $path;
            return file_exists($fullPath) ? $fullPath : false;
        }

        static function register($path, $function)
        {
            $path = str_replace(osc_plugins_path(), '', $path);
            self::addHook('install_' . $path, $function);
        }

        static function install($path)
        {
            $data['s_value'] = osc_installed_plugins() ;
            $plugins_list    = unserialize($data['s_value']) ;

            if( is_array($plugins_list) ) {
                // check if the plugin is already installed
                if( in_array($path, $plugins_list) ) {
                    return array('error_code' => 'error_installed') ;
                }
            }

            if( !file_exists(osc_plugins_path() . $path) ) {
                return array('error_code' => 'error_file') ;
            }

            // check if there are spaces when you include the plugin
            ob_start() ;
            include_once( osc_plugins_path() . $path ) ;

            if ( ob_get_length() > 0 ) {
                return array('error_code' => 'error_output', 'output' => ob_get_clean()) ;
            }
            ob_end_clean() ;

            try {
                self::runHook('install_' . $path) ;
            } catch(Exception $e) {
                return array('error_code' => 'custom_error' ,'msg' => $e->getMessage()) ;
            }

            if( !self::activate($path) ) {
                return array('error_code' => '') ;
            }

            $plugins_list[]  = $path ;
            $data['s_value'] = serialize($plugins_list) ;
            $condition = array( 's_section' => 'osclass', 's_name' => 'installed_plugins') ;
            Preference::newInstance()->update($data, $condition) ;

            return true ;
        }

        static function uninstall($path)
        {
            $data['s_value'] = osc_installed_plugins() ;
            $plugins_list    = unserialize($data['s_value']) ;

            $path = str_replace(osc_plugins_path(), '', $path);
            if( !is_array($plugins_list) ) {
                return false ;
            }

            if( !self::deactivate($path) ) {
                return false ;
            }

            self::runHook($path . '_uninstall') ;

            foreach($plugins_list as $k => $v) {
                if($v == $path) {
                    unset($plugins_list[$k]) ;
                }
            }

            $data['s_value'] = serialize($plugins_list) ;
            $condition = array( 's_section' => 'osclass', 's_name' => 'installed_plugins') ;
            Preference::newInstance()->update($data, $condition) ;

            $plugin = self::getInfo($path) ;
            self::cleanCategoryFromPlugin($plugin['short_name']) ;
            return true ;
        }

        static function activate($path)
        {
            $data['s_value'] = osc_active_plugins() ;
            $plugins_list    = unserialize($data['s_value']);

            if( is_array($plugins_list) ) {
                // check if the plugin is already active
                if( in_array($path, $plugins_list) ) {
                    return false ;
                }
            }

            self::runHook($path . '_enable') ;

            $plugins_list[]  = $path ;
            $data['s_value'] = serialize($plugins_list) ;
            $condition = array( 's_section' => 'osclass', 's_name' => 'active_plugins') ;
            Preference::newInstance()->update($data, $condition) ;

            self::reload() ;
            return true ;
        }

        static function deactivate($path)
        {
            $data['s_value'] = osc_active_plugins() ;
            $plugins_list = unserialize($data['s_value']);

            $path = str_replace(osc_plugins_path(), '', $path) ;
            // check if there is some plugin enabled
            if( !is_array($plugins_list) ) {
                return false ;
            }

            // remove $path from the active plugins list
            foreach($plugins_list as $k => $v) {
                if($v == $path) {
                    unset($plugins_list[$k]) ;
                }
            }

            self::runHook($path . '_disable') ;

            // update t_preference field for active plugins
            $data['s_value'] = serialize($plugins_list) ;
            $condition = array( 's_section' => 'osclass', 's_name' => 'active_plugins') ;
            Preference::newInstance()->update($data, $condition) ;

            self::reload() ;
            return true ;
        }

        static function isThisCategory($name, $id)
        {
            return PluginCategory::newInstance()->isThisCategory($name, $id);
        }

        static function getInfo($plugin)
        {
            $s_info = file_get_contents(osc_plugins_path() . $plugin);
            $info   = array();
            if( preg_match('|Plugin Name:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['plugin_name'] = trim($match[1]);
            } else {
                $info['plugin_name'] = $plugin;
            }

            if( preg_match('|Plugin URI:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['plugin_uri'] = trim($match[1]);
            } else {
                $info['plugin_uri'] = "";
            }

            if( preg_match('|Plugin update URI:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['plugin_update_uri'] = trim($match[1]);
            } else {
                $info['plugin_update_uri'] = "";
            }

            if( preg_match('|Description:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['description'] = trim($match[1]);
            } else {
                $info['description'] = "";
            }

            if( preg_match('|Version:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['version'] = trim($match[1]);
            } else {
                $info['version'] = "";
            }

            if( preg_match('|Author:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['author'] = trim($match[1]);
            } else {
                $info['author'] = "";
            }

            if( preg_match('|Author URI:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['author_uri'] = trim($match[1]);
            } else {
                $info['author_uri'] = "";
            }

            if( preg_match('|Short Name:([^\\r\\t\\n]*)|i', $s_info, $match) ) {
                $info['short_name'] = trim($match[1]);
            } else {
                $info['short_name'] = $info['plugin_name'];
            }

            $info['filename'] = $plugin;

            return $info;
        }

        static function checkUpdate($plugin) {
            $info = Plugins::getInfo($plugin);
            return osc_check_update($info['plugin_update_uri'], $info['version']);
        }


        static function configureView($path)
        {
            $plugin = str_replace(osc_plugins_path(), '', $path);
            if(stripos($plugin, ".php")===FALSE) {
                $plugins_list = unserialize(osc_active_plugins());
                if(is_array($plugins_list)) {
                    foreach($plugins_list as $p){
                        $data = self::getInfo($p);
                        if($plugin == $data['plugin_name']) {
                            $plugin = $p ;
                            break ;
                        }
                    }
                }
            }
            header('Location: '.osc_plugin_configure_url($plugin));
            exit;
        }

        static function cleanCategoryFromPlugin($plugin)
        {
            $dao_pluginCategory = new PluginCategory() ;
            $dao_pluginCategory->delete(array('s_plugin_name' => $plugin)) ;
            unset($dao_pluginCategory) ;
        }

        static function addToCategoryPlugin($categories, $plugin)
        {
            $dao_pluginCategory = new PluginCategory() ;
            $dao_category = new Category() ;
            if(!empty($categories)) {
                foreach($categories as $catId)
                {
                    $result = $dao_pluginCategory->isThisCategory($plugin, $catId);
                    if($result==0) {
                        $fields = array() ;
                        $fields['s_plugin_name'] = $plugin ;
                        $fields['fk_i_category_id'] = $catId ;
                        $dao_pluginCategory->insert($fields) ;

                        $subs = $dao_category->findSubcategories($catId);
                        if(is_array($subs) && count($subs)>0) {
                            $cats = array();
                            foreach( $subs as $sub) {
                                $cats[] = $sub['pk_i_id'];
                            }
                            self::addToCategoryPlugin($cats, $plugin) ;
                        }
                    }
                }
            }
            unset($dao_pluginCategory) ;
            unset($dao_category) ;
        }

        // Add a hook
        static function addHook($hook, $function, $priority = 5)
        {
            $hook         = preg_replace('|/+|', '/', str_replace('\\', '/', $hook)) ;
            $plugin_path  = str_replace('\\', '/', osc_plugins_path()) ;
            $hook         = str_replace($plugin_path, '', $hook) ;
            $found_plugin = false;
            if(isset(self::$hooks[$hook])) {
                for($_priority = 0;$_priority<=10;$_priority++) {
                    if(isset(self::$hooks[$hook][$_priority])) {
                        foreach(self::$hooks[$hook][$_priority] as $fxName) {
                            if($fxName==$function) {
                                $found_plugin = true;
                                break;
                            }
                        }
                    }
                }
            }
            if(!$found_plugin) { self::$hooks[$hook][$priority][] = $function; }
        }

        static function removeHook($hook, $function)
        {
            for($priority = 0;$priority<=10;$priority++) {
                if(isset(self::$hooks[$hook][$priority])) {
                    foreach(self::$hooks[$hook][$priority] as $k => $v) {
                        if($v==$function) {
                            unset(self::$hooks[$hook][$priority][$k]);
                        }
                    }
                }
            }
        }

        static function getActive()
        {
            return self::$hooks;
        }

        static function reload()
        {
            Preference::newInstance()->toArray();
            self::init();
        }

        static function init()
        {
            self::loadActive();
        }
    }

?>