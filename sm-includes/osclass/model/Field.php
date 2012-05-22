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
     * Model database for Field table
     * 
     * @package OSClass
     * @subpackage Model
     * @since unknown
     */
    class Field extends DAO
    {
        /**
         * It references to self object: Field.
         * It is used as a singleton
         * 
         * @access private
         * @since unknown
         * @var Field 
         */
        private static $instance ;

        /**
         * It creates a new Field object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since unknown
         * @return Field
         */
        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Set data related to t_meta_fields table
         */
        function __construct()
        {
            parent::__construct();
            $this->setTableName('t_meta_fields') ;
            $this->setPrimaryKey('pk_i_id') ;
            $this->setFields( array('pk_i_id', 's_name', 'e_type', 'b_required', 's_slug', 's_options') ) ;
        }

        /**
         * Find a field by its id.
         * 
         * @access public
         * @since unknown
         * @param int $id
         * @return array Field information. If there's no information, return an empty array.
         */
        public function findByPrimaryKey($id)
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('pk_i_id', $id) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            return $result->row() ;
        }

        /**
         * Find a field by its name
         *
         * @access public
         * @since unknown
         * @param string $id
         * @return array Field information. If there's no information, return an empty array.
         */
        public function findByCategory($id)
        {
            $this->dao->select('mf.*') ;
            $this->dao->from(sprintf('%st_meta_fields mf, %st_meta_categories mc', DB_TABLE_PREFIX, DB_TABLE_PREFIX)) ;
            $this->dao->where('mc.fk_i_category_id', $id) ;
            $this->dao->where('mf.pk_i_id = mc.fk_i_field_id');

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            return $result->result() ;
        }

        /**
         * Find fields from a category and an item
         *
         * @access public
         * @since unknown
         * @param string $id
         * @return array Field information. If there's no information, return an empty array.
         */
        public function findByCategoryItem($catId, $itemId)
        {
            if( !is_numeric($catId) || (!is_numeric($itemId) && $itemId != null) ) {
                return array() ;
            }

            $result = $this->dao->query(sprintf("SELECT query.*, im.s_value as s_value FROM (SELECT mf.* FROM %st_meta_fields mf, %st_meta_categories mc WHERE mc.fk_i_category_id = %d AND mf.pk_i_id = mc.fk_i_field_id) as query LEFT JOIN %st_item_meta im ON im.fk_i_field_id = query.pk_i_id AND im.fk_i_item_id = %d", DB_TABLE_PREFIX, DB_TABLE_PREFIX, $catId, DB_TABLE_PREFIX, $itemId));

            if( $result == false ) {
                return array() ;
            }

            return $result->result() ;
        }

        /**
         * Find a field by its name
         *
         * @access public
         * @since unknown
         * @param string $name
         * @return array Field information. If there's no information, return an empty array.
         */
        public function findByName($name)
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('s_name', $name) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            return $result->row();
        }

        /**
         * Find a field by its name
         *
         * @access public
         * @since unknown
         * @param string $slug
         * @return array Field information. If there's no information, return an empty array.
         */
        public function findBySlug($slug)
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('s_slug', $slug) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            return $result->row();
        }

        /**
         * Gets which categories are associated with that field
         *
         * @access public
         * @since unknown
         * @param string $id
         * @return array
         */
        public function categories($id)
        {
            $this->dao->select('fk_i_category_id') ;
            $this->dao->from(sprintf('%st_meta_categories', DB_TABLE_PREFIX)) ;
            $this->dao->where('fk_i_field_id', $id) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            $categories = $result->result();
            $cats = array();
            foreach($categories as $k => $v) {
                $cats[] = $v['fk_i_category_id'];
            }
            return $cats;
        }

        /**
         * Insert a new field
         * 
         * @access public
         * @since unknown
         * @param string $name
         * @param string $type
         * @param string $slug
         * @param bool $required
         * @param array $options
         * @param array $categories 
         */
        public function insertField($name, $type, $slug, $required, $options, $categories = null) {
            if($slug=='') {
                $slug = preg_replace('|([-]+)|', '-', preg_replace('|[^a-z0-9_-]|', '-', strtolower($name)));
            }
            $slug_tmp = $slug;
            $slug_k = 0;
            while(true) {
                if(!$this->findBySlug($slug)) {
                    break;
                } else {
                    $slug_k++;
                    $slug = $slug_tmp."_".$slug_k;
                }
            }
            $this->dao->insert($this->getTableName(), array("s_name" => $name, "e_type" =>$type, "b_required" => $required, "s_slug" => $slug, 's_options' => $options));
            $id = $this->dao->insertedId();
            $return = true;
            foreach($categories as $c) {
                $result = $this->dao->insert(sprintf('%st_meta_categories', DB_TABLE_PREFIX), array('fk_i_category_id' => $c, 'fk_i_field_id' =>$id));
                if(!$result) { $return = false; };
            }
            return $return;
        }
        
        
        /**
         * Save the categories linked to a field
         * 
         * @access public
         * @since unknown
         * @param int $id
         * @param array $categories 
         * @return bool
         */
        public function insertCategories($id, $categories = null) {
            if($categories!=null) {
                $return = true;
                foreach($categories as $c) {
                    $result = $this->dao->insert(sprintf('%st_meta_categories', DB_TABLE_PREFIX), array('fk_i_category_id' => $c, 'fk_i_field_id' =>$id));
                    if(!$result) {
                        $return = false;
                    }
                }
                return $return;
            }
            return false;
        }
        
        /**
         * Removes categories from a field
         * 
         * @access public
         * @since unknown
         * @param int $id
         * @return bool on success
         */
        public function cleanCategoriesFromField($id) {
            return $this->dao->delete(sprintf('%st_meta_categories', DB_TABLE_PREFIX), array('fk_i_field_id' =>$id));
        }
        
        /**
         * Update a field value
         * 
         * @access public
         * @since unknown
         * @param int $itemId
         * @param int $field
         * @param string $value
         * @return mixed false on fail, int of num. of affected rows
         */
        public function replace($itemId, $field, $value) {
            return $this->dao->replace(sprintf('%st_item_meta', DB_TABLE_PREFIX), array('fk_i_item_id' => $itemId, 'fk_i_field_id' => $field, 's_value' => $value));
        }

        /**
         * Delete a field and all information associated with it
         * 
         * @access public
         * @since unknown
         * @param int $id
         * @return bool on success
         */
        public function deleteByPrimaryKey($id) {
            $this->dao->delete(sprintf('%st_item_meta', DB_TABLE_PREFIX), array('fk_i_field_id' =>$id));
            $this->dao->delete(sprintf('%st_meta_categories', DB_TABLE_PREFIX), array('fk_i_field_id' =>$id));
            return $this->dao->delete($this->getTableName(), array('pk_i_id' =>$id));
        }
    }

    /* file end: ./oc-includes/osclass/model/Field.php */
?>