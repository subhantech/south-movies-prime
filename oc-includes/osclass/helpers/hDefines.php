<?php

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


    /**
    * Helper Defines
    * @package OSClass
    * @subpackage Helpers
    * @author OSClass
    */

    /**
     * Gets the root url for your installation
     *
     * @param boolean $with_index true if index.php in the url is needed
     * @return string
     */
    function osc_base_url($with_index = false) {
        $path = '';
        if(MULTISITE) {
            $path = osc_multisite_url();
        } else {
            $path = WEB_PATH ;
        }
        if ($with_index) $path .= "index.php" ;
        return($path) ;
    }

    /**
     * Gets the root url of oc-admin for your installation
     * 
     * @param boolean $with_index true if index.php in the url is needed
     * @return string
     */
    function osc_admin_base_url($with_index = false) {
        $path = '';
        if(MULTISITE) {
            $path = osc_multisite_url();
        } else {
            $path = WEB_PATH ;
        }
        $path .= "oc-admin/" ;
        if ($with_index) $path .= "index.php" ;
        return($path) ;
    }
    
    /**
    * Gets the root path for your installation
    *
    * @return string
    */
    function osc_base_path() {
        return(ABS_PATH) ;
    }

    /**
    * Gets the root path of oc-admin
    *
    * @return string
    */
    function osc_admin_base_path() {
        return(osc_base_path() . "oc-admin/") ;
    }

    /**
    * Gets the librarieas path
    *
    * @return string
    */
    function osc_lib_path() {
        return(LIB_PATH) ;
    }

    /**
    * Gets the content path
    *
    * @return string
    */
    function osc_content_path() {
        return(CONTENT_PATH) ;
    }

    /**
    * Gets the themes path
    *
    * @return string
    */
    function osc_themes_path() {
        return(THEMES_PATH) ;
    }

    /**
    * Gets the plugins path
    *
    * @return string
    */
    function osc_plugins_path() {
        return(PLUGINS_PATH) ;
    }

    /**
    * Gets the translations path
    *
    * @return string
    */
    function osc_translations_path() {
        return(TRANSLATIONS_PATH) ;
    }

    /**
    * Gets the current oc-admin theme
    *
    * @return string
    */
    function osc_current_admin_theme() {
        return( AdminThemes::newInstance()->getCurrentTheme() ) ;
    }

    /**
     * Gets the complete url of a given admin's file
     *
     * @param string $file the admin's file
     * @return string
     */
    function osc_current_admin_theme_url($file = '') {
        return AdminThemes::newInstance()->getCurrentThemeUrl() . $file ;
    }


    /**
     * Gets the complete path of a given admin's file
     *
     * @param string $file the admin's file
     * @return string
     */
    function osc_current_admin_theme_path($file = '') {
        require AdminThemes::newInstance()->getCurrentThemePath() . $file ;
    }

    /**
     * Gets the complete url of a given style's file
     *
     * @param string $file the style's file
     * @return string
     */
    function osc_current_admin_theme_styles_url($file = '') {
        return AdminThemes::newInstance()->getCurrentThemeStyles() . $file ;
    }

    /**
     * Gets the complete url of a given js's file
     *
     * @param string $file the js's file
     * @return string
     */
    function osc_current_admin_theme_js_url($file = '') {
        return AdminThemes::newInstance()->getCurrentThemeJs() . $file ;
    }

    /**
     * Gets the current theme for the public website
     *
     * @return string
     */
    function osc_current_web_theme() {
        return WebThemes::newInstance()->getCurrentTheme() ;
    }

    /**
     * Gets the complete url of a given file using the theme url as a root
     *
     * @param string $file the given file
     * @return string
     */
    function osc_current_web_theme_url($file = '') {
        return WebThemes::newInstance()->getCurrentThemeUrl() . $file ;
    }

    /**
     * Gets the complete path of a given file using the theme path as a root
     *
     * @param string $file
     * @return string
     */
    function osc_current_web_theme_path($file = '') {

        if( file_exists(WebThemes::newInstance()->getCurrentThemePath() . $file) ){
            require WebThemes::newInstance()->getCurrentThemePath() . $file ;
        } else {
            WebThemes::newInstance()->setGuiTheme();
            if( file_exists(WebThemes::newInstance()->getCurrentThemePath() . $file) ) {
                require WebThemes::newInstance()->getCurrentThemePath() . $file;
            }
        }
    }

    /**
     * Gets the complete path of a given styles file using the theme path as a root
     *
     * @param string $file
     * @return string
     */
    function osc_current_web_theme_styles_url($file = '') {
        return WebThemes::newInstance()->getCurrentThemeStyles() . $file ;
    }

    /**
     * Gets the complete path of a given js file using the theme path as a root
     *
     * @param string $file
     * @return string
     */
    function osc_current_web_theme_js_url($file = '') {
        return WebThemes::newInstance()->getCurrentThemeJs() . $file ;
    }

    
    /////////////////////////////////////
    //functions for the public website //
    /////////////////////////////////////

    /**
     *  Create automatically the contact url
     *
     * @return string
     */
    function osc_contact_url() {
        if ( osc_rewrite_enabled() ) {
            $path = osc_base_url() . osc_get_preference('rewrite_contact');
        } else {
            $path = osc_base_url(true) . '?page=contact' ;
        }
        return $path ;
    }
    
    /**
     * Create automatically the url to post an item in a category
     *
     * @return string
     */
    function osc_item_post_url_in_category() {
        if (osc_category_id() > 0) {
            if ( osc_rewrite_enabled() ) {
                $path = osc_base_url() . osc_get_preference('rewrite_item_new') . '/' . osc_category_id();
            } else {
                $path = sprintf(osc_base_url(true) . '?page=item&action=item_add&catId=%d', osc_category_id()) ;
            }
        } else {
            $path = osc_item_post_url() ;
        }
        return $path ;
    }

    /**
     *  Create automatically the url to post an item
     *
     * @return string
     */
    function osc_item_post_url() {
        if ( osc_rewrite_enabled() ) {
            $path = osc_base_url() . osc_get_preference('rewrite_item_new') ;
        } else {
            $path = sprintf(osc_base_url(true) . '?page=item&action=item_add') ;
        }
        return $path ;
    }

    /**
     * Create automatically the url of a category
     *
     * @param string $pattern
     * @return string the url
     */
    function osc_search_category_url() {
        $path = '' ;
        if(osc_rewrite_enabled()) {
            $category = Category::newInstance()->hierarchy(osc_category_id()) ;
            $sanitized_categories = array();
            for ($i = count($category); $i > 0; $i--) {
                $sanitized_categories[] = $category[$i - 1]['s_slug'];
            }
            $path = osc_base_url() . str_replace('{CATEGORIES}', implode("/", $sanitized_categories), str_replace('{CATEGORY_ID}', osc_category_id(), str_replace('{CATEGORY_SLUG}', osc_category_slug(), osc_get_preference('rewrite_cat_url'))));
        } else {
            $path = sprintf( osc_base_url(true) . '?page=search&sCategory=%d', osc_category_id() ) ;
        }
        return $path ;
    }

    /**
     * Create automatically the url of the users' dashboard
     *
     * @return string
     */
    function osc_user_dashboard_url() {
        if ( osc_rewrite_enabled() ) {
            $path = osc_base_url() . osc_get_preference('rewrite_user_dashboard');
        } else {
            $path = osc_base_url(true) . '?page=user&action=dashboard' ;
        }
        return $path ;
    }

    /**
     * Create automatically the logout url
     *
     * @return string
     */
    function osc_user_logout_url() {
        if ( osc_rewrite_enabled() ) {
            $path = osc_base_url() . osc_get_preference('rewrite_user_logout');
        } else {
            $path = osc_base_url(true) . '?page=main&action=logout' ;
        }
        return $path ;
    }

    /**
     * Create automatically the login url
     *
     * @return string
     */
    function osc_user_login_url() {
        if ( osc_rewrite_enabled() ) {
            $path = osc_base_url() . osc_get_preference('rewrite_user_login');
        } else {
            $path = osc_base_url(true) . '?page=login' ;
        }
        return $path ;
    }

    /**
     * Create automatically the url to register an account
     *
     * @return string
     */
    function osc_register_account_url() {
        if ( osc_rewrite_enabled() ) {
            $path = osc_base_url() . osc_get_preference('rewrite_user_register');
        } else {
            $path = osc_base_url(true) . '?page=register&action=register' ;
        }
        return $path ;
    }

    /**
     * Create automatically the url to activate an account
     *
     * @param int $id
     * @param string $code
     * @return string
     */
    function osc_user_activate_url($id, $code) {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_activate') . '/' . $id . '/' . $code ;
        } else {
            return osc_base_url(true) . '?page=register&action=validate&id=' . $id . '&code=' . $code ;
        }
    }

    /**
     * Create automatically the url of the item's comments page
     *
     * @param mixed $page
     * @param string $locale
     * @return string
     */
    function osc_item_comments_url($page = 'all', $locale = '') {
        if ( osc_rewrite_enabled() ) {
            return osc_item_url($locale) . "?comments-page=" . $page;
        } else {
            return osc_item_url($locale) . "&comments-page=" . $page;
        }
    }

    /**
     * Create automatically the url of the item's comments page
     *
     * @param string $locale
     * @return string
     */
    function osc_comment_url($locale = '') {
        return osc_item_url($locale) . "?comment=" . osc_comment_id();
    }

    
    /**
     * Create automatically the url of the item details page
     *
     * @param string $locale
     * @return string
     */
    function osc_item_url($locale = '') {
        if ( osc_rewrite_enabled() ) {
            $sanitized_categories = array();
            $cat = Category::newInstance()->hierarchy(osc_item_category_id()) ;
            for ($i = (count($cat)); $i > 0; $i--) {
                $sanitized_categories[] = $cat[$i - 1]['s_slug'];
            }
            $url = str_replace('{CATEGORIES}', implode("/", $sanitized_categories), str_replace('{ITEM_ID}', osc_item_id(), str_replace('{ITEM_TITLE}', osc_sanitizeString(osc_item_title()), osc_get_preference('rewrite_item_url'))));
            if($locale!='') {
                $path = osc_base_url().$locale."/".$url;
            } else {
                $path = osc_base_url().$url;
            }
        } else {
            $path = osc_item_url_ns( osc_item_id(), $locale ) ;
        }
        return $path ;
    }

    /**
     * Create automatically the url of the item details page
     *
     * @param string $locale
     * @return string
     */
    function osc_premium_url($locale = '') {
        if ( osc_rewrite_enabled() ) {
            $sanitized_categories = array();
            $cat = Category::newInstance()->hierarchy(osc_premium_category_id()) ;
            for ($i = (count($cat)); $i > 0; $i--) {
                $sanitized_categories[] = $cat[$i - 1]['s_slug'];
            }
            $url = str_replace('{CATEGORIES}', implode("/", $sanitized_categories), str_replace('{ITEM_ID}', osc_premium_id(), str_replace('{ITEM_TITLE}', osc_sanitizeString(osc_premium_title()), osc_get_preference('rewrite_item_url'))));
            if($locale!='') {
                $path = osc_base_url().$locale."/".$url;
            } else {
                $path = osc_base_url().$url;
            }
        } else {
            $path = osc_item_url_ns( osc_premium_id(), $locale ) ;
        }
        return $path ;
    }

    /**
     * Create the no friendly url of the item using the id of the item
     * 
     * @param int $id the primary key of the item
     * @param $locale
     * @return string
     */
    function osc_item_url_ns($id, $locale = '') {
        $path = osc_base_url(true) . '?page=item&id=' . $id ;
        if($locale!='') {
            $path .= "&lang=" . $locale;
        }

        return $path ;
    }
    
    /**
     * Create automatically the url to for admin to edit an item
     *
     * @param int $id
     * @return string
     */
    function osc_item_admin_edit_url($id) {
        return osc_admin_base_url(true) . '?page=items&action=item_edit&id=' . $id ;
    }
     
    /**
     * Gets current user alerts' url
     *
     * @return string
     */
    function osc_user_alerts_url() {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_alerts') ;
        } else {
            return osc_base_url(true) . '?page=user&action=alerts' ;
        }
    }

    /**
     * Gets current user alert unsubscribe url
     *
     * @param string $email
     * @param string $secret
     * @return string
     */
    function osc_user_unsubscribe_alert_url($email = '', $secret = '') {
        if($secret=='') { $secret = osc_alert_secret(); }
        if($email=='') { $email = osc_user_email(); }
        return osc_base_url(true) . '?page=user&action=unsub_alert&email='.urlencode($email).'&secret='.$secret ;
    }

    /**
     * Gets user alert activate url
     *
     * @param string $secret
     * @param string $email
     * @return string
     */
    function osc_user_activate_alert_url( $secret , $email ) {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_activate_alert') . '/' . $secret . '/' . urlencode($email) ;
        } else {
            return osc_base_url(true) . '?page=user&action=activate_alert&email=' . urlencode($email) . '&secret=' . $secret ;
        }

    }

    /**
     * Gets current user url
     *
     * @return string
     */
    function osc_user_profile_url() {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_profile') ;
        } else {
            return osc_base_url(true) . '?page=user&action=profile' ;
        }
    }

    /**
     * Gets current user alert activate url
     *
     * @param int $page
     * @return string
     */
    function osc_user_list_items_url($page = '') {
        if ( osc_rewrite_enabled() ) {
            if($page=='') {
                return osc_base_url() . osc_get_preference('rewrite_user_items');
            } else {
                return osc_base_url() . osc_get_preference('rewrite_user_items') . '?iPage='.$page ;
            }
        } else {
            if($page=='') {
                return osc_base_url(true) . '?page=user&action=items' ;
            } else {
                return osc_base_url(true) . '?page=user&action=items&iPage='.$page ;
            }
        }
    }

    /**
     * Gets url to change email
     *
     * @return string
     */
    function osc_change_user_email_url() {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_change_email');
        } else {
            return osc_base_url(true) . '?page=user&action=change_email' ;
        }
    }

    /**
     * Gets confirmation url of change email
     *
     * @param int $userId
     * @param string $code
     * @return string
     */
    function osc_change_user_email_confirm_url($userId, $code) {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_change_email_confirm') . '/' . $userId . '/' . $code ;
        } else {
            return osc_base_url(true) . '?page=user&action=change_email_confirm&userId=' . $userId . '&code=' . $code ;
        }
    }

    /**
     * Gets url for changing password
     *
     * @return string
     */
    function osc_change_user_password_url() {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_change_password');
        } else {
            return osc_base_url(true) . '?page=user&action=change_password' ;
        }
    }
    
    /**
     * Gets url for recovering password
     *
     * @return string
     */
    function osc_recover_user_password_url() {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_recover');
        } else {
            return osc_base_url(true) . '?page=login&action=recover' ;
        }
    }
    
    /**
     * Gets url for confirm the forgot password process
     *
     * @param int $userId
     * @param string $code
     * @return string
     */
    function osc_forgot_user_password_confirm_url($userId, $code) {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_user_forgot') . '/' . $userId . '/' . $code ;
        } else {
            return osc_base_url(true) . '?page=login&action=forgot&userId='.$userId.'&code='.$code;
        }
    }

    /**
     * Gets url for confirmation admin password recover proces
     *
     * @param int $adminId
     * @param string $code
     * @return string
     */
    function osc_forgot_admin_password_confirm_url($adminId, $code) {
        return osc_admin_base_url(true) . '?page=login&action=forgot&adminId='.$adminId.'&code='.$code;
    }

    /**
     * Gets url for changing website language (for users)
     *
     * @param string $locale
     * @return string
     */
    function osc_change_language_url($locale) {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_language') . '/' . $locale ;
        } else {
            return osc_base_url(true) . '?page=language&locale=' . $locale ;
        }
    }
    
    /////////////////////////////////////
    //       functions for items       //
    /////////////////////////////////////
    
    /**
     * Gets url for editing an item
     *
     * @param string $secret
     * @param int $id
     * @return string
     */
    function osc_item_edit_url($secret = '', $id = '') {
        if ($id == '') $id = osc_item_id();
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_item_edit') . '/' . $id . '/' . $secret ;
        } else {
            return osc_base_url(true) . '?page=item&action=item_edit&id=' . $id . ($secret != '' ? '&secret=' . $secret : '') ;
        }
    }

    /**
     * Gets url for delete an item
     *
     * @param string $secret
     * @param int $id
     * @return string
     */
    function osc_item_delete_url($secret = '', $id = '') {
        if ($id == '') $id = osc_item_id();
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_item_delete') . '/' . $id . '/' . $secret ;
        } else {
            return osc_base_url(true) . '?page=item&action=item_delete&id=' . $id . ($secret != '' ? '&secret=' . $secret : '') ;
        }
    }
    
    /**
     * Gets url for activate an item
     *
     * @param string $secret
     * @param int $id
     * @return string
     */
    function osc_item_activate_url($secret = '', $id = '') {
        if ($id == '') $id = osc_item_id();
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_item_activate') . '/' . $id . '/' . $secret ;
        } else {
            return osc_base_url(true) . '?page=item&action=activate&id=' . $id . ($secret != '' ? '&secret=' . $secret : '') ;
        }
    }
    
    /**
     * Gets url for deleting a resource of an item
     *
     * @param int $id of the resource
     * @param int $item
     * @param string $code
     * @param string $secret
     * @return string
     */
    function osc_item_resource_delete_url($id, $item, $code, $secret = '') {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_item_resource_delete') . '/' . $id . '/' . $item . '/' . $code . ($secret != '' ? '/' . $secret : '');
        } else {
            return osc_base_url(true) . '?page=item&action=deleteResource&id=' . $id . '&item=' . $item . '&code=' . $code . ($secret != '' ? '&secret=' . $secret : '') ;
        }
    }

    /**
     * Gets url of send a friend (current item)
     *
     * @return string
     */
    function osc_item_send_friend_url() {
        if ( osc_rewrite_enabled() ) {
            return osc_base_url() . osc_get_preference('rewrite_item_send_friend') . '/' . osc_item_id() ;
        } else {
            return osc_base_url(true)."?page=item&action=send_friend&id=".osc_item_id();
        }
    }
    /////////////////////////////////////
    //functions for locations & search //
    /////////////////////////////////////


    /**
     * Gets list of countries
     *
     * @return string
     */
    function osc_get_countries() {
        if (View::newInstance()->_exists('countries')) {
            return View::newInstance()->_get('countries') ;
        } else {
            return Country::newInstance()->listAll() ;
        }
    }
    
    /**
     * Gets list of regions (from a country)
     *
     * @param int $country
     * @return string
     */
    function osc_get_regions($country = '') {
        if (View::newInstance()->_exists('regions')) {
            return View::newInstance()->_get('regions') ;
        } else {
            if($country=='') {
                return Region::newInstance()->listAll() ;
            } else {
                return Region::newInstance()->findByCountry($country);
            }
        }
    }
    
    /**
     * Gets list of cities (from a region)
     *
     * @param int $region
     * @return string
     */
    function osc_get_cities($region = '') {
        if (View::newInstance()->_exists('cities')) {
            return View::newInstance()->_get('cities') ;
        } else {
            if($region=='') {
                return City::newInstance()->listAll() ;
            } else {
                return City::newInstance()->findByRegion($region) ;
            }
        }
    }
    
    /**
     * Gets list of currencies
     *
     * @return string
     */
    function osc_get_currencies() {
        if (!View::newInstance()->_exists('currencies')) {
            View::newInstance()->_exportVariableToView('currencies', Currency::newInstance()->listAll());
        }
        return View::newInstance()->_get('currencies');
    }


    /**
     * Prints the additional options to the menu
     *
     * @param array $option with options of the form array('name' => 'display name', 'url' => 'url of link')
     *
     * @return void
     */
    function osc_add_option_menu($option = null) {
        if($option!=null) {
            echo '<li><a href="' . $option['url'] . '" >' . $option['name'] . '</a></li>' ;
        }
    }

    /**
     * Get if user is on ad page
     *
     * @return boolean
     */
    function osc_is_ad_page() {
        $location = Rewrite::newInstance()->get_location();
        $section = Rewrite::newInstance()->get_section();
        if($location=='item' && $section=='') {
            return true;
        }
        return false;
    }

    /**
     * Get if user is on search page
     *
     * @return boolean
     */
    function osc_is_search_page() {
        $location = Rewrite::newInstance()->get_location();
        if($location=='search') {
            return true;
        }
        return false;
    }

    /**
     * Get if user is on a static page
     *
     * @return boolean
     */
    function osc_is_static_page() {
        $location = Rewrite::newInstance()->get_location();
        if($location=='page') {
            return true;
        }
        return false;
    }

    /**
     * Get if user is on homepage
     *
     * @return boolean
     */
    function osc_is_home_page() {
        $location = Rewrite::newInstance()->get_location();
        $section = Rewrite::newInstance()->get_section();
        if($location=='' && $section=='') {
            return true;
        }
        return false;
    }

    /**
     * Get if user is on user dashboard
     *
     * @return boolean
     */
    function osc_is_user_dashboard() {
        $location = Rewrite::newInstance()->get_location();
        $section = Rewrite::newInstance()->get_section();
        if($location=='user' && $section=='dashboard') {
            return true;
        }
        return false;
    }
    
    /**
     * Get if user is on publish page
     *
     * @return boolean
     */
    function osc_is_publish_page() {
        $location = Rewrite::newInstance()->get_location();
        $section = Rewrite::newInstance()->get_section();
        if($location=='item' && $section=='item_add') {
            return true;
        }
        return false;
    }
    
    /**
     * Get if user is on login form
     *
     * @return boolean
     */
    function osc_is_login_form() {
        $location = Rewrite::newInstance()->get_location();
        $section = Rewrite::newInstance()->get_section();
        if($location=='login' && $section=='') {
            return true;
        }
        return false;
    }
    
    /**
     * Get location
     *
     * @return string
     */
    function osc_get_osclass_location() {
        return Rewrite::newInstance()->get_location();
    }

    /**
     * Get section
     *
     * @return string
     */
    function osc_get_osclass_section() {
        return Rewrite::newInstance()->get_section();
    }



?>
