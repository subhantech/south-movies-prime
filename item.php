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

    class CWebItem extends BaseModel
    {
        private $itemManager;
        private $user;
        private $userId;

        function __construct()
        {
            parent::__construct() ;
            $this->itemManager = Item::newInstance();

            // here allways userId == ''
            if( osc_is_web_user_logged_in() ) {
                $this->userId = osc_logged_user_id();
                $this->user = User::newInstance()->findByPrimaryKey($this->userId);
            } else {
                $this->userId = null;
                $this->user = null;
            }
        }

        //Business Layer...
        function doModel()
        {
            //calling the view...

            $locales = OSCLocale::newInstance()->listAllEnabled() ;
            $this->_exportVariableToView('locales', $locales) ;

            switch( $this->action ) {
                case 'item_add': // post
                    if( osc_reg_user_post() && $this->user == null ) {
                        osc_add_flash_warning_message( _m('Only registered users are allowed to post items') ) ;
                        $this->redirectTo(osc_user_login_url()) ;
                    }

                    $countries = Country::newInstance()->listAll();
                    $regions = array();
                    if( isset($this->user['fk_c_country_code']) && $this->user['fk_c_country_code']!='' ) {
                        $regions = Region::newInstance()->findByCountry($this->user['fk_c_country_code']);
                    } else if( count($countries) > 0 ) {
                        $regions = Region::newInstance()->findByCountry($countries[0]['pk_c_code']);
                    }
                    $cities = array();
                    if( isset($this->user['fk_i_region_id']) && $this->user['fk_i_region_id']!='' ) {
                        $cities = City::newInstance()->findByRegion($this->user['fk_i_region_id']) ;
                    } else if( count($regions) > 0 ) {
                        $cities = City::newInstance()->findByRegion($regions[0]['pk_i_id']) ;
                    }

                    $this->_exportVariableToView('countries',$countries ) ;
                    $this->_exportVariableToView('regions', $regions) ;
                    $this->_exportVariableToView('cities', $cities) ;

                    $form = count(Session::newInstance()->_getForm());
                    $keepForm = count(Session::newInstance()->_getKeepForm());
                    if($form==0 || $form==$keepForm) {
                        Session::newInstance()->_dropKeepForm();
                    }

                    if( Session::newInstance()->_getForm('countryId') != "" ) {
                        $countryId  = Session::newInstance()->_getForm('countryId') ;
                        $regions    = Region::newInstance()->findByCountry($countryId) ; 
                        $this->_exportVariableToView('regions', $regions) ;
                        if(Session::newInstance()->_getForm('regionId') != "" ) {
                            $regionId  = Session::newInstance()->_getForm('regionId') ;
                            $cities = City::newInstance()->findByRegion($regionId ) ;
                            $this->_exportVariableToView('cities', $cities ) ;
                        }
                    }

                    $this->_exportVariableToView('user', $this->user) ;

                    osc_run_hook('post_item');

                    $this->doView('item-post.php');
                break;
                case 'item_add_post': //post_item
                    if( osc_reg_user_post() && $this->user == null ) {
                        osc_add_flash_warning_message( _m('Only registered users are allowed to post items') ) ;
                        $this->redirectTo( osc_base_url(true) ) ;
                    }

                    $mItems = new ItemActions(false);
                    // prepare data for ADD ITEM
                    $mItems->prepareData(true);
                    // set all parameters into session
                    foreach( $mItems->data as $key => $value ) {
                        Session::newInstance()->_setForm($key,$value);
                    }

                    $meta = Params::getParam('meta');
                    if(is_array($meta)) {
                        foreach( $meta as $key => $value ) {
                            Session::newInstance()->_setForm('meta_'.$key, $value);
                            Session::newInstance()->_keepForm('meta_'.$key);
                        }
                    }

                    if ((osc_recaptcha_private_key() != '') && Params::existParam("recaptcha_challenge_field")) {
                        if(!osc_check_recaptcha()) {
                            osc_add_flash_error_message( _m('The Recaptcha code is wrong') ) ;
                            $this->redirectTo( osc_item_post_url() );
                            return false; // BREAK THE PROCESS, THE RECAPTCHA IS WRONG
                        }
                    }
                    // POST ITEM ( ADD ITEM )
                    $success = $mItems->add();

                    if($success!=1 && $success!=2) {
                        osc_add_flash_error_message( $success) ;
                        $this->redirectTo( osc_item_post_url() );
                    } else {
                        Session::newInstance()->_dropkeepForm('meta_'.$key);

                        if($success==1) {
                            osc_add_flash_ok_message( _m('Check your inbox to verify your email address') ) ;
                        } else {
                            osc_add_flash_ok_message( _m('Your item has been published') ) ;
                        }

                        $itemId         = Params::getParam('itemId');
                        $item           = $this->itemManager->findByPrimaryKey($itemId);

                        osc_run_hook('posted_item', $item);
                        $category = Category::newInstance()->findByPrimaryKey(Params::getParam('catId'));
                        View::newInstance()->_exportVariableToView('category', $category);
                        $this->redirectTo(osc_search_category_url());
                    }
                break;
                case 'item_edit':   // edit item
                                    $secret = Params::getParam('secret');
                                    $id     = Params::getParam('id');
                                    $item   = $this->itemManager->listWhere("i.pk_i_id = '%s' AND ((i.s_secret = '%s' AND i.fk_i_user_id IS NULL) OR (i.fk_i_user_id = '%d'))", addslashes($id), addslashes($secret), addslashes($this->userId));
                                    if (count($item) == 1) {
                                        $item     = Item::newInstance()->findByPrimaryKey($id);

                                        $form     = count(Session::newInstance()->_getForm());
                                        $keepForm = count(Session::newInstance()->_getKeepForm());
                                        if($form == 0 || $form == $keepForm) {
                                            Session::newInstance()->_dropKeepForm();
                                        }

                                        $this->_exportVariableToView('item', $item);

                                        osc_run_hook("before_item_edit", $item);
                                        $this->doView('item-edit.php');
                                    } else {
                                        // add a flash message [ITEM NO EXISTE]
                                        osc_add_flash_error_message( _m("Sorry, we don't have any items with that ID") ) ;
                                        if($this->user != null) {
                                            $this->redirectTo( osc_user_list_items_url() );
                                        } else {
                                            $this->redirectTo( osc_base_url() ) ;
                                        }
                                    }
                break;
                case 'item_edit_post':
                    // recoger el secret y el
                    $secret = Params::getParam('secret');
                    $id     = Params::getParam('id');
                    $item   = $this->itemManager->listWhere("i.pk_i_id = '%s' AND ((i.s_secret = '%s' AND i.fk_i_user_id IS NULL) OR (i.fk_i_user_id = '%d'))", addslashes($id), addslashes($secret), addslashes($this->userId)) ;

                    if (count($item) == 1) {
                        $this->_exportVariableToView('item', $item[0]) ;

                        $mItems = new ItemActions(false);
                        // prepare data for ADD ITEM
                        $mItems->prepareData(false);
                        // set all parameters into session
                        foreach( $mItems->data as $key => $value ) {
                            Session::newInstance()->_setForm($key,$value);
                        }

                        $meta = Params::getParam('meta');
                        if(is_array($meta)) {
                            foreach( $meta as $key => $value ) {
                                Session::newInstance()->_setForm('meta_'.$key, $value);
                                Session::newInstance()->_keepForm('meta_'.$key);
                            }
                        }

                        if( (osc_recaptcha_private_key() != '') && Params::existParam("recaptcha_challenge_field") ) {
                            if( !osc_check_recaptcha() ) {
                                osc_add_flash_error_message( _m('The Recaptcha code is wrong') ) ;
                                $this->redirectTo( osc_item_edit_url() ) ;
                                return false ; // BREAK THE PROCESS, THE RECAPTCHA IS WRONG
                            }
                        }

                        $success = $mItems->edit();

                        osc_run_hook('edited_item', Item::newInstance()->findByPrimaryKey($id));
                        
                        if($success==1){
                            osc_add_flash_ok_message( _m("Great! We've just updated your item") ) ;
                            $this->redirectTo( osc_base_url(true) . "?page=item&id=$id" ) ;
                        } else {
                            osc_add_flash_error_message( $success) ;
                            $this->redirectTo( osc_item_edit_url($secret) ) ;
                        }
                    }
                break;
                case 'activate':
                    $secret = Params::getParam('secret');
                    $id     = Params::getParam('id');
                    $item   = $this->itemManager->listWhere("i.pk_i_id = '%s' AND ((i.s_secret = '%s') OR (i.fk_i_user_id = '%d'))", addslashes($id), addslashes($secret), addslashes($this->userId)) ;

                    // item doesn't exist
                    if( count($item) == 0 ) {
                        $this->do404() ;
                        return ;
                    }

                    View::newInstance()->_exportVariableToView('item', $item[0]);
                    if( $item[0]['b_active'] == 0 ) {
                        // ACTIVETE ITEM
                        $mItems = new ItemActions(false) ;
                        $success = $mItems->activate( $item[0]['pk_i_id'], $item[0]['s_secret'] );

                        if( $success ) {
                            osc_add_flash_ok_message( _m('The item has been validated') ) ;
                        }else{
                            osc_add_flash_error_message( _m("The item can't be validated") ) ;
                        }
                    } else {
                        osc_add_flash_warning_message( _m('The item has already been validated') ) ;
                    }

                    $this->redirectTo( osc_item_url( ) );
                break;
                case 'item_delete':
                    $secret = Params::getParam('secret');
                    $id     = Params::getParam('id');
                    $item   = $this->itemManager->listWhere("i.pk_i_id = '%s' AND ((i.s_secret = '%s') OR (i.fk_i_user_id = '%d'))", addslashes($id), addslashes($secret), addslashes($this->userId)) ;
                    if (count($item) == 1) {
                        $mItems = new ItemActions(false);
                        $success = $mItems->delete($item[0]['s_secret'], $item[0]['pk_i_id']);
                        if($success) {
                            osc_add_flash_ok_message( _m('Your item has been deleted') ) ;
                        } else {
                            osc_add_flash_error_message( _m("The item you are trying to delete couldn't be deleted") ) ;
                        }
                        if($this->user!=null) {
                            $this->redirectTo(osc_user_list_items_url());
                        } else {
                            $this->redirectTo( osc_base_url() ) ;
                        }
                    }else{
                        osc_add_flash_error_message( _m("The item you are trying to delete couldn't be deleted") ) ;
                        $this->redirectTo( osc_base_url() ) ;
                    }
                break;
                case 'mark':
                    $mItem = new ItemActions(false) ;

                    $id = Params::getParam('id') ;
                    $as = Params::getParam('as') ;

                    $item = Item::newInstance()->findByPrimaryKey($id);
                    View::newInstance()->_exportVariableToView('item', $item);
                    $mItem->mark($id, $as) ;

                    osc_add_flash_ok_message( _m("Thanks! That's very helpful") ) ;
                    $this->redirectTo( osc_item_url( ) );
                break;
                case 'send_friend':
                    $item = $this->itemManager->findByPrimaryKey( Params::getParam('id') );

                    $this->_exportVariableToView('item', $item) ;

                    $this->doView('item-send-friend.php');
                break;
                case 'send_friend_post':
                    $item = $this->itemManager->findByPrimaryKey( Params::getParam('id') );
                    $this->_exportVariableToView('item', $item) ;

                    Session::newInstance()->_setForm("yourEmail",   Params::getParam('yourEmail'));
                    Session::newInstance()->_setForm("yourName",    Params::getParam('yourName'));
                    Session::newInstance()->_setForm("friendName", Params::getParam('friendName'));
                    Session::newInstance()->_setForm("friendEmail", Params::getParam('friendEmail'));
                    Session::newInstance()->_setForm("message_body",Params::getParam('message'));

                    if ((osc_recaptcha_private_key() != '') && Params::existParam("recaptcha_challenge_field")) {
                        if(!osc_check_recaptcha()) {
                            osc_add_flash_error_message( _m('The Recaptcha code is wrong') ) ;
                            $this->redirectTo(osc_item_send_friend_url() );
                            return false; // BREAK THE PROCESS, THE RECAPTCHA IS WRONG
                        }
                    }

                    $mItem = new ItemActions(false);
                    $success = $mItem->send_friend();

                    if($success) {
                        Session::newInstance()->_clearVariables();
                        $this->redirectTo( osc_item_url() );
                    } else {
                        $this->redirectTo(osc_item_send_friend_url() );
                    }
                break;
                case 'contact':
                    $item = $this->itemManager->findByPrimaryKey( Params::getParam('id') ) ;
                    if( empty($item) ){
                        osc_add_flash_error_message( _m("This item doesn't exist") );
                        $this->redirectTo( osc_base_url(true) );
                    } else {
                        $this->_exportVariableToView('item', $item) ;

                        if( osc_item_is_expired () ) {
                            osc_add_flash_error_message( _m("We're sorry, but the item has expired. You can't contact the seller") ) ;
                            $this->redirectTo( osc_item_url() );
                        }

                        if( osc_reg_user_can_contact() && osc_is_web_user_logged_in() || !osc_reg_user_can_contact() ){
                            $this->doView('item-contact.php');
                        } else {
                            osc_add_flash_error_message( _m("You can't contact the seller, only registered users can") ) ;
                            $this->redirectTo( osc_item_url() );
                        }
                    }
                break;
                case 'contact_post':
                    $item = $this->itemManager->findByPrimaryKey( Params::getParam('id') ) ;
                    $this->_exportVariableToView('item', $item) ;
                    if ((osc_recaptcha_private_key() != '') && Params::existParam("recaptcha_challenge_field")) {
                        if(!osc_check_recaptcha()) {
                            osc_add_flash_error_message( _m('The Recaptcha code is wrong') ) ;
                            Session::newInstance()->_setForm("yourEmail",   Params::getParam('yourEmail'));
                            Session::newInstance()->_setForm("yourName",    Params::getParam('yourName'));
                            Session::newInstance()->_setForm("phoneNumber", Params::getParam('phoneNumber'));
                            Session::newInstance()->_setForm("message_body",Params::getParam('message'));
                            $this->redirectTo( osc_item_url( ) );
                            return false; // BREAK THE PROCESS, THE RECAPTCHA IS WRONG
                        }
                    }

                    if( osc_isExpired($item['dt_expiration']) ) {
                        osc_add_flash_error_message( _m("We're sorry, but the item has expired. You can't contact the seller") ) ;
                        $this->redirectTo(osc_item_url());
                    }

                    $mItem = new ItemActions(false);

                    $result = $mItem->contact();
                    
                    if(is_string($result)){
                        osc_add_flash_error_message( $result ) ;
                    } else {
                        osc_add_flash_ok_message( _m("We've just sent an e-mail to the seller") ) ;
                    }
                    
                    $this->redirectTo( osc_item_url( ) );

                    break;
                case 'add_comment':
                    $mItem  = new ItemActions(false) ;
                    $status = $mItem->add_comment() ;
                    switch ($status) {
                        case -1: $msg = _m('Sorry, we could not save your comment. Try again later') ;
                                 osc_add_flash_error_message($msg) ;
                        break ;
                        case 1:  $msg = _m('Your comment is awaiting moderation') ;
                                 osc_add_flash_info_message($msg) ;
                        break ;
                        case 2:  $msg = _m('Your comment has been approved') ;
                                 osc_add_flash_ok_message($msg) ;
                        break ;
                        case 3:  $msg = _m('Please fill the required field (email)') ;
                                 osc_add_flash_warning_message($msg) ;
                        break ;
                        case 4:  $msg = _m('Please type a comment') ;
                                 osc_add_flash_warning_message($msg) ;
                        break ;
                        case 5:  $msg = _m('Your comment has been marked as spam') ;
                                 osc_add_flash_error_message($msg) ;
                        break ;
                    }

                    $this->redirectTo( osc_item_url() ) ;
                    break ;
                case 'delete_comment':
                    $mItem = new ItemActions(false);
                    $status = $mItem->add_comment();

                    $itemId    = Params::getParam('id');
                    $commentId = Params::getParam('comment');

                    $item = Item::newInstance()->findByPrimaryKey($itemId);

                    if( count($item) == 0 ) {
                        osc_add_flash_error_message( _m("This item doesn't exist") );
                        $this->redirectTo( osc_base_url(true) );
                    }

                    View::newInstance()->_exportVariableToView('item', $item);

                    if($this->userId == null) {
                        osc_add_flash_error_message(_m('You must be logged in to delete a comment') );
                        $this->redirectTo( osc_item_url() );
                    }

                    $commentManager = ItemComment::newInstance();
                    $aComment = $commentManager->findByPrimaryKey($commentId);

                    if( count($aComment) == 0 ) {
                        osc_add_flash_error_message( _m("The comment doesn't exist") );
                        $this->redirectTo( osc_item_url() );
                    }

                    if( $aComment['b_active'] != 1 ) {
                        osc_add_flash_error_message( _m('The comment is not active, you cannot delete it') );
                        $this->redirectTo( osc_item_url() );
                    }

                    if($aComment['fk_i_user_id'] != $this->userId) {
                        osc_add_flash_error_message( _m('The comment was not added by you, you cannot delete it') );
                        $this->redirectTo( osc_item_url() );
                    }

                     $commentManager->deleteByPrimaryKey($commentId);
                     osc_add_flash_ok_message( _m('The comment has been deleted' ) ) ;
                     $this->redirectTo( osc_item_url() );
                break;
                default:
                    // if there isn't ID, show an error 404
                    if( Params::getParam('id') == '') {
                        $this->do404() ;
                        return ;
                    }

                    if( Params::getParam('lang') != '' ) {
                        Session::newInstance()->_set('userLocale', Params::getParam('lang')) ;
                    }

                    $item = $this->itemManager->findByPrimaryKey( Params::getParam('id') );
                    // if item doesn't exist show an error 404
                    if( count($item) == 0 ) {
                        $this->do404() ;
                        return ;
                    }

                    if ($item['b_active'] != 1) {
                        if( $this->userId == $item['fk_i_user_id'] ) {
                            osc_add_flash_warning_message( _m("The item hasn't been validated. Please validate it in order to show it to the rest of users") );
                        } else {
                            osc_add_flash_warning_message( _m("This item hasn't been validated") );
                            $this->redirectTo( osc_base_url(true) );
                        }
                    } else if ($item['b_enabled'] == 0) {
                        osc_add_flash_warning_message( _m('The item has been suspended') );
                        $this->redirectTo( osc_base_url(true) );
                    }
                    $mStats = new ItemStats();
                    $mStats->increase('i_num_views', $item['pk_i_id']);

                    foreach($item['locale'] as $k => $v) {
                        $item['locale'][$k]['s_title'] = osc_apply_filter('item_title',$v['s_title']);
                        $item['locale'][$k]['s_description'] = nl2br(osc_apply_filter('item_description',$v['s_description']));
                    }

                    $this->_exportVariableToView('item', $item);

                    osc_run_hook('show_item', $item) ;

                    $this->doView('item.php') ;
                break;
            }
        }

        //hopefully generic...
        function doView($file)
        {
            osc_run_hook("before_html");
            osc_current_web_theme_path($file) ;
            Session::newInstance()->_clearVariables();
            osc_run_hook("after_html");
        }
    }

    /* file end: ./item.php */
?>