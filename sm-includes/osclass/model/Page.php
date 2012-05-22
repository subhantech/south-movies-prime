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
     * Page DAO
     */
    class Page extends DAO
    {
        /**
         *
         * @var type 
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
            parent::__construct() ;
            $this->setTableName('t_pages') ;
            $this->setPrimaryKey('pk_i_id') ;
            $array_fields = array(
                'pk_i_id',
                's_internal_name',
                'b_indelible',
                'dt_pub_date', 
                'dt_mod_date', 
                'i_order') ;
            $this->setFields($array_fields) ;
        }
        
        /**
         * Find a page by page id.
         *
         * @access public
         * @since unknown
         * @param int $id Page id.
         * @param string $locale By default is null but you can specify locale code.
         * @return array Page information. If there's no information, return an empty array.
         */
        function findByPrimaryKey($id, $locale = null)
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('pk_i_id', $id) ;
            $result = $this->dao->get() ;
            
            $row = $result->row() ;

            if( $result == false ) {
                return false ;
            }

            if( $result->numRows() == 0 ) {
                return false ;
            }
            
            $row = $result->row() ;

            // page_description
            $this->dao->select() ;
            $this->dao->from($this->getDescriptionTableName()) ;
            $this->dao->where('fk_i_pages_id', $id) ;
            if( !is_null($locale) ) {
                $this->dao->where('fk_c_locale_code', $locale) ;
            }
            $result   = $this->dao->get() ;
            $aRows = $result->result() ;

            $row['locale'] = array() ;
            foreach($aRows as $r) {
                $row['locale'][$r['fk_c_locale_code']] = $r ;
            }

            return $row ;
        }
        
        /**
         * Find a page by internal name.
         *
         * @access public
         * @since unknown
         * @param string $intName Internal name of the page to find.
         * @param string $locale Locale string.
         * @return array It returns page fields. If it has no results, it returns an empty array.
         */
        function findByInternalName($intName, $locale = null)
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName()) ;
            $this->dao->where('s_internal_name', $intName) ;
            $result = $this->dao->get() ;
            
            
            if( $result == false ) {
                return array() ;
            }

            if( $result->numRows() == 0 ){
                return array() ;
            }
            
            $row = $result->row() ;
            return $this->extendDescription($row, $locale) ;
        }
        
        /**
         * Find a page by order.
         *
         * @access public
         * @since unknown
         * @param int order
         * @return array It returns page fields. If it has no results, it returns an empty array.
         */
        function findByOrder($order, $locale = null)
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName()) ;
            $array_where = array(
                'i_order'     => $order,
                'b_indelible' => 0
            );
            $this->dao->where($array_where) ;
            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            if( $result->numRows() == 0 ) {
                return array() ;
            }

            $row    = $result->row() ;
            $result = $this->extendDescription($row, $locale) ;
            return $result ;
        }

        /**
         * Get all the pages with the parameters you choose.
         *
         * @access public
         * @since unknown
         * @param bool $indelible It's true if the page is indelible and false if not.
         * @param string $locale It's
         * @param int $start
         * @param int $limit
         * @return array Return all the pages that have been found with the criteria selected. If there's no pages, the
         * result is an empty array.
         */
        public function listAll($indelible = null, $locale = null, $start = null, $limit = null)
        {
            $this->dao->select() ;
            $this->dao->from($this->getTableName()) ;
            if( !is_null($indelible) ) {
                $this->dao->where('b_indelible', $indelible) ;
            }
            $this->dao->orderBy('i_order', 'ASC') ;
            if( !is_null($limit) ) {
                $this->dao->limit($limit, $start) ;
            }
            $result = $this->dao->get() ;
            if($result) {
                $aPages = $result->result() ;

                if( count($aPages) == 0 ) {
                    return array() ;
                }

                $resultPages = array() ;
                foreach($aPages as $aPage) {
                    $data = $this->extendDescription($aPage, $locale) ;
                    if(count($data) > 0) {
                        $resultPages[] = $data ;
                    }
                    unset($data) ;
                }

                return $resultPages ;
            } else {
                return array();
            }
        }

        /**
         * An array with data of some page, returns the title and description in every language available
         *
         * @access public
         * @since unknown
         * @param array $aPage
         * @return array Page information, title and description in every language available
         */
        public function extendDescription($aPage, $locale = null)
        {
            $this->dao->select() ;
            $this->dao->from($this->getDescriptionTableName()) ;
            $this->dao->where("fk_i_pages_id", $aPage['pk_i_id']) ;
            if( !is_null($locale) ) {
                $this->dao->where('fk_c_locale_code', $locale) ;
            }
            $results       = $this->dao->get() ;
            $aDescriptions = $results->result() ;

            if( count($aDescriptions) == 0 ) {
                return array() ;
            }

            $aPage['locale'] = array() ;
            foreach($aDescriptions as $description) {
                if( !empty($description['s_title']) || !empty($description['s_text']) ) {
                    $aPage['locale'][$description['fk_c_locale_code']] = $description ;
                }
            }

            return $aPage ;
        }

        /**
         * Delete a page by id number.
         *
         * @access public
         * @since unknown
         * @param int $id Page id which is going to be deleted
         * @return@return mixed It return the number of affected rows if the delete has been 
         * correct or false if nothing has been modified
         */
        public function deleteByPrimaryKey($id)
        {
            $row = $this->findByPrimaryKey($id);
            $order = $row['i_order'];
            
            $this->reOrderPages($order);

            $this->dao->delete($this->getDescriptionTableName(), array('fk_i_pages_id' => $id));
            return $this->dao->delete($this->tableName, array('pk_i_id' => $id));
        }

        /**
         * Delete a page by internal name.
         *
         * @access public
         * @since unknown
         * @param string $intName Page internal name which is going to be deleted
         * @return bool True on successful removal, false on failure
         */
        public function deleteByInternalName($intName)
        {
            $row = $this->findByInternalName($intName);
            return $this->deleteByPrimaryKey($row['pk_i_id']);
        }

        /**
         * Order pages from $order
         *
         * @access private
         * @since unknown
         * @param int $order
         */
        private function reOrderPages($order)
        {
            $aPages = $this->listAll(false);
            $arows = 0;
            foreach($aPages as $page){
                if($page['i_order'] > $order){
                    $new_order = $page['i_order']-1;
                    $arows += $this->dao->update($this->tableName, array('i_order' => $new_order), array('pk_i_id' => $page['pk_i_id']) );
                }
            }
            return $arows;
        }

        /**
         * Find previous page
         *
         * @access public
         * @since 2.4
         * @param int $order
         */
        public function findPrevPage($order)
        {
            $this->dao->select();
            $this->dao->from($this->tableName);
            $this->dao->where('b_indelible', 0);
            $this->dao->where('i_order < '.$order);
            $this->dao->orderBy('i_order', 'DESC');
            $this->dao->limit(1);
            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            if( $result->numRows() == 0 ) {
                return array() ;
            }
            return $result->row() ;
        }

        /**
         * Find next page
         *
         * @access public
         * @since 2.4
         * @param int $order
         */
        public function findNextPage($order)
        {
            $this->dao->select();
            $this->dao->from($this->tableName);
            $this->dao->where('b_indelible', 0);
            $this->dao->where('i_order > '.$order);
            $this->dao->orderBy('i_order', 'ASC');
            $this->dao->limit(1);
            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            if( $result->numRows() == 0 ) {
                return array() ;
            }
            return $result->row() ;
        }

        /**
         * Insert a new page. You have to pass all the parameters
         *
         * @access public
         * @since unknown
         * @param array $aFields Fields to be inserted in pages table
         * @param array $aFieldsDescription An array with the titles and descriptions in every language.
         * @return bool True if the insert has been done well and false if not.
         */
        public function insert($aFields, $aFieldsDescription = null)
        {
            $this->dao->select("MAX(i_order) as o");
            $this->dao->from($this->tableName);
            $results = $this->dao->get();
            $lastPage = $results->row();

            $order = $lastPage['o'];
            if( is_null($order) ){
                $order = -1;
            }
            
            $this->dao->insert($this->tableName, array(
                's_internal_name' => $aFields['s_internal_name']
                ,'b_indelible' => $aFields['b_indelible']
                ,'dt_pub_date' => date('Y-m-d H:i:s')
                ,'dt_mod_date' => date('Y-m-d H:i:s')
                ,'i_order' => ($order+1)
            ));


            $id = $this->dao->insertedId();

            if($this->dao->affectedRows() == 0) {
                return false;
            }

            foreach($aFieldsDescription as $k => $v) {
                $affected_rows = $this->insertDescription($id, $k, $v['s_title'], $v['s_text']);
                if(!$affected_rows) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Insert the content (title and description) of a page.
         *
         * @access private
         * @since unknown
         * @param int $id Id of the page, it would be the foreign key
         * @param string $locale Locale code of the language
         * @param string $title Text to be inserted in s_title
         * @param string $text Text to be inserted in s_text
         * @return bool True if the insert has been done well and false if not.
         */
        private function insertDescription($id, $locale, $title, $text)
        {

            $this->dao->insert($this->getDescriptionTableName() ,array(
                'fk_i_pages_id' => $id
                ,'fk_c_locale_code' => $locale
                ,'s_title' => $title
                ,'s_text' => $text
            ));

            if($this->dao->affectedRows() == 0) {
                return false;
            }

            return true;
        }

        /**
         * Update the content (title and description) of a page
         *
         * @access public
         * @since unknown
         * @param int $id Id of the page id is going to be modified
         * @param string $locale Locale code of the language
         * @param string $title Text to be updated in s_title
         * @param string $text Text to be updated in s_text
         * @return int Number of affected rows.
         */
        public function updateDescription($id, $locale, $title, $text)
        {
            $conditions = array('fk_c_locale_code' => $locale, 'fk_i_pages_id' => $id);
            $exist= $this->existDescription($conditions);

            if(!$exist) {
                $result = $this->insertDescription($id, $locale, $title, $text);
                return $result;
            }

            return $this->dao->update($this->getDescriptionTableName(),
                    array(
                        's_title' => $title
                        ,'s_text' => $text
                    ), array(
                        'fk_c_locale_code' => $locale
                        ,'fk_i_pages_id' => $id
                    ));
        }

        /**
         * Check if depending the conditions, the row exists in de DB.
         *
         * @access public
         * @since unknown
         * @param array $conditions
         * @return bool Return true if exists and false if not.
         */
        public function existDescription($conditions){
            $this->dao->select("COUNT(*) as total");
            $this->dao->from($this->getDescriptionTableName());
            foreach($conditions as $key => $value) {
                $this->dao->where($key, $value);
            }

            $result = $this->dao->get();
            $count = $result->row();
            
            return ($count['total']>0)?true:false;
        }

        /**
         * It change the internal name of a page. Here you don't check if in indelible or not the page.
         *
         * @access public
         * @since unknown
         * @param int $id The id of the page to be changed.
         * @param string $intName The new internal name.
         * @return int Number of affected rows.
         */
        public function updateInternalName($id, $intName)
        {
            $fields = array('s_internal_name' => $intName,
                             'dt_mod_date'    => date('Y-m-d H:i:s'));
            $where  = array('pk_i_id' => $id);

            return $this->dao->update($this->tableName, $fields, $where);
        }

        /**
         * Check if a page id is indelible
         *
         * @access public
         * @since unknown
         * @param int $id Page id
         * @return true if it's indelible, false in case not
         */
        function isIndelible($id)
        {
            $page = $this->findByPrimaryKey($id);
            if($page['b_indelible'] == 1) {
                return true;
            }
            return false;
        }

        /**
         * Check if Internal Name exists with another id
         *
         * @access public
         * @since unknown
         * @param int $id page id
         * @param string $internalName page internal name
         * @return true if internal name exists, false if not
         */
        function internalNameExists($id, $internalName)
        {
            $this->dao->select();
            $this->dao->from($this->tableName);
            $this->dao->where('s_internal_name', $internalName);
            $this->dao->where('pk_i_id <> '.$id);
            $result = $this->dao->get();
            
            if($result->numRows() > 0) {
                return true;
            }
            return false;
        }

        function getDescriptionTableName()
        {
            return $this->getTablePrefix() . 't_pages_description' ;
        }
    }

    /* file end: ./oc-includes/osclass/model/Page.php */
?>