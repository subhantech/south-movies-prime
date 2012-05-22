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

    function fn_email_alert_validation($alert, $email, $secret) {
        $user['s_name'] = "";
                                    
        // send alert validation email
        $prefLocale = osc_language() ;
        $page = Page::newInstance()->findByInternalName('email_alert_validation') ;
        $page_description = $page['locale'] ;

        $_title = osc_apply_filter('email_title', osc_apply_filter('email_alert_validation_title', $page_description[$prefLocale]['s_title']));
        $_body  = osc_apply_filter('email_description', osc_apply_filter('email_alert_validation_description', $page_description[$prefLocale]['s_text']));

        $validation_link  = osc_user_activate_alert_url( $secret, $email );

        $words = array() ;
        $words[] = array('{USER_NAME}'    , '{USER_EMAIL}', '{VALIDATION_LINK}') ;
        $words[] = array($user['s_name']  , $email        , $validation_link ) ;
        $title = osc_mailBeauty($_title, $words) ;
        $body  = osc_mailBeauty($_body , $words) ;

        $params = array(
            'subject' => $_title
            ,'to' => $email
            ,'to_name' => $user['s_name']
            ,'body' => $body
            ,'alt_body' => $body
        ) ;

        osc_sendMail($params) ;
    }
    osc_add_hook('hook_email_alert_validation', 'fn_email_alert_validation');
   
    function fn_alert_email_hourly($user, $ads, $s_search) {
        $prefLocale = osc_language() ;
        $page = Page::newInstance()->findByInternalName('alert_email_hourly') ;

        $page_description = $page['locale'] ;

        $_title = osc_apply_filter('email_title', osc_apply_filter('alert_email_hourly_title', $page_description[$prefLocale]['s_title']));
        $_body  = osc_apply_filter('email_description', osc_apply_filter('alert_email_hourly_description', $page_description[$prefLocale]['s_text']));
        
        if($user['fk_i_user_id']!=0) {
            $user = User::newInstance()->findByPrimaryKey($user['fk_i_user_id']);
        } else {
            $user['s_name'] = $user['s_email'];
        }

        $unsub_link = osc_user_unsubscribe_alert_url($user['s_email'], $s_search['s_secret']);

        $unsub_link = "<a href='". $unsub_link ."'>unsubscribe alert</a>";

        $words = array() ;
        $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{ADS}', '{UNSUB_LINK}') ;
        $words[] = array($user['s_name'], $user['s_email'], $ads, $unsub_link) ;
        $title = osc_mailBeauty($_title, $words) ;
        $body = osc_mailBeauty($_body, $words) ;

        $params = array(
            'subject' => $title
            ,'to' => $user['s_email']
            ,'to_name' => $user['s_name']
            ,'body' => $body
            ,'alt_body' => $body
        ) ;

        osc_sendMail($params) ;

    }
    osc_add_hook('hook_alert_email_hourly', 'fn_alert_email_hourly');

    function fn_alert_email_daily($user, $ads, $s_search) {
        $prefLocale = osc_language() ;
        $page = Page::newInstance()->findByInternalName('alert_email_daily') ;

        $page_description = $page['locale'] ;

        $_title = osc_apply_filter('email_title', osc_apply_filter('alert_email_daily_title', $page_description[$prefLocale]['s_title']));
        $_body  = osc_apply_filter('email_description', osc_apply_filter('alert_email_daily_description', $page_description[$prefLocale]['s_text']));
        
        if($user['fk_i_user_id']!=0) {
            $user = User::newInstance()->findByPrimaryKey($user['fk_i_user_id']);
        } else {
            $user['s_name'] = $user['s_email'];
        }

        $unsub_link = osc_user_unsubscribe_alert_url($user['s_email'], $s_search['s_secret']);

        $unsub_link = "<a href='". $unsub_link ."'>unsubscribe alert</a>";

        $words = array() ;
        $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{ADS}', '{UNSUB_LINK}') ;
        $words[] = array($user['s_name'], $user['s_email'], $ads, $unsub_link) ;
        $title = osc_mailBeauty($_title, $words) ;
        $body = osc_mailBeauty($_body, $words) ;

        $params = array(
            'subject' => $title
            ,'to' => $user['s_email']
            ,'to_name' => $user['s_name']
            ,'body' => $body
            ,'alt_body' => $body
        ) ;

        osc_sendMail($params) ;

    }
    osc_add_hook('hook_alert_email_daily', 'fn_alert_email_daily');

    function fn_alert_email_weekly($user, $ads, $s_search) {
        $prefLocale = osc_language() ;
        $page = Page::newInstance()->findByInternalName('alert_email_weekly') ;

        $page_description = $page['locale'] ;

        $_title = osc_apply_filter('email_title', osc_apply_filter('alert_email_weekly_title', $page_description[$prefLocale]['s_title']));
        $_body  = osc_apply_filter('email_description', osc_apply_filter('alert_email_weekly_description', $page_description[$prefLocale]['s_text']));
        
        if($user['fk_i_user_id']!=0) {
            $user = User::newInstance()->findByPrimaryKey($user['fk_i_user_id']);
        } else {
            $user['s_name'] = $user['s_email'];
        }

        $unsub_link = osc_user_unsubscribe_alert_url($user['s_email'], $s_search['s_secret']);

        $unsub_link = "<a href='". $unsub_link ."'>unsubscribe alert</a>";

        $words = array() ;
        $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{ADS}', '{UNSUB_LINK}') ;
        $words[] = array($user['s_name'], $user['s_email'], $ads, $unsub_link) ;
        $title = osc_mailBeauty($_title, $words) ;
        $body = osc_mailBeauty($_body, $words) ;

        $params = array(
            'subject' => $title
            ,'to' => $user['s_email']
            ,'to_name' => $user['s_name']
            ,'body' => $body
            ,'alt_body' => $body
        ) ;

        osc_sendMail($params) ;

    }
    osc_add_hook('hook_alert_email_weekly', 'fn_alert_email_weekly');

    function fn_alert_email_instant($user, $ads, $s_search) {
        $prefLocale = osc_language() ;
        $page = Page::newInstance()->findByInternalName('alert_email_instant') ;

        $page_description = $page['locale'] ;

        $_title = osc_apply_filter('email_title', osc_apply_filter('alert_email_instant_title', $page_description[$prefLocale]['s_title']));
        $_body  = osc_apply_filter('email_description', osc_apply_filter('alert_email_instant_description', $page_description[$prefLocale]['s_text']));
        
        if($user['fk_i_user_id']!=0) {
            $user = User::newInstance()->findByPrimaryKey($user['fk_i_user_id']);
        } else {
            $user['s_name'] = $user['s_email'];
        }

        $unsub_link = osc_user_unsubscribe_alert_url($user['s_email'], $s_search['s_secret']);

        $unsub_link = "<a href='". $unsub_link ."'>unsubscribe alert</a>";

        $words = array() ;
        $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{ADS}', '{UNSUB_LINK}') ;
        $words[] = array($user['s_name'], $user['s_email'], $ads, $unsub_link) ;
        $title = osc_mailBeauty($_title, $words) ;
        $body = osc_mailBeauty($_body, $words) ;

        $params = array(
            'subject' => $title
            ,'to' => $user['s_email']
            ,'to_name' => $user['s_name']
            ,'body' => $body
            ,'alt_body' => $body
        ) ;

        osc_sendMail($params) ;

    }
    osc_add_hook('hook_alert_email_instant', 'fn_alert_email_instant');

    function fn_email_comment_validated($aComment) {
        $mPages = new Page() ;
        $locale = osc_current_user_locale() ;

        $aPage = $mPages->findByInternalName('email_comment_validated') ;

        $content = array() ;
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale] ;
        } else {
            $content = current($aPage['locale']) ;
        }

        if (!is_null($content)) {
            $words   = array();
            $words[] = array('{COMMENT_AUTHOR}', '{COMMENT_EMAIL}',
                             '{COMMENT_TITLE}', '{COMMENT_BODY}',
                             '{WEB_URL}', '{ITEM_URL}',
                             '{ITEM_LINK}') ;
            $words[] = array($aComment['s_author_name'], $aComment['s_author_email'],
                             $aComment['s_title'], $aComment['s_body'],
                             osc_base_url(), osc_item_url(),
                             '<a href="' . osc_item_url() . '">' . osc_item_url() . '</a>') ;
            $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_comment_validated_title', $content['s_title'])), $words) ;
            $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_comment_validated_description', $content['s_text'])), $words) ;

            $emailParams = array('subject'  => $title
                                 ,'to'       => $aComment['s_author_email']
                                 ,'to_name'  => $aComment['s_author_name']
                                 ,'body'     => $body
                                 ,'alt_body' => $body
            ) ;
            osc_sendMail($emailParams) ;
        }
    }
    osc_add_hook('hook_email_comment_validated', 'fn_email_comment_validated');
    
    function fn_email_new_item_non_register_user($item) {
        $mPages = new Page() ;
        $aPage = $mPages->findByInternalName('email_new_item_non_register_user') ;
        $locale = osc_current_user_locale() ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }
        $item_url = osc_item_url( ) ;
        $item_url = '<a href="'.$item_url.'" >'.$item_url.'</a>';
        $edit_url = osc_item_edit_url( $item['s_secret'], $item['pk_i_id'] );
        $delete_url = osc_item_delete_url( $item['s_secret'],  $item['pk_i_id'] );

        $words   = array();
        $words[] = array('{ITEM_ID}', '{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{ITEM_TITLE}',
                        '{ITEM_URL}', '{WEB_TITLE}', '{EDIT_LINK}', '{EDIT_URL}', '{DELETE_LINK}', '{DELETE_URL}');
        $words[] = array($item['pk_i_id'], $item['s_contact_name'], $item['s_contact_email'], osc_base_url(), $item['s_title'],
        $item_url, osc_page_title(), '<a href="' . $edit_url . '">' . $edit_url . '</a>', $edit_url, '<a href="' . $delete_url . '">' . $delete_url . '</a>', $delete_url) ;
        $title   = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_new_item_non_register_user_title', $content['s_title'])), $words) ;
        $body    = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_new_item_non_register_user_description', $content['s_text'])), $words) ;

        $emailParams =  array(
            'subject' => $title
            ,'to' => $item['s_contact_email']
            ,'to_name' => $item['s_contact_name']
            ,'body' => $body
            ,'alt_body' => $body
        );

        osc_sendMail($emailParams);
    }
    osc_add_hook('hook_email_new_item_non_register_user', 'fn_email_new_item_non_register_user');
    
    function fn_email_user_forgot_password($user, $password_url) {
        $aPage = Page::newInstance()->findByInternalName('email_user_forgot_password');

        $content = array();
        $locale = osc_current_user_locale() ;
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        if (!is_null($content)) {
            $words   = array();
                $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{WEB_TITLE}', '{IP_ADDRESS}',
                                 '{PASSWORD_LINK}', '{PASSWORD_URL}', '{DATE_TIME}');
                $words[] = array($user['s_name'], $user['s_email'], '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', osc_page_title(),
                                 $_SERVER['REMOTE_ADDR'], '<a href="' . $password_url . '">' . $password_url . '</a>', $password_url, date('Y-m-d H:i:').'00');
            $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_user_forgot_pass_word_title',$content['s_title'])), $words);
            $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_user_forgot_password_description', $content['s_text'])), $words);

            $emailParams = array('subject'  => $title,
                                'to'       => $user['s_email'],
                                'to_name'  => $user['s_name'],
                                'body'     => $body,
                                'alt_body' => $body);
            osc_sendMail($emailParams);
        }
    }
    osc_add_hook('hook_email_user_forgot_password', 'fn_email_user_forgot_password');
    
    function fn_email_user_registration($user) {
    
        $pageManager = new Page() ;
        $locale = osc_current_user_locale() ;
        $aPage = $pageManager->findByInternalName('email_user_registration') ;
        $content = array() ;
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale] ;
        } else {
            $content = current($aPage['locale']) ;
        }

        if (!is_null($content)) {
            $words   = array();
            $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{WEB_TITLE}', '{WEB_URL}') ;
            $words[] = array($user['s_name'], $user['s_email'], osc_page_title(), '<a href="' . osc_base_url() . '" >' . osc_base_url() . '</a>' ) ;
            $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_user_registration_title', $content['s_title'])), $words) ;
            $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_user_regsitration_description', $content['s_text'])), $words) ;

            $emailParams = array(
                        'subject'  => $title
                        ,'to'       => $user['s_email']
                        ,'to_name'  => $user['s_name']
                        ,'body'     => $body
                        ,'alt_body' => $body
            );
            osc_sendMail($emailParams) ;
        }
    }
    osc_add_hook('hook_email_user_registration', 'fn_email_user_registration');

    function fn_email_new_email($new_email, $validation_url) {
        $locale = osc_current_user_locale() ;
        $aPage = Page::newInstance()->findByInternalName('email_new_email') ;
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale] ;
        } else {
            $content = current($aPage['locale']) ;
        }

        if (!is_null($content)) {

            $words = array() ;
            $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{WEB_TITLE}', '{VALIDATION_LINK}', '{VALIDATION_URL}') ;
            $words[] = array(Session::newInstance()->_get('userName'), Params::getParam('new_email'), '<a href="' . osc_base_url() . '" >' . osc_base_url() . '</a>', osc_page_title(), '<a href="' . $validation_url . '" >' . $validation_url . '</a>', $validation_url ) ;
            $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_new_email_title', $content['s_title'])), $words) ;
            $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_new_email_description', $content['s_text'])), $words) ;

            $params = array(
                    'subject' => $title
                    ,'to' => $new_email
                    ,'to_name' => Session::newInstance()->_get('userName')
                    ,'body' => $body
                    ,'alt_body' => $body
            ) ;
            osc_sendMail($params) ;
            osc_add_flash_ok_message( _m('We have sent you an e-mail. Follow the instructions to validate the changes')) ;
        } else {
            osc_add_flash_error_message( _m('We tried to sent you an e-mail, but it failed. Please, contact the administrator')) ;
        }
    }
    osc_add_hook('hook_email_new_email', 'fn_email_new_email');
    
    function fn_email_user_validation($user, $input) {
        $mPages = new Page() ;
        $locale = osc_current_user_locale() ;

        $aPage = $mPages->findByInternalName('email_user_validation') ;

        $content = array() ;
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale] ;
        } else {
            $content = current($aPage['locale']) ;
        }
                    
        if (!is_null($content)) {
            $validation_url = osc_user_activate_url($user['pk_i_id'], $input['s_secret']);
            $words   = array();
            $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{VALIDATION_LINK}', '{VALIDATION_URL}') ;
            $words[] = array($user['s_name'], $user['s_email'], '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', '<a href="' . $validation_url . '" >' . $validation_url . '</a>', '<a href="' . $validation_url . '" >' . $validation_url . '</a>') ;
            $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_user_validation_title', $content['s_title'])), $words) ;
            $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_user_validation_description', $content['s_text'])), $words) ;

            $emailParams = array('subject'  => $title
                                ,'to'       => $user['s_email']
                                ,'to_name'  => $user['s_name']
                                ,'body'     => $body
                                ,'alt_body' => $body
            ) ;
            osc_sendMail($emailParams) ;
        }
    }
    osc_add_hook('hook_email_user_validation', 'fn_email_user_validation');
    
    function fn_email_send_friend($aItem) {
        $item_url   = osc_item_url();
        $item_url = '<a href="'.$item_url.'" >'.$item_url.'</a>';

        $mPages = new Page();
        $aPage = $mPages->findByInternalName('email_send_friend');
        $locale = osc_current_user_locale();

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $words   = array() ;
        $words[] = array(
                    '{FRIEND_NAME}'
                    ,'{USER_NAME}'
                    ,'{USER_EMAIL}'
                    ,'{FRIEND_EMAIL}'
                    ,'{WEB_URL}'
                    ,'{ITEM_TITLE}'
                    ,'{COMMENT}'
                    ,'{ITEM_URL}'
                    ,'{WEB_TITLE}'
        ) ;
        $words[] = array(
                    $aItem['friendName']
                    ,$aItem['yourName']
                    ,$aItem['yourEmail']
                    ,$aItem['friendEmail']
                    ,'<a href="'.osc_base_url().'" >'.osc_base_url().'</a>'
                    ,$aItem['s_title']
                    ,$aItem['message']
                    ,$item_url
                    ,osc_page_title()
        ) ;

        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_send_friend_title', $content['s_title'])), $words) ;
        $body  = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_send_friend_description', $content['s_text'])), $words) ;

        $params = array(
            'from'      => $aItem['yourEmail']
           ,'from_name' => $aItem['yourName']
           ,'subject'   => $title
           ,'to'        => $aItem['friendEmail']
           ,'to_name'   => $aItem['friendName']
           ,'body'      => $body
           ,'alt_body'  => $body
        ) ;

        if( osc_notify_contact_friends() ) {
            $params['add_bcc'] = osc_contact_email() ;
        }

        osc_sendMail($params) ;
    }
    osc_add_hook('hook_email_send_friend', 'fn_email_send_friend');
    
    function fn_email_item_inquiry($aItem) {
        $id         = $aItem['id'] ;
        $yourEmail  = $aItem['yourEmail'] ;
        $yourName   = $aItem['yourName'] ;
        $phoneNumber= $aItem['phoneNumber'] ;
        $message    = $aItem['message'] ;

        $path = null ;
        $item = Item::newInstance()->findByPrimaryKey( $id ) ;
        View::newInstance()->_exportVariableToView('item', $item) ;

        $mPages = new Page() ;
        $aPage  = $mPages->findByInternalName('email_item_inquiry') ;
        $locale = osc_current_user_locale() ;

        $content = array() ;
        if( isset($aPage['locale'][$locale]['s_title']) ) {
            $content = $aPage['locale'][$locale] ;
        } else {
            $content = current($aPage['locale']) ;
        }

        $item_url = osc_item_url() ;
        $item_url = '<a href="' . $item_url . '" >' . $item_url . '</a>' ;

        $words   = array();
        $words[] = array('{CONTACT_NAME}', '{USER_NAME}', '{USER_EMAIL}', '{USER_PHONE}',
                             '{WEB_URL}', '{ITEM_TITLE}','{ITEM_URL}', '{COMMENT}') ;

        $words[] = array($item['s_contact_name'], $yourName, $yourEmail,
                         $phoneNumber, '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', $item['s_title'], $item_url, $message ) ;

        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_item_inquiry_title', $content['s_title'])), $words) ;
        $body  = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_item_inquiry_description', $content['s_text'])), $words) ;

        $from      = osc_contact_email() ;
        $from_name = osc_page_title() ;

        $emailParams = array (
            'from'      => $from
           ,'from_name' => $from_name
           ,'subject'   => $title
           ,'to'        => $item['s_contact_email']
           ,'to_name'   => $item['s_contact_name']
           ,'body'      => $body
           ,'alt_body'  => $body
           ,'reply_to'  => $yourEmail
        ) ;

        if( osc_notify_contact_item() ) {
            $emailParams['add_bcc'] = osc_contact_email() ;
        }

        if( osc_item_attachment() ) {
            $attachment   = Params::getFiles('attachment');
            $resourceName = $attachment['name'] ;
            $tmpName      = $attachment['tmp_name'] ;
            $resourceType = $attachment['type'] ;
            $path         = osc_content_path() . 'uploads/' . time() . '_' . $resourceName ;

            if( !is_writable(osc_content_path() . 'uploads/') ) {
                osc_add_flash_error_message( _m('There has been some errors sending the message') ) ;
            }

            if( !move_uploaded_file($tmpName, $path) ) {
                unset($path) ;
            }
        }

        if( isset($path) ) {
            $emailParams['attachment'] = $path ;
        }

        osc_sendMail($emailParams) ;

        @unlink($path) ;
    }
    osc_add_hook('hook_email_item_inquiry', 'fn_email_item_inquiry');
    
    function fn_email_new_comment_admin($aItem) {
        $authorName     = trim($aItem['authorName']);
        $authorName     = strip_tags($authorName);
        $authorEmail    = trim($aItem['authorEmail']);
        $authorEmail    = strip_tags($authorEmail);
        $body           = trim($aItem['body']);
        $body           = strip_tags($body);
        $title          = $aItem['title'] ;
        $itemId         = $aItem['id'] ;
        $userId         = $aItem['userId'] ;
        $admin_email = osc_contact_email() ;
        $prefLocale  = osc_language() ;

        $item = Item::newInstance()->findByPrimaryKey($itemId) ;
        View::newInstance()->_exportVariableToView('item', $item);
        $itemURL = osc_item_url() ;
        $itemURL = '<a href="'.$itemURL.'" >'.$itemURL.'</a>';
        
        $mPages = new Page() ;
        $aPage = $mPages->findByInternalName('email_new_comment_admin') ;
        $locale = osc_current_user_locale() ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $words   = array();
        $words[] = array('{COMMENT_AUTHOR}', '{COMMENT_EMAIL}', '{COMMENT_TITLE}',
                         '{COMMENT_TEXT}', '{ITEM_TITLE}', '{ITEM_ID}', '{ITEM_URL}');
        $words[] = array($authorName, $authorEmail, $title, $body, $item['s_title'], $itemId, $itemURL);
        $title_email = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_new_comment_admin_title', $content['s_title'])), $words);
        $body_email = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_new_comment_admin_description', $content['s_text'])), $words);

        $from = osc_contact_email() ;
        $from_name = osc_page_title() ;

        $emailParams = array(
                        'from'      => $admin_email
                        ,'from_name' => __('Admin mail system')
                        ,'subject'   => $title_email
                        ,'to'        => $admin_email
                        ,'to_name'   => __('Admin mail system')
                        ,'body'      => $body_email
                        ,'alt_body'  => $body_email
                        );
        osc_sendMail($emailParams) ;
    }
    osc_add_hook('hook_email_new_comment_admin', 'fn_email_new_comment_admin');
    
    function fn_email_item_validation($item) {
        View::newInstance()->_exportVariableToView('item', $item);
        $title  = osc_item_title();
        $contactEmail   = $item['s_contact_email'];
        $contactName    = $item['s_contact_name'];
        $mPages = new Page();
        $locale = osc_current_user_locale();
        $aPage = $mPages->findByInternalName('email_item_validation') ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $item_url = osc_item_url();
        $item_url = '<a href="'.$item_url.'" >'.$item_url.'</a>';


        $all = '';

        if (isset($item['locale'])) {
            foreach ($item['locale'] as $locale => $data) {
                $locale_name = OSCLocale::newInstance()->findByCode($locale);
                $all .= '<br/>';
                if (isset($locale_name[0]) && isset($locale_name[0]['s_name'])) {
                    $all .= __('Language') . ': ' . $locale_name[0]['s_name'] . '<br/>';
                } else {
                    $all .= __('Language') . ': ' . $locale . '<br/>';
                }
                $all .= __('Title') . ': ' . $data['s_title'] . '<br/>';
                $all .= __('Description') . ': ' . $data['s_description'] . '<br/>';
                $all .= '<br/>';
            }
        } else {
            $all .= __('Title') . ': ' . $item['s_title'] . '<br/>';
            $all .= __('Description') . ': ' . $item['s_description'] . '<br/>';
        }

        // Format activation URL
        $validation_url = osc_item_activate_url( $item['s_secret'], $item['pk_i_id'] );

        $words   = array();
        $words[] = array('{ITEM_DESCRIPTION_ALL_LANGUAGES}', '{ITEM_DESCRIPTION}', '{ITEM_COUNTRY}',
                         '{ITEM_PRICE}', '{ITEM_REGION}', '{ITEM_CITY}', '{ITEM_ID}', '{USER_NAME}',
                         '{USER_EMAIL}', '{WEB_URL}', '{ITEM_TITLE}', '{ITEM_URL}', '{WEB_TITLE}',
                         '{VALIDATION_LINK}', '{VALIDATION_URL}');
        $words[] = array($all, $item['s_description'], $item['s_country'], osc_prepare_price($item['i_price']),
                         $item['s_region'], $item['s_city'], $item['pk_i_id'], $item['s_contact_name'],
                         $item['s_contact_email'], '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', $item['s_title'], $item_url,
                         osc_page_title(), '<a href="' . $validation_url . '" >' . $validation_url . '</a>', $validation_url );
        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_item_validation_title', $content['s_title'])), $words);
        $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_item_validation_description', $content['s_text'])), $words);

        $emailParams =  array (
                            'subject'  => $title
                            ,'to'       => $contactEmail
                            ,'to_name'  => $contactName
                            ,'body'     => $body
                            ,'alt_body' => $body
                        );
        osc_sendMail($emailParams) ;

    }
    osc_add_hook('hook_email_item_validation', 'fn_email_item_validation');
    
    function fn_email_admin_new_item($item) {
        View::newInstance()->_exportVariableToView('item', $item);
        $title  = osc_item_title();
        $contactEmail   = $item['s_contact_email'];
        $contactName    = $item['s_contact_name'];
        $mPages = new Page();
        $locale = osc_current_user_locale();
        $aPage = $mPages->findByInternalName('email_admin_new_item') ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale] ;
        } else {
            $content = current($aPage['locale']) ;
        }

        $item_url = osc_item_url() ;
        $item_url = '<a href="'.$item_url.'" >'.$item_url.'</a>';

        $all = '' ;

        if (isset($item['locale'])) {
            foreach ($item['locale'] as $locale => $data) {
                $locale_name = OSCLocale::newInstance()->findByCode($locale);
                $all .= '<br/>';
                if (isset($locale_name[0]) && isset($locale_name[0]['s_name'])) {
                    $all .= __('Language') . ': ' . $locale_name[0]['s_name'] . '<br/>';
                } else {
                    $all .= __('Language') . ': ' . $locale . '<br/>';
                }
                $all .= __('Title') . ': ' . $data['s_title'] . '<br/>';
                $all .= __('Description') . ': ' . $data['s_description'] . '<br/>';
                $all .= '<br/>';
            }
        } else {
            $all .= __('Title') . ': ' . $item['s_title'] . '<br/>';
            $all .= __('Description') . ': ' . $item['s_description'] . '<br/>';
        }

        // Format activation URL
        $validation_url = osc_item_activate_url( $item['s_secret'], $item['pk_i_id'] );

        // Format admin edit URL
        $admin_edit_url =  osc_item_admin_edit_url( $item['pk_i_id'] );

        $words   = array();
        $words[] = array('{EDIT_LINK}', '{EDIT_URL}', '{ITEM_DESCRIPTION_ALL_LANGUAGES}', '{ITEM_DESCRIPTION}',
                         '{ITEM_COUNTRY}', '{ITEM_PRICE}', '{ITEM_REGION}', '{ITEM_CITY}', '{ITEM_ID}',
                         '{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{ITEM_TITLE}', '{ITEM_URL}',
                         '{WEB_TITLE}', '{VALIDATION_LINK}', '{VALIDATION_URL}');
        $words[] = array('<a href="' . $admin_edit_url . '" >' . $admin_edit_url . '</a>', $admin_edit_url, $all, $item['s_description'], $item['s_country'],
                         osc_prepare_price($item['i_price']), $item['s_region'], $item['s_city'], $item['pk_i_id'],
                         $item['s_contact_name'], $item['s_contact_email'], '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', $item['s_title'],
                         $item_url, osc_page_title(), '<a href="' . $validation_url . '" >' . $validation_url . '</a>', $validation_url );
        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_admin_new_item_title', $content['s_title'])), $words);
        $body  = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_admin_new_item_description', $content['s_text'])), $words);

        $emailParams = array(
                            'subject'  => $title
                            ,'to'       => osc_contact_email()
                            ,'to_name'  => 'admin'
                            ,'body'     => $body
                            ,'alt_body' => $body
        ) ;
        osc_sendMail($emailParams) ;
    }
    osc_add_hook('hook_email_admin_new_item', 'fn_email_admin_new_item');

    
    function fn_email_item_validation_non_register_user($item) {
        
        View::newInstance()->_exportVariableToView('item', $item);
        
        $mPages = new Page() ;
        $aPage = $mPages->findByInternalName('email_item_validation_non_register_user') ;
        $locale = osc_current_user_locale() ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $item_url = osc_item_url();
        $item_url = '<a href="'.$item_url.'" >'.$item_url.'</a>';
        $edit_url = osc_item_edit_url( $item['s_secret'], $item['pk_i_id'] );
        $delete_url = osc_item_delete_url( $item['s_secret'],  $item['pk_i_id'] );

        $all = '';

        if (isset($item['locale'])) {
            foreach ($item['locale'] as $locale => $data) {
                $locale_name = OSCLocale::newInstance()->findByCode($locale);
                $all .= '<br/>';
                if (isset($locale_name[0]) && isset($locale_name[0]['s_name'])) {
                    $all .= __('Language') . ': ' . $locale_name[0]['s_name'] . '<br/>';
                } else {
                    $all .= __('Language') . ': ' . $locale . '<br/>';
                }
                $all .= __('Title') . ': ' . $data['s_title'] . '<br/>';
                $all .= __('Description') . ': ' . $data['s_description'] . '<br/>';
                $all .= '<br/>';
            }
        } else {
            $all .= __('Title') . ': ' . $item['s_title'] . '<br/>';
            $all .= __('Description') . ': ' . $item['s_description'] . '<br/>';
        }

        // Format activation URL
        $validation_url = osc_item_activate_url( $item['s_secret'], $item['pk_i_id'] );

        $words   = array();
        $words[] = array('{ITEM_DESCRIPTION_ALL_LANGUAGES}', '{ITEM_DESCRIPTION}', '{ITEM_COUNTRY}',
                         '{ITEM_PRICE}', '{ITEM_REGION}', '{ITEM_CITY}', '{ITEM_ID}', '{USER_NAME}',
                         '{USER_EMAIL}', '{WEB_URL}', '{ITEM_TITLE}', '{ITEM_URL}', '{WEB_TITLE}',
                         '{VALIDATION_LINK}', '{VALIDATION_URL}',
                         '{EDIT_LINK}', '{EDIT_URL}', '{DELETE_LINK}', '{DELETE_URL}');
        $words[] = array($all, $item['s_description'], $item['s_country'], osc_prepare_price($item['i_price']),
                         $item['s_region'], $item['s_city'], $item['pk_i_id'], $item['s_contact_name'],
                         $item['s_contact_email'], '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', $item['s_title'], $item_url,
                         osc_page_title(), '<a href="' . $validation_url . '" >' . $validation_url . '</a>', $validation_url,
                         '<a href="' . $edit_url . '">' . $edit_url . '</a>', $edit_url, '<a href="' . $delete_url . '">' . $delete_url . '</a>', $delete_url);
        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_item_validation_non_register_user_title', $content['s_title'])), $words);
        $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_item_validation_non_register_user_description', $content['s_text'])), $words);

        $emailParams =  array(
            'subject' => $title
            ,'to' => $item['s_contact_email']
            ,'to_name' => $item['s_contact_name']
            ,'body' => $body
            ,'alt_body' => $body
        );

        osc_sendMail($emailParams);
    }
    osc_add_hook('hook_email_item_validation_non_register_user', 'fn_email_item_validation_non_register_user');
        
    function fn_email_admin_new_user($user) {
        $pageManager = new Page() ;
        $locale      = osc_current_user_locale() ;
        $aPage       = $pageManager->findByInternalName('email_admin_new_user') ;
        $content     = array() ;

        if( isset($aPage['locale'][$locale]['s_title']) ) {
            $content = $aPage['locale'][$locale] ;
        } else {
            $content = current($aPage['locale']) ;
        }

        if( !is_null($content) ) {
            $words   = array();
            $words[] = array(
                '{USER_NAME}',
                '{USER_EMAIL}',
                '{WEB_TITLE}',
                '{WEB_URL}',
            ) ;
            $words[] = array(
                $user['s_name'],
                $user['s_email'],
                osc_page_title(),
                '<a href="' . osc_base_url() . '" >' . osc_base_url() . '</a>'
            ) ;
            $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_user_registration_title', $content['s_title'])), $words) ;
            $body  = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_user_regsitration_description', $content['s_text'])), $words) ;

            $emailParams = array(
                'subject'  => $title,
                'to'       => osc_contact_email(),
                'to_name'  => osc_page_title(),
                'body'     => $body,
                'alt_body' => $body,
            ) ;
            osc_sendMail($emailParams) ;
        }
    }
    osc_add_hook('hook_email_admin_new_user', 'fn_email_admin_new_user') ;

    function fn_email_contact_user($id, $yourEmail, $yourName, $phoneNumber, $message) {
        $mPages = new Page();
        $aPage = $mPages->findByInternalName('email_contact_user');
        $locale = osc_current_user_locale() ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $words   = array();
        $words[] = array('{CONTACT_NAME}', '{USER_NAME}', '{USER_EMAIL}', '{USER_PHONE}',
                             '{WEB_URL}', '{COMMENT}');

        $words[] = array(osc_user_name(), $yourName, $yourEmail,
                         $phoneNumber, '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', $message );

        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_item_inquiry_title', $content['s_title'])), $words);
        $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_item_inquiry_description', $content['s_text'])), $words);

        $from = osc_contact_email() ;
        $from_name = osc_page_title() ;

        $emailParams = array (
            'from'      => $from
           ,'from_name' => $from_name
           ,'subject'   => $title
           ,'to'        => osc_user_email()
           ,'to_name'   => osc_user_name()
           ,'body'      => $body
           ,'alt_body'  => $body
           ,'reply_to'  => $yourEmail
        ) ;

        if( osc_notify_contact_item() ) {
            $emailParams['add_bcc'] = osc_contact_email() ;
        }

        osc_sendMail($emailParams);

    }
    osc_add_hook('hook_email_contact_user', 'fn_email_contact_user');
    
    function fn_email_new_comment_user($aItem) {
        $authorName     = trim($aItem['authorName']);
        $authorName     = strip_tags($authorName);
        $authorEmail    = trim($aItem['authorEmail']);
        $authorEmail    = strip_tags($authorEmail);
        $body           = trim($aItem['body']);
        $body           = strip_tags($body);
        $title          = $aItem['title'] ;
        $itemId         = $aItem['id'] ;
        $userId         = $aItem['userId'] ;
        $admin_email = osc_contact_email() ;
        $prefLocale  = osc_language() ;

        $item = Item::newInstance()->findByPrimaryKey($itemId) ;
        View::newInstance()->_exportVariableToView('item', $item);
        $itemURL = osc_item_url() ;
        $itemURL = '<a href="'.$itemURL.'" >'.$itemURL.'</a>';
        
        $mPages = new Page() ;
        $aPage = $mPages->findByInternalName('email_new_comment_user') ;
        $locale = osc_current_user_locale() ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $words   = array();
        $words[] = array('{COMMENT_AUTHOR}', '{COMMENT_EMAIL}', '{COMMENT_TITLE}',
                         '{COMMENT_TEXT}', '{ITEM_TITLE}', '{ITEM_ID}', '{ITEM_URL}', '{SELLER_NAME}', '{SELLER_EMAIL}');
        $words[] = array($authorName, $authorEmail, $title, $body, $item['s_title'], $itemId, $itemURL, $item['s_contact_name'], $item['s_contact_email']);
        $title_email = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_new_comment_user_title', $content['s_title'])), $words);
        $body_email = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_new_comment_user_description', $content['s_text'])), $words);

        $from = osc_contact_email() ;
        $from_name = osc_page_title() ;

        $emailParams = array(
                         'from'      => $admin_email
                        ,'from_name' => __('Admin mail system')
                        ,'subject'   => $title_email
                        ,'to'        => $item['s_contact_email']
                        ,'to_name'   => $item['s_contact_name']
                        ,'body'      => $body_email
                        ,'alt_body'  => $body_email
                        );
        osc_sendMail($emailParams) ;
    }
    osc_add_hook('hook_email_new_comment_user', 'fn_email_new_comment_user');

    
?>