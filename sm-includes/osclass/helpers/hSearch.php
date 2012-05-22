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
    * Helper Search
    * @package OSClass
    * @subpackage Helpers
    * @author OSClass
    */

    /**
     * Gets search object
     *
     * @return mixed
     */
    function osc_search() {
        if(View::newInstance()->_exists('search')) {
            return View::newInstance()->_get('search');
        } else {
            $search = new Search();
            View::newInstance()->_exportVariableToView('search', $search);
            return $search;
        }
    }

    /**
     * Gets available search orders
     *
     * @return array
     */
    function osc_list_orders() {
        return  array(
                     __('Newly listed')        => array('sOrder' => 'dt_pub_date', 'iOrderType' => 'desc')
                    ,__('Lower price first')   => array('sOrder' => 'i_price', 'iOrderType' => 'asc')
                    ,__('Higher price first')  => array('sOrder' => 'i_price', 'iOrderType' => 'desc')
                );
    }
    
    /**
     * Gets current search page
     *
     * @return int
     */
    function osc_search_page() {
        return View::newInstance()->_get('search_page');
    }
    
    /**
     * Gets total pages of search
     *
     * @return int
     */
    function osc_search_total_pages() {
        return View::newInstance()->_get('search_total_pages');
    }
    
    /**
     * Gets if "has pic" option is enabled or not in the search
     *
     * @return boolean
     */
    function osc_search_has_pic() {
        return View::newInstance()->_get('search_has_pic');
    }
    
    /**
     * Gets current search order
     *
     * @return string
     */
    function osc_search_order() {
        return View::newInstance()->_get('search_order');
    }
    
    /**
     * Gets current search order type
     *
     * @return string
     */
    function osc_search_order_type() {
        return View::newInstance()->_get('search_order_type');
    }
    
    /**
     * Gets current search pattern
     *
     * @return string
     */
    function osc_search_pattern() {
        if(View::newInstance()->_exists('search_pattern')) {
            return View::newInstance()->_get('search_pattern');
        } else {
            return '';
        }
    }

    /**
     * Gets current search region
     *
     * @return string
     */
    function osc_search_region() {
        return View::newInstance()->_get('search_region');
    }

    /**
     * Gets current search city
     *
     * @return string
     */
    function osc_search_city() {
        return View::newInstance()->_get('search_city');
    }
    
    /**
     * Gets current search users
     * 
     * @return string
     */
    function osc_search_user() {
        if(is_array(View::newInstance()->_get('search_from_user') ) ){
            return View::newInstance()->_get('search_from_user');
        }
        return array();
    }
    /**
     * Gets current search max price
     *
     * @return float
     */
    function osc_search_price_max() {
        return View::newInstance()->_get('search_price_max');
    }
    
    /**
     * Gets current search min price
     *
     * @return float
     */
    function osc_search_price_min() {
        return View::newInstance()->_get('search_price_min');
    }
    
    /**
     * Gets current search total items
     *
     * @return int
     */
    function osc_search_total_items() {
        return View::newInstance()->_get('search_total_items');
    }
    
    /**
     * Gets current search "show as" variable (show the items as a list or as a gallery)
     *
     * @return string
     */
    function osc_search_show_as() {
        return View::newInstance()->_get('search_show_as');
    }
    
    /**
     * Gets current search start item record
     *
     * @return int
     */
    function osc_search_start() {
        return View::newInstance()->_get('search_start');
    }
    
    /**
     * Gets current search end item record
     *
     * @return int
     */
    function osc_search_end() {
        return View::newInstance()->_get('search_end');
    }
    
    /**
     * Gets current search category
     *
     * @return array
     */
    function osc_search_category() {
        if (View::newInstance()->_exists('search_subcategories')) {
            $category = View::newInstance()->_current('search_subcategories') ;
        } elseif (View::newInstance()->_exists('search_categories')) {
            $category = View::newInstance()->_current('search_categories') ;
        } else {
            $category = View::newInstance()->_get('search_category') ;
        }
        return($category) ;
    }

    /**
     * Gets current search category id
     *
     * @return int
     */
    function osc_search_category_id() {
        $categories = osc_search_category() ;
        $category   = array() ;
        $where      = array() ;

        foreach($categories as $cat) {
            if( is_numeric($cat) ) {
                $where[] = "a.pk_i_id = " . $cat ;
            } else {
                $slug_cat = explode( "/", trim($cat, "/") ) ;
                $where[]  = "b.s_slug = '" . addslashes( $slug_cat[count($slug_cat)-1] ) . "'" ;
            }
        }

        if( empty($where) ) {
            return null ;
        }

        // TODO: not the best way to do it
        $categories = Category::newInstance()->listWhere( implode(" OR ", $where) ) ;
        foreach($categories as $cat) {
            $category[] = $cat['pk_i_id'] ;
        }

        return $category ;
    }

    /**
     * Update the search url with new options
     *
     * @return string
     */
    function osc_update_search_url($params, $delimiter = '&amp;') {
        $request = Params::getParamsAsArray('get');
        unset($request['osclass']);
        if(isset($request['sCategory[0]'])) {
            unset($request['sCategory']);
        }
        unset($request['sCategory[]']);
        $merged = array_merge($request, $params);
        return osc_search_url($merged);
        //return osc_base_url(true) ."?" . http_build_query($merged, '', $delimiter);
    }

    /**
     * Load the form for the alert subscription
     *
     * @return void
     */
    function osc_alert_form() {
        osc_current_web_theme_path('alert-form.php') ;
    }
    
    /**
     * Gets alert of current search
     *
     * @return string
     */
    function osc_search_alert() {
        return View::newInstance()->_get('search_alert');
    }

    /**
     * Gets for a default search (all categories, noother option)
     *
     * @return string
     */
    function osc_search_show_all_url( ) {
        if(osc_rewrite_enabled ()) {
            return osc_base_url() . 'search/';
        } else {
            return osc_base_url(true) . '?page=search';
        }
    }

    /**
     * Gets search url given params
     *
     * @params array $params
     * @return string
     */
    function osc_search_url($params = null) {
        if(osc_rewrite_enabled()) {
            $url = osc_base_url().osc_get_preference('rewrite_search_url');
            if($params!=null) {
                foreach($params as $k => $v) {
                    switch($k) {
                        case 'sCountry':
                            $k = osc_get_preference('rewrite_search_country');
                            break;
                        case 'sRegion':
                            $k = osc_get_preference('rewrite_search_region');
                            break;
                        case 'sCity':
                            $k = osc_get_preference('rewrite_search_city');
                            break;
                        case 'sCityArea':
                            $k = osc_get_preference('rewrite_search_city_area');
                            break;
                        case 'sCategory':
                            $k = osc_get_preference('rewrite_search_category');
                            break;
                        case 'sUser':
                            $k = osc_get_preference('rewrite_search_user');
                            break;
                        case 'sPattern':
                            $k = osc_get_preference('rewrite_search_pattern');
                            break;
                        default:
                            break;
                    }
                    $url .= $k.",".$v."/";
                }
            }
        } else {
            $url = osc_base_url(true) . '?page=search';
            if($params!=null) {
                foreach($params as $k => $v) {
                    $url .= "&" . $k . "=" . $v;
                }
            }
        }
        return $url;
    }
    
    /**
     * Gets list of countries with items
     *
     * @return array
     */
    function osc_list_country() {
        if (View::newInstance()->_exists('list_countries')) {
            return View::newInstance()->_current('list_countries') ;
        } else {
            return null;
        }
    }

    /**
     * Gets list of regions with items
     *
     * @return array
     */
    function osc_list_region() {
        if (View::newInstance()->_exists('list_regions')) {
            return View::newInstance()->_current('list_regions') ;
        } else {
            return null;
        }
    }

    /**
     * Gets list of cities with items
     *
     * @return array
     */
    function osc_list_city() {
        if (View::newInstance()->_exists('list_cities')) {
            return View::newInstance()->_current('list_cities') ;
        } else {
            return null;
        }
    }
    
    /**
     * Gets the next country in the list_countries list
     *
     * @return array
     */
    function osc_has_list_countries() {
        if ( !View::newInstance()->_exists('list_countries') ) {
            View::newInstance()->_exportVariableToView('list_countries', CountryStats::newInstance()->listCountries() ) ;
        }
        $result = View::newInstance()->_next('list_countries') ;
        if (!$result) {
            View::newInstance()->_reset('list_countries') ;
        }
        return $result;
    }

    /**
     * Gets the next region in the list_regions list
     *
     * @param string $country
     * @return array
     */
    function osc_has_list_regions($country = '%%%%') {
        if ( !View::newInstance()->_exists('list_regions') ) {
            View::newInstance()->_exportVariableToView('list_regions', RegionStats::newInstance()->listRegions($country) ) ;
        }
        $result = View::newInstance()->_next('list_regions') ;
        if (!$result) {
            View::newInstance()->_reset('list_regions') ;
        }
        return $result;
    }

    /**
     * Gets the next city in the list_cities list
     *
     * @param string $region
     * @return array
     */
    function osc_has_list_cities($region = '%%%%') {
        if ( !View::newInstance()->_exists('list_cities') ) {
            View::newInstance()->_exportVariableToView('list_cities', CityStats::newInstance()->listCities($region) ) ;
        }
        $result = View::newInstance()->_next('list_cities');
        if (!$result) {
            View::newInstance()->_reset('list_cities') ;
        }
        return $result;
    }

    /**
     * Gets the total number of countries in list_countries
     *
     * @return int
     */
    function osc_count_list_countries() {
        if ( !View::newInstance()->_exists('list_countries') ) {
            View::newInstance()->_exportVariableToView('list_countries', CountryStats::newInstance()->listCountries() ) ;
        }
        return View::newInstance()->_count('list_countries') ;
    }
    
    /**
     * Gets the total number of regions in list_regions
     *
     * @param string $country
     * @return int
     */
    function osc_count_list_regions($country = '%%%%') {
        if ( !View::newInstance()->_exists('list_regions') ) {
            View::newInstance()->_exportVariableToView('list_regions', RegionStats::newInstance()->listRegions($country) ) ;
        }
        return View::newInstance()->_count('list_regions') ;
    }

    /**
     * Gets the total number of cities in list_cities
     *
     * @param string $region
     * @return int
     */
    function osc_count_list_cities($region = '%%%%') {
        if ( !View::newInstance()->_exists('list_cities') ) {
            View::newInstance()->_exportVariableToView('list_cities', CityStats::newInstance()->listCities($region) ) ;
        }
        return View::newInstance()->_count('list_cities') ;
    }

    // country attributes
    
    /**
     * Gets the the name of current "list country"
     *
     * @return string
     */
    function osc_list_country_name() {
        return osc_field(osc_list_country(), 'country_name', '') ;
    }
    
    /**
     * Gets the number of items of current "list country"
     *
     * @return int
     */
    function osc_list_country_code() {
        return osc_field(osc_list_country(), 'country_code', '') ;
    }
    
    /**
     * Gets the number of items of current "list country"
     *
     * @return int
     */
    function osc_list_country_items() {
        return osc_field(osc_list_country(), 'items', '') ;
    }
    
    // region attributes
    
    /**
     * Gets the name of current "list region"
     *
     * @return string
     */
    function osc_list_region_name() {
        return osc_field(osc_list_region(), 'region_name', '') ;
    }
    
    /**
     * Gets the id of current "list region"
     *
     * @return string
     */
    function osc_list_region_id() {
        return osc_field(osc_list_region(), 'region_id', '') ;
    }
    
    /**
     * Gets the number of items of current "list region"
     *
     * @return int
     */
    function osc_list_region_items() {
        return osc_field(osc_list_region(), 'items', '') ;
    }

    // city attributes
    
    /**
     * Gets the the name of current "list city""
     *
     * @return string
     */
    function osc_list_city_name() {
        return osc_field(osc_list_city(), 'city_name', '') ;
    }
    
    /**
     * Gets the id of current "list city"
     *
     * @return string
     */
    function osc_list_city_id() {
        return osc_field(osc_list_city(), 'city_id', '') ;
    }

    /**
     * Gets the number of items of current "list city"
     *
     * @return int
     */
    function osc_list_city_items() {
        return osc_field(osc_list_city(), 'items', '') ;
    }
    
    /**
     * Gets the url of current "list country""
     *
     * @return string
     */
    function osc_list_country_url() {
        return osc_search_url(array('sCountry' => osc_list_country_name()));
    }

    /**
     * Gets the url of current "list region""
     *
     * @return string
     */
    function osc_list_region_url() {
        return osc_search_url(array('sRegion' => osc_list_region_name()));
    }

    /**
     * Gets the url of current "list city""
     *
     * @return string
     */
    function osc_list_city_url() {
        return osc_search_url(array('sCity' => osc_list_city_name()));
    }

    /**********************
     ** LATEST SEARCHES **
     **********************/
    /**
     * Gets the latest searches done in the website
     *
     * @param int $limit
     * @return array
     */
    function osc_get_latest_searches($limit = 20) {
        if ( !View::newInstance()->_exists('latest_searches') ) {
            View::newInstance()->_exportVariableToView('latest_searches', LatestSearches::newInstance()->getSearches($limit) ) ;
        }
        return View::newInstance()->_count('latest_searches') ;
    }

    /**
     * Gets the total number of latest searches done in the website
     *
     * @return int
     */
    function osc_count_latest_searches() {
        if ( !View::newInstance()->_exists('latest_searches') ) {
            View::newInstance()->_exportVariableToView('latest_searches', LatestSearches::newInstance()->getSearches() ) ;
        }
        return View::newInstance()->_count('latest_searches') ;
    }
    
    /**
     * Gets the next latest search
     *
     * @return array
     */
    function osc_has_latest_searches() {
        if ( !View::newInstance()->_exists('latest_searches') ) {
            View::newInstance()->_exportVariableToView('latest_searches', LatestSearches::newInstance()->getSearches() ) ;
        }
        return View::newInstance()->_next('latest_searches') ;
    }

    /**
     * Gets the current latest search
     *
     * @return array
     */
    function osc_latest_search() {
        if (View::newInstance()->_exists('latest_searches')) {
            return View::newInstance()->_current('latest_searches') ;
        }
        return null;
    }
    
    /**
     * Gets the current latest search pattern
     *
     * @return string
     */
    function osc_latest_search_text() {
        return osc_field(osc_latest_search(), 's_search', '');
    }

    /**
     * Gets the current latest search date
     *
     * @return string
     */
    function osc_latest_search_date() {
        return osc_field(osc_latest_search(), 'd_date', '');
    }

    /**
     * Gets the current latest search total
     *
     * @return string
     */
    function osc_latest_search_total() {
        return osc_field(osc_latest_search(), 'i_total', '');
    }

?>
