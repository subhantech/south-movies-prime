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

    class CAdminLanguages extends AdminSecBaseModel
    {
        //specific for this class
        private $localeManager ;

        function __construct()
        {
            parent::__construct() ;

            //specific things for this class
            $this->localeManager = OSCLocale::newInstance() ;
        }

        //Business Layer...
        function doModel()
        {
            switch ($this->action) {
                case('add'):                // caliing add view
                                            $this->doView('languages/add.php') ;
                break ;
                case('add_post'):           // adding a new language
                                            $filePackage = Params::getFiles('package') ;
                                            if( isset($filePackage['size']) && $filePackage['size'] != 0 ) {
                                                $path         = osc_translations_path();
                                                (int) $status = osc_unzip_file($filePackage['tmp_name'], $path) ;
                                            } else {
                                                $status = 3 ;
                                            }

                                            switch ($status) {
                                                case(0):    $msg = _m('The translation folder is not writable') ;
                                                            osc_add_flash_error_message($msg, 'admin') ;
                                                break;
                                                case(1):    if( osc_checkLocales() ) {
                                                                $msg = _m('The language has been installed correctly') ;
                                                                osc_add_flash_ok_message($msg, 'admin') ;
                                                            } else {
                                                                $msg = _m('There was a problem adding the language') ;
                                                                osc_add_flash_error_message($msg, 'admin') ;
                                                            }
                                                break;
                                                case(2):    $msg = _m('The zip file is not valid') ;
                                                            osc_add_flash_error_message($msg, 'admin') ;
                                                break;
                                                case(3):    $msg = _m('No file was uploaded') ;
                                                            osc_add_flash_warning_message($msg, 'admin') ;
                                                            $this->redirectTo(osc_admin_base_url(true)."?page=languages&action=add") ;
                                                break;
                                                case(-1):
                                                default:    $msg = _m('There was a problem adding the language') ;
                                                            osc_add_flash_error_message($msg, 'admin') ;
                                                break;
                                            }

                                            $this->redirectTo( osc_admin_base_url(true) . '?page=languages' ) ;
                break ;
                case('edit'):               // editing a language
                                            $sLocale = Params::getParam('id');
                                            if( !preg_match('/.{2}_.{2}/', $sLocale) ) {
                                                osc_add_flash_error_message( _m('Language id isn\'t in the correct format'), 'admin');
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages');
                                            }

                                            $aLocale = $this->localeManager->findByPrimaryKey($sLocale);

                                            if(count($aLocale) == 0) {
                                                osc_add_flash_error_message( _m('Language id doesn\'t exist'), 'admin');
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages');
                                            }

                                            $this->_exportVariableToView("aLocale", $aLocale);
                                            $this->doView('languages/frm.php') ;
                break ;
                case('edit_post'):          // edit language post
                                            $iUpdated               = 0;
                                            $languageCode           = Params::getParam('pk_c_code');
                                            $enabledWebstie         = Params::getParam('b_enabled');
                                            $enabledBackoffice      = Params::getParam('b_enabled_bo');
                                            $languageName           = Params::getParam('s_name');
                                            $languageShortName      = Params::getParam('s_short_name');
                                            $languageDescription    = Params::getParam('s_description');
                                            $languageCurrencyFormat = Params::getParam('s_currency_format');
                                            $languageDecPoint       = Params::getParam('s_dec_point');
                                            $languageNumDec         = Params::getParam('i_num_dec');
                                            $languageThousandsSep   = Params::getParam('s_thousands_sep');
                                            $languageDateFormat     = Params::getParam('s_date_format');
                                            $languageStopWords      = Params::getParam('s_stop_words');


                                            // formatting variables
                                            if( !preg_match('/.{2}_.{2}/', $languageCode) ) {
                                                osc_add_flash_error_message( _m('Language id isn\'t in the correct format'), 'admin');
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages');
                                            }
                                            $enabledWebstie         = ($enabledWebstie != '' ? true : false);
                                            $enabledBackoffice      = ($enabledBackoffice != '' ? true : false);
                                            $languageName           = strip_tags($languageName);
                                            $languageName           = trim($languageName);

                                            $languageShortName      = strip_tags($languageShortName);
                                            $languageShortName      = trim($languageShortName);

                                            $languageDescription    = strip_tags($languageDescription);
                                            $languageDescription    = trim($languageDescription);

                                            $languageCurrencyFormat = strip_tags($languageCurrencyFormat);
                                            $languageCurrencyFormat = trim($languageCurrencyFormat);
                                            $languageDateFormat     = strip_tags($languageDateFormat);
                                            $languageDateFormat     = trim($languageDateFormat);
                                            $languageStopWords      = strip_tags($languageStopWords);
                                            $languageStopWords      = trim($languageStopWords);
                                            
                                            $msg = '';
                                            if(!osc_validate_text($languageName)) {
                                                $msg .= _m("Language name field is required")."<br/>";
                                            }
                                            if(!osc_validate_text($languageShortName)) {
                                                $msg .= _m("Language short name field is required")."<br/>";
                                            }
                                            if(!osc_validate_text($languageDescription)) {
                                                $msg .= _m("Language description field is required")."<br/>";
                                            }
                                            if(!osc_validate_text($languageCurrencyFormat)) {
                                                $msg .= _m("Currency format field is required")."<br/>";
                                            }
                                            if(!osc_validate_int($languageNumDec)) {
                                                $msg .= _m("Number of decimals has to be numeric only")."<br/>";
                                            }
                                            if($msg!='') {
                                                osc_add_flash_error_message( $msg, 'admin');
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages&action=edit&id='.$languageCode);
                                            }

                                            $array = array('b_enabled'         => $enabledWebstie
                                                          ,'b_enabled_bo'      => $enabledBackoffice
                                                          ,'s_name'            => $languageName
                                                          ,'s_short_name'      => $languageShortName
                                                          ,'s_description'     => $languageDescription
                                                          ,'s_currency_format' => $languageCurrencyFormat
                                                          ,'s_dec_point'       => $languageDecPoint
                                                          ,'i_num_dec'         => $languageNumDec
                                                          ,'s_thousands_sep'   => $languageThousandsSep
                                                          ,'s_date_format'     => $languageDateFormat
                                                          ,'s_stop_words'      => $languageStopWords);

                                            $iUpdated = $this->localeManager->update($array, array('pk_c_code' => $languageCode));
                                            if($iUpdated > 0) {
                                                osc_add_flash_ok_message(sprintf(_m('%s has been updated'), $languageShortName), 'admin');
                                            }
                                            $this->redirectTo(osc_admin_base_url(true).'?page=languages') ;
                break ;
                case('enable_selected'):
                                            $msg      = _m('Selected languages have been enabled for the website') ;
                                            $iUpdated = 0 ;
                                            $aValues  = array('b_enabled' => 1) ;

                                            $id = Params::getParam('id') ;

                                            if( !is_array($id) ) {
                                                osc_add_flash_warning_message( _m("The language ids aren't in the correct format"), 'admin') ;
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                                            }

                                            foreach( $id as $i ) {
                                                $iUpdated += $this->localeManager->update($aValues, array('pk_c_code' => $i)) ;
                                            }

                                            if( $iUpdated > 0 ) {
                                                osc_add_flash_ok_message($msg, 'admin') ;
                                            }

                                            $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                break ;
                case('disable_selected'):
                                            $msg         = _m('Selected languages have been disabled for the website') ;
                                            $msg_warning = '' ;
                                            $iUpdated    = 0 ;
                                            $aValues     = array('b_enabled' => 0) ;

                                            $id = Params::getParam('id') ;

                                            if( !is_array($id) ) {
                                                osc_add_flash_warning_message( _m("The language ids aren't in the correct format"), 'admin') ;
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                                            }

                                            foreach( $id as $i ) {
                                                if( osc_language() == $i ) {
                                                    $msg_warning = sprintf( _m("%s can't be disabled because is the default language"), osc_language() ) ;
                                                    continue ;
                                                }
                                                $iUpdated += $this->localeManager->update($aValues, array('pk_c_code' => $i)) ;
                                            }

                                            if( $msg_warning != '' ) {
                                                if( $iUpdated > 0 ) {
                                                    osc_add_flash_warning_message($msg . '</p><p>' . $msg_warning, 'admin') ;
                                                } else {
                                                    osc_add_flash_warning_message($msg_warning, 'admin') ;
                                                }
                                            } else {
                                                osc_add_flash_ok_message($msg, 'admin') ;
                                            }

                                            $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                break ;
                case('enable_bo_selected'):
                                            $msg      = _m('Selected languages have been enabled for the backoffice (oc-admin)') ;
                                            $iUpdated = 0 ;
                                            $aValues  = array('b_enabled_bo' => 1) ;

                                            $id = Params::getParam('id') ;

                                            if( !is_array($id) ) {
                                                osc_add_flash_warning_message( _m("The language ids aren't in the correct format"), 'admin') ;
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                                            }

                                            foreach( $id as $i ) {
                                                $iUpdated += $this->localeManager->update($aValues, array('pk_c_code' => $i)) ;
                                            }

                                            if( $iUpdated > 0 ) {
                                                osc_add_flash_ok_message($msg, 'admin') ;
                                            }

                                            $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                break ;
                case('disable_bo_selected'):
                                            $msg         = _m('Selected languages have been disabled for the backoffice (oc-admin)') ;
                                            $msg_warning = '' ;
                                            $iUpdated    = 0 ;
                                            $aValues     = array('b_enabled_bo' => 0) ;

                                            $id = Params::getParam('id') ;

                                            if( !is_array($id) ) {
                                                osc_add_flash_warning_message( _m("The language ids aren't in the correct format"), 'admin') ;
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                                            }

                                            foreach( $id as $i ) {
                                                if( osc_language() == $i ) {
                                                    $msg_warning = sprintf( _m("%s can't be disabled because is the default language"), osc_language() ) ;
                                                    continue ;
                                                }
                                                $iUpdated += $this->localeManager->update($aValues, array('pk_c_code' => $i)) ;
                                            }

                                            if( $msg_warning != '' ) {
                                                if( $iUpdated > 0 ) {
                                                    osc_add_flash_warning_message($msg . '</p><p>' . $msg_warning, 'admin') ;
                                                } else {
                                                    osc_add_flash_warning_message($msg_warning, 'admin') ;
                                                }
                                            } else {
                                                osc_add_flash_ok_message($msg, 'admin') ;
                                            }

                                            $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                break ;
                case('delete'):             if( is_array(Params::getParam('id') ) ) {
                                                $default_lang = osc_language() ;
                                                foreach( Params::getParam('id') as $code ) {
                                                    if( $default_lang != $code ) {
                                                        if($this->localeManager->deleteLocale($code)) {
                                                            if( !osc_deleteDir(osc_translations_path() . $code) ) {
                                                                osc_add_flash_error_message( sprintf( _m("Directory '%s' couldn't be removed"), $code), 'admin') ;
                                                            } else {
                                                                osc_add_flash_ok_message( sprintf( _m('Directory "%s" has been successfully removed'), $code), 'admin');
                                                            }
                                                        } else {
                                                            osc_add_flash_error_message( sprintf( _m("Directory '%s' couldn't be removed ;)"), $code), 'admin') ;
                                                        }
                                                    } else {
                                                        osc_add_flash_error_message( sprintf( _m("Directory '%s' couldn't be removed because it's the default language. Set another language as default first and try again"), $code), 'admin') ;
                                                    }
                                                }
                                            }
                                            $this->redirectTo(osc_admin_base_url(true) . '?page=languages') ;
                break ;
                default:
                                            $locales = OSCLocale::newInstance()->listAll() ;

                                            $this->_exportVariableToView('locales', $locales) ;
                                            $this->doView('languages/index.php') ;
                break ;
            }
        }

        //hopefully generic...
        function doView($file)
        {
            osc_current_admin_theme_path($file) ;
            Session::newInstance()->_clearVariables() ;
        }
    }

    /* file end: ./oc-admin/languages.php */
?>
