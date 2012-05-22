<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

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

    class CAdminCategories extends AdminSecBaseModel
    {
        //specific for this class
        private $categoryManager ;

        function __construct()
        {
            parent::__construct() ;

            //specific things for this class
            $this->categoryManager = Category::newInstance() ;
        }

        //Business Layer...
        function doModel()
        {
            parent::doModel() ;

            //specific things for this class
            switch ($this->action)
            {
                case 'add_post_default': // add default category and reorder parent categories
                                        $fields['fk_i_parent_id'] = NULL;
                                        $fields['i_expiration_days'] = 0;
                                        $fields['i_position'] = 0;
                                        $fields['b_enabled'] = 1;

                                        $default_locale = osc_language();
                                        $aFieldsDescription[$default_locale]['s_name'] = "NEW CATEGORY, EDIT ME!";

                                        $categoryId = $this->categoryManager->insert($fields, $aFieldsDescription);

                                        // reorder parent categories. NEW category first
                                        $rootCategories = $this->categoryManager->findRootCategories();
                                        foreach($rootCategories as $cat){
                                            $order = $cat['i_position'];
                                            $order++;
                                            $this->categoryManager->updateOrder($cat['pk_i_id'],$order);
                                        }
                                        $this->categoryManager->updateOrder($categoryId,'0');

                                        $this->redirectTo(osc_admin_base_url(true).'?page=categories');
                break; 
                case('settings'):       // calling the categories settings view
                                        $this->doView('categories/settings.php') ;
                break;
                case('settings_post'):  // updating categories option
                                        $selectableParent = Params::getParam('selectable_parent_categories') ;

                                        $updated = Preference::newInstance()->update(
                                            array('s_value' => $selectableParent),
                                            array('s_name'  => 'selectable_parent_categories')
                                        ) ;

                                        if( $updated > 0 ) {
                                            osc_add_flash_ok_message( _m("Categories' settings have been updated"), 'admin') ;
                                        }
                                        $this->redirectTo( osc_admin_base_url(true) . '?page=categories&action=settings' ) ;
                break;
                default:                //
                                        $this->_exportVariableToView("categories", $this->categoryManager->toTreeAll() );
                                        $this->doView("categories/index.php") ;

            }
        }

        //hopefully generic...
        function doView($file)
        {
            osc_current_admin_theme_path($file) ;
            Session::newInstance()->_clearVariables();
        }
    }

    /* file end: ./oc-admin/categories.php */
?>
