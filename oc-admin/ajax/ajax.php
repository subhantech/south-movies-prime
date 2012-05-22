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

    define('IS_AJAX', true) ;

    class CAdminAjax extends AdminSecBaseModel {

        function __construct()
        {
            parent::__construct();
            $this->ajax = true ;
        }

        //Business Layer...
        function doModel()
        {
            //specific things for this class
            switch ($this->action) {
                case 'bulk_actions':
                    break;
                case 'regions': //Return regions given a countryId
                    $regions = Region::newInstance()->findByCountry(Params::getParam("countryId"));
                    echo json_encode($regions);
                    break;
                case 'cities': //Returns cities given a regionId
                    $cities = City::newInstance()->findByRegion(Params::getParam("regionId"));
                    echo json_encode($cities);
                    break;
                case 'location': // This is the autocomplete AJAX
                    $cities = City::newInstance()->ajax(Params::getParam("term"));
                    echo json_encode($cities);
                    break;
                case 'userajax': // This is the autocomplete AJAX
                    $users = User::newInstance()->ajax(Params::getParam("term"));
                    if(count($users)==0) {
                        echo json_encode(array(0 => array('id'=> '', 'label' => __('No results'), 'value' => __('No results')) ));
                    } else {
                        echo json_encode($users);
                    }
                    break;
                case 'date_format':
                    echo json_encode(array('format' => Params::getParam('format'), 'str_formatted' => osc_format_date(date(Params::getParam('format')))));
                    break;
                case 'runhook': // run hooks
                    $hook = Params::getParam('hook');

                    if($hook == '') {
                        echo json_encode(array('error' => 'hook parameter not defined')) ;
                        break;
                    }

                    switch($hook) {
                        case 'item_form':
                            osc_run_hook('item_form', Params::getParam('catId'));
                        break;
                        case 'item_edit':
                            $catId  = Params::getParam("catId");
                            $itemId = Params::getParam("itemId");
                            osc_run_hook("item_edit", $catId, $itemId);
                        break;
                        default:
                            osc_run_hook('ajax_admin_' . $hook);
                        break;
                    }
                break;
                case 'items': // Return items (use external file oc-admin/ajax/item_processing.php)
                    require_once osc_admin_base_path() . 'ajax/items_processing.php';
                    $items_processing = new ItemsProcessingAjax(Params::getParamsAsArray("get"));
                    break;
                case 'users': // Return items (use external file oc-admin/ajax/item_processing.php)
                    require_once osc_admin_base_path() . 'ajax/users_processing.php';
                    $users_processing = new UsersProcessingAjax(Params::getParamsAsArray("get"));
                    break;
                case 'media': // Return items (use external file oc-admin/ajax/media_processing.php)
                    require_once osc_admin_base_path() . 'ajax/media_processing.php';
                    $media_processing = new MediaProcessingAjax(Params::getParamsAsArray("get"));
                    break;
                case 'categories_order': // Save the order of the categories
                    $aIds        = Params::getParam('list') ;
                    $orderParent = 0 ;
                    $orderSub    = 0 ;
                    $catParent   = 0 ;
                    $error       = 0 ;

                    $catManager = Category::newInstance() ;
                    $aRecountCat = array();
                    
                    foreach($aIds as $id => $parent) {
                        if( $parent == 'root' ) {
                            $res = $catManager->updateOrder($id, $orderParent) ;
                            if( is_bool($res) && !$res ) {
                                $error = 1 ;
                            }
                            
                            // find category
                            $auxCategory = Category::newInstance()->findByPrimaryKey($id);
                            
                            // set parent category 
                            $conditions = array('pk_i_id' => $id) ;
                            $array['fk_i_parent_id'] = NULL ;
                            $res = $catManager->update($array, $conditions) ;
                            if( is_bool($res) && !$res ) {
                                $error = 1 ;
                            } else if($res==1) { // updated ok
                                $parentId = $auxCategory['fk_i_parent_id'];
                                if($parentId) {
                                    // update parent category stats
                                    array_push($aRecountCat, $id);
                                    array_push($aRecountCat, $parentId);
                                }
                                
                            }
                            $orderParent++ ;
                        } else {
                            if( $parent != $catParent ) {
                                $catParent = $parent ;
                                $orderSub  = 0 ;
                            }

                            $res = $catManager->updateOrder($id, $orderSub) ;
                            if( is_bool($res) && !$res ) {
                                $error = 1 ;
                            }

                            // set parent category 
                            $auxCategory = Category::newInstance()->findByPrimaryKey($id);
                            $auxCategoryP = Category::newInstance()->findByPrimaryKey($catParent);
                            
                            $conditions = array('pk_i_id' => $id) ;
                            $array['fk_i_parent_id'] = $catParent ;

                            $res = $catManager->update($array, $conditions) ;
                            if( is_bool($res) && !$res ) {
                                $error = 1 ;
                            } else if($res==1) { // updated ok
                                // update category parent
                                $prevParentId = $auxCategory['fk_i_parent_id'];
                                $parentId = $auxCategoryP['pk_i_id'];
                                array_push($aRecountCat, $prevParentId);
                                array_push($aRecountCat, $parentId);
                            }
                            $orderSub++ ;
                        }
                    }

                    // update category stats
                    foreach($aRecountCat as $rId) {
                        osc_update_cat_stats_id($rId);
                    }
                    
                    if( $error ) {
                        $result = array( 'error' => __("Some error ocurred") ) ;
                    } else {
                        $result = array( 'ok' => __("Order saved")) ;
                    }

                    echo json_encode($result) ;
                break ;
                case 'category_edit_iframe':
                    $this->_exportVariableToView( 'category', Category::newInstance()->findByPrimaryKey( Params::getParam("id") ) ) ;
                    $this->_exportVariableToView( 'languages', OSCLocale::newInstance()->listAllEnabled() ) ;
                    $this->doView("categories/iframe.php") ;
                    break;
                case 'field_categories_iframe':
                    $selected = Field::newInstance()->categories(Params::getParam("id"));
                    if ($selected == null) {
                        $selected = array();
                    };
                    $this->_exportVariableToView("selected", $selected);
                    $this->_exportVariableToView("field", Field::newInstance()->findByPrimaryKey(Params::getParam("id")));
                    $this->_exportVariableToView("categories", Category::newInstance()->toTreeAll());
                    $this->doView("fields/iframe.php");
                    break;
                case 'field_categories_post':
                    $error = 0;
                    $field = Field::newInstance()->findByName(Params::getParam("s_name"));
                    
                    if (!isset($field['pk_i_id']) || (isset($field['pk_i_id']) && $field['pk_i_id'] == Params::getParam("id"))) {
                        // remove categories from a field
                        Field::newInstance()->cleanCategoriesFromField(Params::getParam("id"));
                        // no error... continue updating fields
                        if($error == 0) {
                            $slug = Params::getParam("field_slug") != '' ? Params::getParam("field_slug") : Params::getParam("s_name");
                            $slug_tmp = $slug = preg_replace('|([-]+)|', '-', preg_replace('|[^a-z0-9_-]|', '-', strtolower($slug)));
                            $slug_k = 0;
                            while(true) {
                                $field = Field::newInstance()->findBySlug($slug);
                                if(!$field || $field['pk_i_id']==Params::getParam("id")) {
                                    break;
                                } else {
                                    $slug_k++;
                                    $slug = $slug_tmp."_".$slug_k;
                                }
                            }
                            $res = Field::newInstance()->update(array('s_name' => Params::getParam("s_name"), 'e_type' => Params::getParam("field_type"), 's_slug' => $slug, 'b_required' => Params::getParam("field_required") == "1" ? 1 : 0, 's_options' => Params::getParam('s_options')), array('pk_i_id' => Params::getParam("id")));
                            if(is_bool($res) && !$res) {
                                $error = 1;
                            }
                        }
                        // no error... continue inserting categories-field
                        if($error == 0) {
                            $aCategories = Params::getParam("categories");
                            if( is_array($aCategories) && count($aCategories) > 0) {
                                $res = Field::newInstance()->insertCategories(Params::getParam("id"), $aCategories);
                                if(!$res) {
                                    $error = 1;
                                }
                            }
                        }
                        // error while updating?
                        if($error == 1) {
                            $message = __("Error while updating.");
                        }
                    } else {
                        $error = 1;
                        $message = __("Sorry, you already have one field with that name");
                    }

                    if($error) {
                        $result = array( 'error' => $message) ;
                    } else {
                        $result = array( 'ok' => __("Saved") , 'text' => Params::getParam("s_name"), 'field_id' => $field['pk_i_id']) ;
                    }
                    
                    echo json_encode($result) ;
                    
                    break;
                case 'delete_field':
                    $id = Params::getParam("id");
                    $error = 0;

                    $fieldManager = Field::newInstance();
                    $res = $fieldManager->deleteByPrimaryKey($id);
                    
                    if($res > 0) {
                        $message = __('The custom field have been deleted');
                    } else {
                        $error = 1;
                        $message = __('Error while deleting');
                    }

                    if($error) {
                        $result = array( 'error' => $message) ;
                    } else {
                        $result = array( 'ok' => __("Saved") ) ;
                    }
                    echo json_encode($result) ;

                    break;
                case 'add_field':
                    $s_name = __('NEW custom field');
                    $slug_tmp = $slug = preg_replace('|([-]+)|', '-', preg_replace('|[^a-z0-9_-]|', '-', strtolower($s_name)));
                    $slug_k = 0;
                    while(true) {
                        $field = Field::newInstance()->findBySlug($slug);
                        if(!$field || $field['pk_i_id']==Params::getParam("id")) {
                            break;
                        } else {
                            $slug_k++;
                            $slug = $slug_tmp."_".$slug_k;
                        }
                    }
                    $fieldManager = Field::newInstance();
                    $result = $fieldManager->insertField($s_name, 'TEXT', $slug, 0, '', array()) ;
                    if($result) {
                        echo json_encode(array('error' => 0, 'field_id' => $fieldManager->dao->insertedId(), 'field_name' => $s_name));
                    } else {
                        echo json_encode(array('error' => 1));
                    }
                    break;
                case 'enable_category':
                    $id       = strip_tags( Params::getParam('id') ) ;
                    $enabled  = (Params::getParam('enabled') != '') ? Params::getParam('enabled') : 0 ;
                    $error    = 0 ;
                    $result   = array() ;
                    $aUpdated = array() ;

                    $mCategory = Category::newInstance() ;
                    $aCategory = $mCategory->findByPrimaryKey( $id ) ;

                    if( $aCategory == false ) {
                        $result = array( 'error' => sprintf( __("It doesn't exist a category with this id: %d"), $id) ) ;
                        echo json_encode($result) ;
                        break ;
                    }

                    // root category
                    if( $aCategory['fk_i_parent_id'] == '' ) {
                        $mCategory->update( array('b_enabled' => $enabled), array('pk_i_id'        => $id) ) ;
                        $mCategory->update( array('b_enabled' => $enabled), array('fk_i_parent_id' => $id) ) ;

                        $subCategories = $mCategory->findSubcategories( $id ) ;

                        $aIds = array($id);
                        $aUpdated[] = array('id' => $id) ;
                        foreach( $subCategories as $subcategory ) {
                            $aIds[]     = $subcategory['pk_i_id'];
                            $aUpdated[] = array( 'id' => $subcategory['pk_i_id'] ) ;
                        }
                        
                        Item::newInstance()->enableByCategory($enabled, $aIds);

                        if( $enabled ) {
                            $result = array(
                                'ok' => __('The category and its subcategories have been enabled')
                            ) ;
                        } else {
                            $result = array(
                                'ok' => __('The category and its subcategories have been disabled')
                            ) ;
                        }
                        $result['affectedIds'] = $aUpdated ;
                        echo json_encode($result) ;
                        break ;
                    }

                    // subcategory
                    $parentCategory = $mCategory->findRootCategory( $id ) ;
                    if( !$parentCategory['b_enabled'] ) {
                        $result = array( 'error' => __('Parent category is disabled, you can not enable that category') ) ;
                        echo json_encode( $result ) ;
                        break ;
                    }

                    $mCategory->update( array('b_enabled' => $enabled), array('pk_i_id' => $id) ) ;
                    if( $enabled ) {
                        $result = array(
                            'ok' => __('The subcategory has been enabled')
                        ) ;
                    } else {
                        $result = array(
                            'ok' => __('The subcategory has been disabled')
                        ) ;
                    }
                    $result['affectedIds'] = array( array('id' => $id) ) ;
                    echo json_encode($result) ;
                    
                    break ;
                case 'delete_category':
                    $id = Params::getParam("id");
                    $error = 0;
                    
                    $categoryManager = Category::newInstance();
                    $res = $categoryManager->deleteByPrimaryKey($id);
                    
                    if($res > 0) {
                        $message = __('The categories have been deleted');
                    } else {
                        $error = 1;
                        $message = __('Error while deleting');
                    }

                    if($error) {
                        $result = array( 'error' => $message) ;
                    } else {
                        $result = array( 'ok' => __("Saved") ) ;
                    }
                    echo json_encode($result) ;
                    
                    break;
                case 'edit_category_post':
                    $id = Params::getParam("id");
                    $fields['i_expiration_days'] = (Params::getParam("i_expiration_days") != '') ? Params::getParam("i_expiration_days") : 0;

                    $error = 0;
                    $has_one_title = 0;
                    $postParams = Params::getParamsAsArray();
                    foreach ($postParams as $k => $v) {
                        if (preg_match('|(.+?)#(.+)|', $k, $m)) {
                            if ($m[2] == 's_name') {
                                if ($v != "") {
                                    $has_one_title = 1;
                                    $aFieldsDescription[$m[1]][$m[2]] = $v;
                                    $s_text = $v;
                                } else {
                                    $aFieldsDescription[$m[1]][$m[2]] = ' ';
                                    $error = 1;
                                }
                            } else {
                                $aFieldsDescription[$m[1]][$m[2]] = $v;
                            }
                        }
                    }

                    $l = osc_language();
                    if ($error==0 || ($error==1 && $has_one_title==1)) {
                        $categoryManager = Category::newInstance();
                        $res = $categoryManager->updateByPrimaryKey(array('fields' => $fields, 'aFieldsDescription' => $aFieldsDescription), $id);
                        
                        if( is_bool($res) ) {
                            $error = 2;
                        }
                    }
                    
                    if($error==0) {
                        $msg = __("Category updated correctly");
                    } else if($error==1) {
                        if($has_one_title==1) {
                            $error = 4;
                            $msg = __('Category updated correctly, but some titles were empty');
                        } else {
                            $msg = __('Sorry, at least a title is needed');
                        }
                    } else if($error==2) {
                        $msg = __('Error while updating');
                    }
                    echo json_encode(array('error' => $error, 'msg' => $msg, 'text' => $aFieldsDescription[$l]['s_name']));
                    
                    break;
                case 'custom': // Execute via AJAX custom file
                    $ajaxFile = Params::getParam("ajaxfile");

                    if($ajaxFile == '') {
                        echo json_encode(array('error' => 'no action defined'));
                        break ;
                    }

                    // valid file?
                    if( stripos($ajaxFile, '../') !== false ) {
                        echo json_encode(array('error' => 'no valid ajaxFile'));
                        break ;
                    }

                    if( !file_exists(osc_plugins_path() . $ajaxFile) ) {
                        echo json_encode(array('error' => "ajaxFile doesn't exist"));
                        break;
                    }

                    require_once osc_plugins_path() . $ajaxFile ;
                break;
                case 'test_mail':
                    $title = sprintf( __('Test email, %s'), osc_page_title() ) ;
                    $body  = __("Test email") . "<br><br>" . osc_page_title() ;

                    $emailParams = array(
                        'subject'  => $title,
                        'to'       => osc_contact_email(),
                        'to_name'  => 'admin',
                        'body'     => $body,
                        'alt_body' => $body
                    ) ;

                    $array = array() ;
                    if( osc_sendMail($emailParams) ) {
                        $array = array('status' => '1', 'html' => __('Email sent successfully') ) ;
                    } else {
                        $array = array('status' => '0', 'html' => __('An error has occurred while sending email') ) ;
                    }
                    echo json_encode($array) ;
                    break;
                case 'order_pages':
                    $order = Params::getParam("order");
                    $id    = Params::getParam("id");
                    if($order != '' && $id != '') {
                        $mPages = Page::newInstance();
                        $actual_page  = $mPages->findByPrimaryKey($id);
                        $actual_order = $actual_page['i_order'];

                        $array     = array();
                        $condition = array();
                        $new_order = $actual_order;

                        if($order == 'up') {
                            $page = $mPages->findPrevPage($actual_order);
                        } else if($order == 'down') {
                            $page = $mPages->findNextPage($actual_order);
                        }
                        if(isset($page['i_order'])) {
                            $mPages->update(array('i_order' => $page['i_order']), array('pk_i_id' => $id));
                            $mPages->update(array('i_order' => $actual_order), array('pk_i_id' => $page['pk_i_id']));
                        }
                        
                        // TO BE IMPROVED
                        // json for datatables
                        $prefLocale = osc_current_user_locale() ;
                        $this->_exportVariableToView( 'pages', $mPages->listAll(0) ) ;
                        $o_json = array() ;
                        while( osc_has_static_pages() ) {
                            $row  = array() ;
                            $page = osc_static_page();

                            $content = array() ;
                            if( isset($page['locale'][$prefLocale]) && !empty($page['locale'][$prefLocale]['s_title']) ) {
                                $content = $page['locale'][$prefLocale] ;
                            } else {
                                $content = current($page['locale']) ;
                            }

                            $options   = array() ;
                            $options[] = '<a href="' . osc_static_page_url() . '">' . __('View page') . '</a>' ;
                            $options[] = '<a href="' . osc_admin_base_url(true) . '?page=pages&amp;action=edit&amp;id=' . osc_static_page_id() . '">' . __('Edit') . '</a>' ;
                            if( !$page['b_indelible'] ) {
                                $options[] = '<a onclick="javascript:return confirm(\'' . osc_esc_js("This action can't be undone. Are you sure you want to continue?") . '\')" href="' . osc_admin_base_url(true) . '?page=pages&amp;action=delete&amp;id=' . osc_static_page_id() . '">' . __('Delete') . '</a>' ;
                            }

                            $row[] = '<input type="checkbox" name="id[]"" value="' . osc_static_page_id() . '"" />' ;
                            $row[] = $page['s_internal_name'] . '<div id="datatables_quick_edit" style="display: none;">' . implode(' &middot; ', $options) . '</div>' ;
                            $row[] = $content['s_title'] ;
                            $row[] = osc_static_page_order() . ' <img id="up" onclick="order_up(' . osc_static_page_id() . ');" style="cursor:pointer; width:15px; height:15px;" src="' . osc_current_admin_theme_url('images/arrow_up.png') . '"/> <br/><img id="down" onclick="order_down(' . osc_static_page_id() . ');" style="cursor:pointer; width:15px; height:15px; margin-left: 10px;" src="' . osc_current_admin_theme_url('images/arrow_down.png') .'"/>' ;

                            $o_json[] = $row ;
                        }

                        echo json_encode($o_json) ;
                    }

                    break;

                /******************************
                 ** COMPLETE UPGRADE PROCESS **
                 ******************************/
                case 'upgrade': // AT THIS POINT WE KNOW IF THERE'S AN UPDATE OR NOT
                    $message = "";
                    $error = 0;
                    $sql_error_msg = "";
                    $rm_errors = 0;
                    $perms = osc_save_permissions();
                    osc_change_permissions();

                    $maintenance_file = ABS_PATH . '.maintenance';
                    $fileHandler = @fopen($maintenance_file, 'w');
                    fclose($fileHandler);

                    /***********************
                     **** DOWNLOAD FILE ****
                     ***********************/
                    $data = osc_file_get_contents("http://osclass.org/latest_version.php");
                    $data = json_decode(substr($data, 1, strlen($data)-3), true);
                    $source_file = $data['url'];
                    if ($source_file != '') {

                        $tmp = explode("/", $source_file);
                        $filename = end($tmp);
                        $result = osc_downloadFile($source_file, $filename);

                        if ($result) { // Everything is OK, continue
                            /**********************
                             ***** UNZIP FILE *****
                             **********************/
                            @mkdir(ABS_PATH . 'oc-temp', 0777);
                            $res = osc_unzip_file(osc_content_path() . 'downloads/' . $filename, ABS_PATH . 'oc-temp/');
                            if ($res == 1) { // Everything is OK, continue
                                /**********************
                                 ***** COPY FILES *****
                                 **********************/
                                $fail = -1;
                                if ($handle = opendir(ABS_PATH . 'oc-temp')) {
                                    $fail = 0;
                                    while (false !== ($_file = readdir($handle))) {
                                        if ($_file != '.' && $_file != '..' && $_file != 'remove.list' && $_file != 'upgrade.sql' && $_file != 'customs.actions') {
                                            $data = osc_copy(ABS_PATH . "oc-temp/" . $_file, ABS_PATH . $_file);
                                            if ($data == false) {
                                                $fail = 1;
                                            };
                                        }
                                    }
                                    closedir($handle);

                                    if ($fail == 0) { // Everything is OK, continue
                                        /************************
                                         *** UPGRADE DATABASE ***
                                         ************************/
                                        $error_queries = array();
                                        if (file_exists(osc_lib_path() . 'osclass/installer/struct.sql')) {
                                            $sql = file_get_contents(osc_lib_path() . 'osclass/installer/struct.sql');
                                            
                                            $conn = DBConnectionClass::newInstance();
                                            $c_db = $conn->getOsclassDb() ;
                                            $comm = new DBCommandClass( $c_db ) ;
                                            $error_queries = $comm->updateDB( str_replace('/*TABLE_PREFIX*/', DB_TABLE_PREFIX, $sql) ) ;
                                            
                                        }
                                        if ($error_queries[0]) { // Everything is OK, continue
                                            /**********************************
                                             ** EXECUTING ADDITIONAL ACTIONS **
                                             **********************************/
                                            if (file_exists(osc_lib_path() . 'osclass/upgrade-funcs.php')) {
                                                // There should be no errors here
                                                define('AUTO_UPGRADE', true);
                                                require_once osc_lib_path() . 'osclass/upgrade-funcs.php';
                                            }
                                            // Additional actions is not important for the rest of the proccess
                                            // We will inform the user of the problems but the upgrade could continue
                                            /****************************
                                             ** REMOVE TEMPORARY FILES **
                                             ****************************/
                                            $path = ABS_PATH . 'oc-temp';
                                            $rm_errors = 0;
                                            $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
                                            for ($dir->rewind(); $dir->valid(); $dir->next()) {
                                                if ($dir->isDir()) {
                                                    if ($dir->getFilename() != '.' && $dir->getFilename() != '..') {
                                                        if (!rmdir($dir->getPathname())) {
                                                            $rm_errors++;
                                                        }
                                                    }
                                                } else {
                                                    if (!unlink($dir->getPathname())) {
                                                        $rm_errors++;
                                                    }
                                                }
                                            }
                                            if (!rmdir($path)) {
                                                $rm_errors++;
                                            }
                                            $deleted = @unlink(ABS_PATH . '.maintenance');
                                            if ($rm_errors == 0) {
                                                $message = __('Everything was OK! Your OSClass installation is updated');
                                            } else {
                                                $message = __('Almost everything was OK! Your OSClass installation is updated, but there were some errors removing temporary files. Please, remove manually the "oc-temp" folder');
                                                $error = 6; // Some errors removing files
                                            }
                                        } else {
                                            $sql_error_msg = $error_queries[2];
                                            $message = __('Problems upgrading the database');
                                            $error = 5; // Problems upgrading the database		                
                                        }
                                    } else {
                                        $message = __('Problems copying files. Maybe permissions are not correct');
                                        $error = 4; // Problems copying files. Maybe permissions are not correct
                                    }
                                } else {
                                    $message = __('Nothing to copy');
                                    $error = 99; // Nothing to copy. THIS SHOULD NEVER HAPPENS, means we dont update any file!
                                }
                            } else {
                                $message = __('Unzip failed');
                                $error = 3; // Unzip failed
                            }
                        } else {
                            $message = __('Download failed');
                            $error = 2; // Download failed
                        }
                    } else {
                        $message = __('Missing download URL');
                        $error = 1; // Missing download URL
                    }

                    if ($error == 5) {
                        $message .= "<br /><br />" . __('We had some errors upgrading your database. The follwing queries failed') . implode("<br />", $sql_error_msg);
                    }
                    echo $message;

                    foreach ($perms as $k => $v) {
                        @chmod($k, $v);
                    }
                    break;
                    
                case 'location_stats':
                    $workToDo = LocationsTmp::newInstance()->count() ;
                    if( $workToDo > 0 ) {
                        // there are wotk to do
                        $aLocations = LocationsTmp::newInstance()->getLocations(1000) ;
                        foreach($aLocations as $location) {
                            $id     = $location['id_location'];
                            $type   = $location['e_type'];
                            $data   = 0; 
                            // update locations stats
                            switch ( $type ) {
                                case 'COUNTRY':
                                    $numItems = CountryStats::newInstance()->calculateNumItems( $id ) ;
                                    $data = CountryStats::newInstance()->setNumItems($id, $numItems) ;
                                    unset($numItems) ;
                                break;
                                case 'REGION' :
                                    $numItems = RegionStats::newInstance()->calculateNumItems( $id ) ;
                                    $data = RegionStats::newInstance()->setNumItems($id, $numItems) ;
                                    unset($numItems) ;
                                break;
                                case 'CITY' :
                                    $numItems = CityStats::newInstance()->calculateNumItems( $id ) ;
                                    $data = CityStats::newInstance()->setNumItems($id, $numItems) ;
                                    unset($numItems) ;
                                break;
                                default:
                                break;
                            }
                            if($data >= 0) {
                                LocationsTmp::newInstance()->delete(array('e_type' => $location['e_type'], 'id_location' => $location['id_location']) ) ;
                            }
                        }
                        $array['status']  = 'more';
                        $array['pending'] = $workToDo = LocationsTmp::newInstance()->count() ;
                        echo json_encode($array);
                    } else {
                        $array['status']  = 'done';
                        echo json_encode($array);
                    }
                    break;
                default:
                    echo json_encode(array('error' => __('no action defined')));
                    break;
            }
            // clear all keep variables into session
            Session::newInstance()->_dropKeepForm();
            Session::newInstance()->_clearVariables();
        }

        //hopefully generic...
        function doView($file) {
            osc_current_admin_theme_path($file);
        }
    }

?>