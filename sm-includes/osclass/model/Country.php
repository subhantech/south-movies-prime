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
     * Model database for Country table
     * 
     * @package OSClass
     * @subpackage Model
     * @since unknown
     */
    class Country extends DAO
    {
        /**
         *
         * @var Country 
         */
        private static $instance ;

        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * 
         */
        function __construct()
        {
            parent::__construct();
            $this->setTableName('t_country') ;
            $this->setPrimaryKey('pk_c_code') ;
            $this->setFields( array('pk_c_code', 's_name') ) ;
        }

        /**
         * Find a country by its ISO code
         * 
         * @access public
         * @since unknown
         * @param type $code
         * @return array
         */
        public function findByCode($code)
        {
            $this->dao->select('*') ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('pk_c_code', $code) ;
            $result = $this->dao->get() ;
            
            return $result->row(); 
        }

        /**
         * Find a country by its name
         * 
         * @access public
         * @since unknown
         * @param type $name
         * @return array
         */
        public function findByName($name)
        {
            $this->dao->select('*') ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('s_name', $name) ;
            $result = $this->dao->get() ;
            return $result->row();
        }

        /**
         * List all the countries
         * 
         * @access public
         * @since unknown
         * @param type $language
         * @return array
         */
        public function listAll() {
            $result = $this->dao->query(sprintf('SELECT * FROM %st_country ORDER BY s_name ASC', DB_TABLE_PREFIX));
            return $result->result();
        }

        /**
         * Function that work with the ajax file
         * 
         * @access public
         * @since unknown
         * @param type $query
         * @return array
         */
        public function ajax($query)
        {
            $this->dao->select('pk_c_code as id, s_name as label, s_name as value') ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->like('s_name', $query, 'after') ;
            $this->dao->limit(5);
            $result = $this->dao->get() ;
            return $result->result();
        }
        
        
        /**
         *  Delete a country with its regions, cities,..
         * 
         *  @access public
         *  @since 2.4
         *  @param $pk
         *  @return boolean
         */
        function deleteByPrimaryKey($pk) {
            $mRegions = Region::NewInstance();
            $aRegions = $mRegions->findByCountry($pk);
            $mCities = City::newInstance();
            $mCityAreas = CityArea::newInstance();
            $result = true;
            foreach($aRegions as $region) {
                $aCities = $mCities->findByRegion($region['pk_i_id']);
                foreach($aCities as $city) {
                    $aCityAreas = $mCityAreas->findByCity($city['pk_i_id']);
                    foreach($aCityAreas as $cityArea) {
                        if(!$mCityAreas->delete(array('pk_i_id' => $cityArea['pk_i_id']))) {
                            $result = false;
                        };
                    }
                    if(!$mCities->delete(array('pk_i_id' => $city['pk_i_id']))) {
                        $result = false;
                    };
                }
                if(!$mRegions->delete(array('pk_i_id' => $region['pk_i_id']))) {
                    $result = false;
                };
            }
            if(!$this->delete(array('pk_c_code' => $pk))) {
                $result = false;
            }
            return $result;
        }
    }

    /* file end: ./oc-includes/osclass/model/Country.php */
?>