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

    class CWebPage extends BaseModel
    {
        var $pageManager ;

        function __construct()
        {
            parent::__construct() ;

            $this->pageManager = Page::newInstance() ;
        }

        function doModel()
        {
            $id   = Params::getParam('id') ;
            $page = false ;

            if( is_numeric($id) ) {
                $page = $this->pageManager->findByPrimaryKey($id) ;
            } else {
                $page = $this->pageManager->findByInternalName(Params::getParam('slug')) ;
            }

            // page not found
            if( $page == false ) {
                $this->do404() ;
                return ;
            }

            // this page shouldn't be shown (i.e.: e-mail templates)
            if( $page['b_indelible'] == 1 ) {
                $this->do404() ;
                return ;
            }

            // export $page content to View
            $this->_exportVariableToView('page', $page) ;
            if( Params::getParam('lang') != '' ) {
                Session::newInstance()->_set('userLocale', Params::getParam('lang')) ;
            }

            // load the right template file
            if( file_exists(osc_themes_path() . osc_theme() . '/page-' . $page['s_internal_name'] . '.php') ) {
                $this->doView('page-' . $page['s_internal_name'] . '.php') ;
            } else {
                $this->doView('page.php') ;
            }
        }

        function doView($file)
        {
            osc_run_hook('before_html') ;
            osc_current_web_theme_path($file) ;
            Session::newInstance()->_clearVariables() ;
            osc_run_hook('after_html') ;
        }
    }

    /* file end: ./page.php */
?>