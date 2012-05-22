<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */
     
     class MediaProcessingAjax
     {
        private $media ;
        private $result ;
        private $toJSON ;

        private $resourceID = null ;
        private $limit ;
        private $start ;
        private $total ;
        private $total_filtered ;
        private $order_by = array() ;

        private $column_names  = array(
            0 => 'r.pk_i_id',
            1 => 'r.pk_i_id',
            2 => 'r.pk_i_id',
            3 => 'r.fk_i_item_id',
            4 => 'c.dt_pub_date'
        ) ;

        /* For Datatables */
        private $sEcho = null;
        private $_get ;

        function __construct($params)
        {
            $this->_get = $params ;
            $this->getDBParams() ;
            
            $this->media          = ItemResource::newInstance()->getResources($this->resourceID, $this->start, $this->limit, ( $this->order_by['column_name'] ? $this->order_by['column_name'] : 'pk_i_id' ), ( $this->order_by['type'] ? $this->order_by['type'] : 'desc' ) ) ;
            $this->total          = ItemResource::newInstance()->countResources() ;
            if( $this->resourceID == null ) {
                $this->total_filtered = $this->total ;
            } else {
                $this->total_filtered = ItemResource::newInstance()->countResources( $this->resourceID ) ;
            }

            $this->toDatatablesFormat() ;
            $this->dumpToDatatables() ;
        }

        function __destruct()
        {
            unset($this->_get) ;
        }

        private function getDBParams()
        {
            foreach($this->_get as $k => $v) {
                if( ( $k == 'resourceId' ) && !empty($v) ) {
                    $this->resourceID = intval($v) ;
                }
                if( $k == 'iDisplayStart' ) {
                    $this->start = intval($v) ;
                }
                if( $k == 'iDisplayLength' ) {
                    $this->limit = intval($v) ;
                }
                if( $k == 'sEcho' ) {
                    $this->sEcho = intval($v) ;
                }

                /* for sorting */
                if( $k == 'iSortCol_0' ) {
                    $this->order_by['column_name'] = $this->column_names[$v] ;
                }
                if( $k == 'sSortDir_0' ) {
                    $this->order_by['type'] = $v ;
                }
            }
        }

        /* START - format functions */
        private function toDatatablesFormat()
        {
            $this->result['iTotalRecords']        = $this->total_filtered ;
            $this->result['iTotalDisplayRecords'] = $this->total ;
            $this->result['sEcho']                = $this->sEcho ;
            $this->result['aaData']               = array() ;

            if( count($this->media) == 0 ) {
                return ;
            }

            $count = 0 ;
            foreach($this->media as $aRow) {
                $row = array() ;

                $row[] = '<input type="checkbox" name="id[]" value="' . $aRow['pk_i_id'] . '" />' ;
                $row[] = '<div id="media_list_pic"><img src="' . osc_apply_filter('resource_path', osc_base_url() . $aRow['s_path']) . $aRow['pk_i_id'] . '_thumbnail.' . $aRow['s_extension'] . '" style="max-width: 60px; max-height: 60px;" /></div> <div id="media_list_filename">' . $aRow['s_content_type'] ;
                $row[] = '<a onclick="javascript:return confirm(\'' . osc_esc_js( __('This action can not be undone. Are you sure you want to continue?') ) . '\')" href="' . osc_admin_base_url(true) . '?page=media&amp;action=delete&amp;id[]=' . $aRow['pk_i_id'] . '" id="dt_link_delete">' . __('Delete') . '</a>' ;
                $row[] = '<a target="_blank" href="' . osc_item_url_ns($aRow['fk_i_item_id']) . '">item #' . $aRow['fk_i_item_id'] . '</a>' ;
                $row[] = $aRow['dt_pub_date'] ;

                $count++ ;
                $this->result['aaData'][] = $row ;
            }
        }

        /**
         * Set toJson variable with the JSON representation of $result
         * 
         * @access private
         * @since unknown
         * @param array $result
         */
        private function toJSON($result)
        {
            $this->toJSON = json_encode($result) ;
        }

        /**
         * Dump $result to JSON and echo the result
         * 
         * @access private
         * @since unknown 
         */
        private function dumpResult()
        {
            $this->toJSON($this->result) ;
            echo $this->toJSON ;
        }

        /**
         * Dump $result
         * 
         * @access private
         * @since unknown 
         */
        private function dumpToDatatables()
        {
            $this->dumpResult() ;
        }
     }

     /* file end: ./oc-admin/ajax/media_processing.php */
?>