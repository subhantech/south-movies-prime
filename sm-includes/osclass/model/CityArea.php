<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.') ;

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
     * Model database for CityArea table
     * 
     * @package OSClass
     * @subpackage Model
     * @since unknown
     */
    class CityArea extends DAO
    {
        /**
         * It references to self object: CityArea.
         * It is used as a singleton
         * 
         * @access private
         * @since unknown
         * @var CityArea
         */
        private static $instance ;

        /**
         * It creates a new CityArea object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since unknown
         * @return CityArea 
         */
        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Set data related to t_city_area table
         */
        function __construct()
        {
            parent::__construct();
            $this->setTableName('t_city_area') ;
            $this->setPrimaryKey('pk_i_id') ;
            $this->setFields( array('pk_i_id', 'fk_i_city_id', 's_name') ) ;
        }

        /**
         * Get the cityArea by its name and city
         * 
         * @access public
         * @since unknown
         * @param string $query
         * @param int $regionId
         * @return array 
         */
        function findByName($cityAreaName, $cityId = null)
        {
            $this->dao->select($this->getFields()) ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('s_name', $cityAreaName) ;
            $this->dao->limit(1) ;
            if( $regionId != null ) {
                $this->dao->where('fk_i_city_id', $cityId) ;
            }

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            return $result->row() ;
        }
        
        /**
         * Return city areas of a given city ID
         * 
         * @access public
         * @since 2.4
         * @param $cityId
         * @return array
         */
        function findByCity($cityId) {
            $this->dao->select($this->getFields()) ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('fk_i_city_id', $cityId) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            return $result->result();
        }
    }

    /* file end: ./oc-includes/osclass/model/CityArea.php */
?>