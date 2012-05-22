<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.') ;

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
     * Model database for Item table
     * 
     * @package OSClass
     * @subpackage Model
     * @since unknown
     */
    class Item extends DAO
    {
        /**
         * It references to self object: Item.
         * It is used as a singleton
         * 
         * @access private
         * @since unknown
         * @var Item 
         */
        private static $instance ;
        
        /**
         * It creates a new Item object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since unknown
         * @return Item 
         */
        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Set data related to t_item table
         */
        function __construct()
        {
            parent::__construct();
            $this->setTableName('t_item') ;
            $this->setPrimaryKey('pk_i_id') ;
            $array_fields = array(
                'pk_i_id',
                'fk_i_user_id',
                'fk_i_category_id',
                'dt_pub_date',
                'dt_mod_date',
                'f_price',
                'i_price',
                'fk_c_currency_code',
                's_contact_name',
                's_contact_email',
                'b_premium',
                'b_enabled',
                'b_active',
                'b_spam',
                's_secret',
                'b_show_email',
                'd_expiration'
            );
            $this->setFields($array_fields) ;
        }
        
        /**
         * List items ordered by views
         *
         * @access public
         * @since unknown
         * @param int $limit
         * @return array of items
         */
        public function mostViewed($limit = 10) 
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName().' i, '.DB_TABLE_PREFIX.'t_item_location l, '.DB_TABLE_PREFIX.'t_item_stats s') ;
            $this->dao->where('l.fk_i_item_id = i.pk_i_id AND s.fk_i_item_id = i.pk_i_id') ;
            $this->dao->groupBy('s.fk_i_item_id') ;
            $this->dao->orderBy('i_num_views', 'DESC') ;
            $this->dao->limit($limit) ;
            
            $result = $this->dao->get() ;
            $items  = $result->result() ;
            
            return $this->extendData($items);
        }
        
        /**
         * Get the result match of the primary key passed by parameter, extended with
         * location information and number of views.
         *
         * @access public
         * @since unknown
         * @param int $id Item id
         * @return array 
         */
        public function findByPrimaryKey($id)
        {
            $this->dao->select('l.*, i.*, SUM(s.i_num_views) AS i_num_views') ;
            $this->dao->from($this->getTableName().' i') ;
            $this->dao->join(DB_TABLE_PREFIX.'t_item_location l', 'l.fk_i_item_id = i.pk_i_id ', 'LEFT') ;
            $this->dao->join(DB_TABLE_PREFIX.'t_item_stats s', 'i.pk_i_id = s.fk_i_item_id', 'LEFT') ;
            $this->dao->where('i.pk_i_id', $id) ;
            $this->dao->groupBy('s.fk_i_item_id') ;
            $result = $this->dao->get() ;
            
            if($result === false) {
                return false;
            }
            
            if( $result->numRows() == 0 ) {
                return array();
            }
            
            $item   = $result->row() ; 

            if(!is_null($item) ) {
                return $this->extendDataSingle($item);
            } else {
                return array();
            }
        }
        
        /**
         * List Items with category name
         *
         * @access public
         * @since unknown
         * @return array of items
         */
        public function listAllWithCategories()
        {
            $this->dao->select('i.*, cd.s_name AS s_category_name ') ;
            $this->dao->from($this->getTableName().' i, '.DB_TABLE_PREFIX.'t_category c, '.DB_TABLE_PREFIX.'t_category_description cd') ;
            $this->dao->where('c.pk_i_id = i.fk_i_category_id AND cd.fk_i_category_id = i.fk_i_category_id') ;
            $result = $this->dao->get() ;
            
            return $result->result() ;
        }
        
        /**
         * Comodin function to serve multiple queries
         *
         * @access public
         * @since unknown
         * @return array of items
         */
        public function listWhere()
        {
            $argv = func_get_args();
            $sql = null;
            switch (func_num_args ()) {
                case 0: return array();
                    break;
                case 1: $sql = $argv[0];
                    break;
                default:
                    $args = func_get_args();
                    $format = array_shift($args);
                    $sql = vsprintf($format, $args);
                    break;
            }
            
            $this->dao->select('l.*, i.*') ;
            $this->dao->from($this->getTableName().' i, '.DB_TABLE_PREFIX.'t_item_location l') ;
            $this->dao->where('l.fk_i_item_id = i.pk_i_id') ;
            $this->dao->where($sql) ;
            $result = $this->dao->get() ;
            $items  = $result->result() ;
            
            return $this->extendData($items);
        }
        
        /**
         * Find item resources belong to an item given its id
         *
         * @access public
         * @since unknown
         * @param int $id Item id
         * @return array of resources
         */
        public function findResourcesByID($id)
        {
            return ItemResource::newInstance()->getResources($id);
        }

        /**
         * Find the item location given a item id
         *
         * @access public
         * @since unknown
         * @param int $id Item id
         * @return array of location 
         */
        public function findLocationByID($id)
        {
            $this->dao->select() ;
            $this->dao->from(DB_TABLE_PREFIX.'t_item_location') ;
            $this->dao->where('fk_i_item_id', $id) ;
            $result = $this->dao->get() ;
            
            return $result->row();
        }

        /**
         * Find items belong to a category given its id
         *
         * @access public
         * @since unknown
         * @param int $catId
         * @return array of items
         */
        public function findByCategoryID($catId)
        {
            return $this->listWhere('fk_i_category_id = %d', $catId);
        }
        
        /**
         * Find items belong to an email
         * 
         * @access public
         * @since unknown
         * @param type $email
         * @return type 
         */
        public function findByEmail($email)
        {
            return $this->listWhere("s_contact_email = '%s'", $email);
        }
        
        /**
         * Count all items, or all items belong to a category id, can be filtered
         * by $active  ['ACTIVE'|'INACTIVE'|'SPAM']
         *
         * @access public
         * @since unknown
         * @param type $categoryId
         * @param string $active 
         * @return int total items
         */
        public function totalItems($categoryId = null, $active = null)
        {
            $this->dao->select('count(*) as total') ;
            $this->dao->from($this->getTableName().' i') ;
            if(!is_null($categoryId)) {
                $this->dao->join(DB_TABLE_PREFIX.'t_category c', 'c.pk_i_id = i.fk_i_category_id') ;
                $this->dao->where('i.fk_i_category_id', $categoryId) ;
            }
            
            $conditions = '';
            if (!is_null($active)) {
                switch ($active) {
                    case 'ACTIVE':  
                        $this->dao->where('b_active', 1);
                    break;
                    case 'INACTIVE':   
                        $this->dao->where('b_active', 0);
                    break;
                    case 'ENABLE':  
                        $this->dao->where('b_enabled', 1);
                    break;
                    case 'DISABLED':   
                        $this->dao->where('b_enabled', 0);
                    break;
                    case 'SPAM':   
                        $this->dao->where('b_spam', 1);
                    break;
                    default:
                }   
            }

            $result = $this->dao->get() ;
            $total_ads = $result->row() ;
            return $total_ads['total'];
        }

        public function numItems($category, $enabled = true, $active = true)
        {
            $this->dao->select( 'COUNT(*) AS total' ) ;
            $this->dao->from( $this->getTableName() ) ;
            $this->dao->where( 'fk_i_category_id', (int)$category['pk_i_id'] ) ;
            $this->dao->where( 'b_enabled', $enabled ) ;
            $this->dao->where( 'b_active', $active ) ;
            $this->dao->where( 'b_spam', 0 ) ;
            
            $this->dao->where( '( b_premium = 1 || dt_expiration >= \'' . date('Y-m-d H:i:s') .'\' )' ) ;
            
            $result = $this->dao->get() ;

            if( $result == false ) {
                return 0 ;
            }

            if( $result->numRows() == 0 ) {
                return 0 ;
            }

            $row = $result->row() ;
            return $row['total'] ;
        }

        // LEAVE THIS FOR COMPATIBILITIES ISSUES (ONLY SITEMAP GENERATOR)
        // BUT REMEMBER TO DELETE IN ANYTHING > 2.1.x THANKS
        public function listLatest($limit = 10)
        {
            return $this->listWhere(" b_active = 1 AND b_enabled = 1 ORDER BY dt_pub_date DESC LIMIT " . $limit);
        }
        
        /**
         * Insert title and description for a given locale and item id.
         * 
         * @access public
         * @since unknown
         * @param string $id Item id
         * @param string $locale
         * @param string $title
         * @param string $description
         * @return boolean
         */
        public function insertLocale($id, $locale, $title, $description)
        {
            $array_set   = array(
                'fk_i_item_id'      => $id,
                'fk_c_locale_code'  => $locale,
                's_title'           => $title,
                's_description'     => $description
            ) ;
            return $this->dao->insert(DB_TABLE_PREFIX.'t_item_description', $array_set) ;
        }
        
        /**
         * Find items belong to an user given its id
         *
         * @access public
         * @since unknown
         * @param int $userId User id
         * @param int $start begining
         * @param int $end ending
         * @return array of items
         */
        public function findByUserID($userId, $start = 0, $end = null)
        {
            $this->dao->select('l.*, i.*') ;
            $this->dao->from($this->getTableName().' i, '.DB_TABLE_PREFIX.'t_item_location l') ;
            $this->dao->where('l.fk_i_item_id = i.pk_i_id');
            $array_where = array(
                'i.fk_i_user_id' => $userId
            );
            $this->dao->where($array_where) ;
            $this->dao->orderBy('i.pk_i_id', 'DESC') ;
            if($end!=null) {
                $this->dao->limit($start, $end) ;
            } else {
                if ($start > 0 ) {
                    $this->dao->limit($start) ;
                }
            }
            
            $result = $this->dao->get() ;
            $items  = $result->result() ;
            
            return $this->extendData($items) ;
        }

        /**
         * Count items belong to an user given its id
         *
         * @access public
         * @since unknown
         * @param int $userId User id
         * @return int number of items
         */
        public function countByUserID($userId)
        {
            $this->dao->select('count(i.pk_i_id) as total') ;
            $this->dao->from($this->getTableName().' i') ;
            $this->dao->where('i.fk_i_user_id', $userId) ;
            $this->dao->orderBy('i.pk_i_id', 'DESC') ;
            
            $result = $this->dao->get() ;
            $total_ads = $result->row() ;
            return $total_ads['total'];
        }
        
        /**
         * Find enabled items belong to an user given its id
         *
         * @access public
         * @since unknown
         * @param int $userId User id
         * @param int $start beginning from $start
         * @param int $end ending 
         * @return array of items
         */
        public function findByUserIDEnabled($userId, $start = 0, $end = null)
        {
            $this->dao->select('l.*, i.*') ;
            $this->dao->from($this->getTableName().' i, '.DB_TABLE_PREFIX.'t_item_location l') ;
            $this->dao->where('l.fk_i_item_id = i.pk_i_id');
            $array_where = array(
                'i.b_enabled'       => 1,
                'i.fk_i_user_id' => $userId
            );
            $this->dao->where($array_where) ;
            $this->dao->orderBy('i.pk_i_id', 'DESC') ;
            if($end!=null) {
                $this->dao->limit($start, $end) ;
            } else if ($start > 0 ) {
                $this->dao->limit($start) ;
            }
                
            $result = $this->dao->get() ;
            $items  = $result->result() ;
            return $this->extendData($items);
        }

        /**
         * Count enabled items belong to an user given its id
         *
         * @access public
         * @since unknown
         * @param int $userId User id
         * @return int number of items
         */
        public function countByUserIDEnabled($userId)
        {
            $this->dao->select('count(i.pk_i_id) as total') ;
            $this->dao->from($this->getTableName().' i') ;
            $array_where = array(
                'i.b_enabled'     => 1,
                'i.fk_i_user_id'  => $userId
            );
            $this->dao->where($array_where) ;
            $this->dao->orderBy('i.pk_i_id', 'DESC') ;
            
            $result = $this->dao->get() ;
            $items  = $result->row() ;
            return $items['total'];
        }
        
        /**
         * Clear item stat given item id and stat to clear
         * $stat array('spam', 'duplicated', 'bad', 'offensive', 'expired')
         *
         * @access public
         * @since unknown
         * @param int $id
         * @param string $stat
         * @return mixed int if updated correctly or false when error occurs
         */
        public function clearStat($id, $stat)
        {
            switch($stat) {
                case 'spam':
                    $array_set  = array('i_num_spam' => 0);
                    break;
                case 'duplicated':
                    $array_set  = array('i_num_repeated' => 0);
                    break;
                case 'bad':
                    $array_set  = array('i_num_bad_classified' => 0);
                    break;
                case 'offensive':
                    $array_set  = array('i_num_offensive' => 0);
                    break;
                case 'expired':
                    $array_set  = array('i_num_expired' => 0);
                    break;
                default:
                    break;
            }
            $array_conditions = array('fk_i_item_id' => $id);
            return $this->dao->update(DB_TABLE_PREFIX.'t_item_stats', $array_set, $array_conditions);
        }
        
        /**
         * Update title and description given a item id and locale.
         *
         * @access public
         * @since unknown
         * @param int $id
         * @param string $locale
         * @param string $title
         * @param string $text
         * @return bool 
         */
        public function updateLocaleForce($id, $locale, $title, $text)
        {
            $array_replace = array(
                's_title'           => $title,
                's_description'     => $text,
                'fk_c_locale_code'  => $locale,
                'fk_i_item_id'      => $id
            );
            return $this->dao->replace(DB_TABLE_PREFIX.'t_item_description', $array_replace) ;
        }        
        
        /**
         * Update dt_expiration field, using $i_expiration_days 
         * 
         * @param type $i_expiration_days 
         * @return string new date expiration, false if error occurs
         */
        public function updateExpirationDate($id, $i_expiration_days) 
        {
            if($i_expiration_days > 0) {
                $sql =  sprintf("UPDATE %s SET dt_expiration = ", $this->getTableName());
                $sql .= sprintf(' date_add(%s.dt_pub_date, INTERVAL %d DAY) ', $this->getTableName(), $i_expiration_days) ;
                $sql .= sprintf(' WHERE pk_i_id = %d', $id);
            } else {
                $sql = sprintf("UPDATE %s SET dt_expiration = '9999-12-31 23:59:59'  WHERE pk_i_id = %d", $this->getTableName(), $id);
            }
            
            $result = $this->dao->query($sql);
            
            if($result && $result>0) {
                $this->dao->select('dt_expiration');
                $this->dao->from($this->getTableName());
                $this->dao->where('pk_i_id', (int)$id );
                $result = $this->dao->get();
                
                if($result && $result->result()>0) {
                    $_item = $result->row();
                    return $_item['dt_expiration'];
                }
                return false;
            }
            return false;
        }
            
        public function enableByCategory($enable, $aIds)
        {
            $sql  = sprintf('UPDATE %st_item SET b_enabled = %d WHERE ', DB_TABLE_PREFIX, $enable );
            $sql .= sprintf('%st_item.fk_i_category_id IN (%s)', DB_TABLE_PREFIX, implode(',', $aIds) );
            
            return $this->dao->query($sql);
        }
        /**
         * Return meta fields for a given item
         *
         * @access public
         * @since unknown
         * @param int $id Item id
         * @return array meta fields array
         */
        public function metaFields($id)
        {
            $this->dao->select('im.s_value as s_value,mf.pk_i_id as pk_i_id, mf.s_name as s_name, mf.e_type as e_type') ;
            $this->dao->from($this->getTableName().' i, '.DB_TABLE_PREFIX.'t_item_meta im, '.DB_TABLE_PREFIX.'t_meta_categories mc, '.DB_TABLE_PREFIX.'t_meta_fields mf') ;
            $this->dao->where('mf.pk_i_id = im.fk_i_field_id');
            $this->dao->where('mf.pk_i_id = mc.fk_i_field_id');
            $this->dao->where('mc.fk_i_category_id = i.fk_i_category_id');
            $array_where = array(
                'im.fk_i_item_id'       => $id,
                'i.pk_i_id'             => $id
            );
            $this->dao->where($array_where) ;
            $result = $this->dao->get() ;
            return $result->result() ;
        }
        
        /**
         * Delete by primary key, delete dependencies too
         *
         * @access public
         * @since unknown
         * @param int $id Item id
         * @return bool
         */
        public function deleteByPrimaryKey($id)
        {             
            $item = $this->findByPrimaryKey($id) ;

            if ( is_null($item) ) {
                return false ;
            }

            if( $item['b_active'] == 1 && $item['b_enabled']==1 && $item['b_spam']==0 && !osc_isExpired($item['dt_expiration'])) {
                if($item['fk_i_user_id']!=null) {
                    User::newInstance()->decreaseNumItems($item['fk_i_user_id']);
                }
                CategoryStats::newInstance()->decreaseNumItems($item['fk_i_category_id']) ;
                CountryStats::newInstance()->decreaseNumItems($item['fk_c_country_code']);
                RegionStats::newInstance()->decreaseNumItems($item['fk_i_region_id']);
                CityStats::newInstance()->decreaseNumItems($item['fk_i_city_id']);
            }

            $this->dao->delete(DB_TABLE_PREFIX.'t_item_description', "fk_i_item_id = $id") ;
            $this->dao->delete(DB_TABLE_PREFIX.'t_item_comment' , "fk_i_item_id = $id") ;
            $this->dao->delete(DB_TABLE_PREFIX.'t_item_resource', "fk_i_item_id = $id") ;
            $this->dao->delete(DB_TABLE_PREFIX.'t_item_location', "fk_i_item_id = $id") ;
            $this->dao->delete(DB_TABLE_PREFIX.'t_item_stats'   , "fk_i_item_id = $id") ;
            $this->dao->delete(DB_TABLE_PREFIX.'t_item_meta'    , "fk_i_item_id = $id") ;
            
            osc_run_hook('delete_item', $id) ;
            
            $res = parent::deleteByPrimaryKey($id);
            return $res ;  
        }
        
        /**
         * Delete by city
         *
         * @access public
         * @since unknown
         * @param int $cityId city id
         * @return bool
         */
        public function deleteByCity($cityId)
        {
            $this->dao->select('fk_i_item_id');
            $this->dao->from(DB_TABLE_PREFIX.'t_item_location') ;
            $this->dao->where('fk_i_city_id', $cityId) ;
            $result = $this->dao->get() ;
            $items  = $result->result() ;
            $arows = 0;
            foreach($items as $i) {
                $arows += $this->deleteByPrimaryKey($i['fk_i_item_id']);
            }
            return $arows;
        }
        
        /**
         * Delete by region
         *
         * @access public
         * @since unknown
         * @param int $regionId region id
         * @return bool
         */
        public function deleteByRegion($regionId)
        {
            $this->dao->select('fk_i_item_id');
            $this->dao->from(DB_TABLE_PREFIX.'t_item_location') ;
            $this->dao->where('fk_i_region_id', $regionId) ;
            $result = $this->dao->get() ;
            $items  = $result->result() ;
            $arows = 0;
            foreach($items as $i) {
                $arows += $this->deleteByPrimaryKey($i['fk_i_item_id']);
            }
            return $arows;
        }
        
        /**
         * Delete by country
         *
         * @access public
         * @since unknown
         * @param int $countryId country id
         * @return bool
         */
        public function deleteByCountry($countryId)
        {
            $this->dao->select('fk_i_item_id');
            $this->dao->from(DB_TABLE_PREFIX.'t_item_location') ;
            $this->dao->where('fk_c_country_code', $countryId) ;
            $result = $this->dao->get() ;
            $items  = $result->result() ;
            $arows = 0;
            foreach($items as $i) {
                $arows += $this->deleteByPrimaryKey($i['fk_i_item_id']);
            }
            return $arows;
        }
        
        /**
         * Extends the given array $item with description in available locales
         *  
         * @access public
         * @since unknown
         * @param array $item
         * @return array item array with description in available locales
         */
        public function extendDataSingle($item)
        {
            $prefLocale = osc_current_user_locale();

            $this->dao->select() ;
            $this->dao->from(DB_TABLE_PREFIX.'t_item_description') ;
            $this->dao->where('fk_i_item_id', $item['pk_i_id']) ;
            $result = $this->dao->get() ;
            $descriptions = $result->result() ;
            
            $item['locale'] = array();
            foreach ($descriptions as $desc) {
                if ($desc['s_title'] != "" || $desc['s_description'] != "") {
                    $item['locale'][$desc['fk_c_locale_code']] = $desc;
                }
            }
            $is_itemLanguageAvailable = (!empty($item['locale'][$prefLocale]['s_title'])
                                         && !empty($item['locale'][$prefLocale]['s_description']));
            if (isset($item['locale'][$prefLocale]) && $is_itemLanguageAvailable) {
                $item['s_title'] = $item['locale'][$prefLocale]['s_title'];
                $item['s_description'] = $item['locale'][$prefLocale]['s_description'];
            } else {
                $aCategory = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);

                $title = sprintf(__('%s in'), $aCategory['s_name']);
                if(isset($item['s_city'])) {
                    $title .= ' ' . $item['s_city'];
                } else if(isset($item['s_region'])) {
                    $title .= ' ' .$item['s_region'];
                } else if(isset($item['s_country'])) {
                    $title .= ' ' . $item['s_country'];
                }
                $item['s_title'] = $title;
                $item['s_description'] = __('There\'s no description available in your language');
                unset($data);
            }
            return $item;
        }
        
        /**
         * Extends the given array $items with category name , and description in available locales
         *
         * @access public
         * @since unknown
         * @param array $items array with items
         * @return array with category name 
         */
        public function extendCategoryName($items)
        {
            if( OC_ADMIN ) {
                $prefLocale = osc_current_admin_locale();
            } else {
                $prefLocale = osc_current_user_locale();
            }

            $results = array();
            foreach ($items as $item) {
                $this->dao->select('fk_c_locale_code, s_name as s_category_name') ;
                $this->dao->from(DB_TABLE_PREFIX.'t_category_description') ;
                $this->dao->where('fk_i_category_id', $item['fk_i_category_id']) ;
                $result = $this->dao->get() ;
                $descriptions = $result->result() ;
                
                foreach ($descriptions as $desc) {
                    $item['locale'][$desc['fk_c_locale_code']]['s_category_name'] = $desc['s_category_name'];
                }
                if (isset($item['locale'][$prefLocale]['s_category_name'])) {
                    $item['s_category_name'] = $item['locale'][$prefLocale]['s_category_name'];
                } else {
                    $data = current($item['locale']);
                    if( isset($data['s_category_name']) ) {
                        $item['s_category_name'] = $data['s_category_name'];
                    } else {
                        $item['s_category_name'] = '';
                    }
                    unset($data);
                }
                $results[] = $item;
            }
            return $results;
        }
        
        /**
         * Extends the given array $items with description in available locales
         *
         * @access public
         * @since unknown
         * @param type $items
         * @return array with description extended with all available locales 
         */
        public function extendData($items)
        {
            if( OC_ADMIN ) {
                $prefLocale = osc_current_admin_locale();
            } else {
                $prefLocale = osc_current_user_locale();
            }

            $results = array();
            
            foreach ($items as $item) {
                $this->dao->select() ;
                $this->dao->from(DB_TABLE_PREFIX.'t_item_description') ;
                $this->dao->where(DB_TABLE_PREFIX.'t_item_description.fk_i_item_id', $item['pk_i_id']) ;
                
                $result = $this->dao->get() ;
                $descriptions = $result->result() ;
                
                $item['locale'] = array();
                foreach ($descriptions as $desc) {
                    if ($desc['s_title'] != "" || $desc['s_description'] != "") {
                        $item['locale'][$desc['fk_c_locale_code']] = $desc;
                    }
                }
                if (isset($item['locale'][$prefLocale])) {
                    $item['s_title'] = $item['locale'][$prefLocale]['s_title'];
                    $item['s_description'] = $item['locale'][$prefLocale]['s_description'];
                } else {
                    $data = current($item['locale']);
                    $item['s_title'] = $data['s_title'];
                    $item['s_description'] = $data['s_description'];
                    unset($data);
                }

                // populate locations and category_name
                $this->dao->select(DB_TABLE_PREFIX.'t_item_location.*, '.DB_TABLE_PREFIX.'t_item_stats.*, cd.s_name as s_category_name') ;
                $this->dao->from(DB_TABLE_PREFIX.'t_item_location') ;
                $this->dao->from(DB_TABLE_PREFIX.'t_category_description as cd') ;
                $this->dao->from(DB_TABLE_PREFIX.'t_item_stats') ;
                $this->dao->where(DB_TABLE_PREFIX.'t_item_location.fk_i_item_id', $item['pk_i_id']) ;
                $this->dao->where(DB_TABLE_PREFIX.'t_item_stats.fk_i_item_id', $item['pk_i_id']) ;
                $this->dao->where('cd.fk_i_category_id', $item['fk_i_category_id']) ;
                
                $result = $this->dao->get() ;
                $extraFields = $result->row() ;
                
                foreach($extraFields as $key => $value) {
                    $item[$key] = $value;
                }
                
                $results[] = $item;
            }
            return $results;
        }
    }

    /* file end: ./oc-includes/osclass/model/Item.php */
?>