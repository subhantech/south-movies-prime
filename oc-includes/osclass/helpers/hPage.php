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
    * Helper Pages
    * @package OSClass
    * @subpackage Helpers
    * @author OSClass
    */

    /**
     * Gets current page object
     *
     * @return array
     */
    function osc_static_page() {
        if (View::newInstance()->_exists('pages')) {
            $page = View::newInstance()->_current('pages') ;
        } else if (View::newInstance()->_exists('page')) {
            $page = View::newInstance()->_get('page') ;
        } else {
            $page = null ;
        }
        return($page) ;
    }
    
    /**
     * Gets current page field
     * 
     * @param string $field
     * @param string $locale
     * @return string
     */
    function osc_static_page_field($field, $locale = '') {
        return osc_field(osc_static_page(), $field, $locale) ;
    }

    /**
     * Gets current page title
     *
     * @param string $locale
     * @return string
     */
    function osc_static_page_title($locale = '') {
        if ($locale == "") $locale = osc_current_user_locale() ;
        return osc_static_page_field("s_title", $locale) ;
    }

    /**
     * Gets current page text
     *
     * @param string $locale
     * @return string
     */
    function osc_static_page_text($locale = '') {
        if ($locale == "") $locale = osc_current_user_locale() ;
        return osc_static_page_field("s_text", $locale) ;
    }

    /**
     * Gets current page ID
     *
     * @return string
     */
    function osc_static_page_id() {
        return osc_static_page_field("pk_i_id") ;
    }

    /**
     * Get page order
     *
     * @return int
     */
    function osc_static_page_order() {
        return (int)osc_static_page_field("i_order") ;
    }

    /**
     * Gets current page modification date
     *
     * @return string
     */
    function osc_static_page_mod_date() {
        return osc_static_page_field("dt_mod_date") ;
    }

    /**
     * Gets current page publish date
     *
     * @return string
     */
    function osc_static_page_pub_date() {
        return osc_static_page_field("dt_pub_date") ;
    }

    /**
     * Gets current page slug or internal name
     *
     * @return string
     */
    function osc_static_page_slug() {
        return osc_static_page_field("s_internal_name") ;
    }

    /**
     * Gets current page url
     *
     * @param string $locale
     * @return string
     */
    function osc_static_page_url($locale = '') {
        if ( osc_rewrite_enabled() ) {
            $sanitized_categories = array();
            $cat = Category::newInstance()->hierarchy(osc_item_category_id()) ;
            for ($i = (count($cat)); $i > 0; $i--) {
                $sanitized_categories[] = $cat[$i - 1]['s_slug'];
            }
            $url = str_replace('{PAGE_TITLE}', osc_static_page_title(), str_replace('{PAGE_ID}', osc_static_page_id(), str_replace('{PAGE_SLUG}', urlencode(osc_static_page_slug()), osc_get_preference('rewrite_page_url'))));
            if($locale!='') {
                $path = osc_base_url().$locale."/".$url;
            } else {
                $path = osc_base_url().$url;
            }
        } else {
            if($locale!='') {
                $path = osc_base_url(true)."?page=page&id=".osc_static_page_id()."&lang=".$locale;
            } else {
                $path = osc_base_url(true)."?page=page&id=".osc_static_page_id();
            }
        }
        return $path ;
    }
    
    /**
     * Gets the specified static page by internal name.
     *
     * @param string $internal_name
     * @param string $locale
     * @return boolean
     */
    function osc_get_static_page($internal_name, $locale = '') {
        if ($locale == "") $locale = osc_current_user_locale() ;
        return View::newInstance()->_exportVariableToView('page', Page::newInstance()->findByInternalName($internal_name, $locale) ) ;
    }    
    
    /**
     * Gets the total of static pages. If static pages are not loaded, this function will load them.
     *
     * @return int
     */
    function osc_count_static_pages() {
        if ( !View::newInstance()->_exists('pages') ) {
            View::newInstance()->_exportVariableToView('pages', Page::newInstance()->listAll(false) ) ;
        }
        return View::newInstance()->_count('pages') ;
    }

    /**
     * Let you know if there are more static pages in the list. If static pages are not loaded,
     * this function will load them.
     *
     * @return boolean
     */
    function osc_has_static_pages() {
        if ( !View::newInstance()->_exists('pages') ) {
            View::newInstance()->_exportVariableToView('pages', Page::newInstance()->listAll(false) ) ;
        }
        
        return View::newInstance()->_next('pages') ;
    }

    /**
     * Move the iterator to the first position of the pages array
     * It reset the osc_has_page function so you could have several loops
     * on the same page
     *
     * @return boolean
     */
    function osc_reset_static_pages() {
        return View::newInstance()->_erase('pages') ;
    }

?>
