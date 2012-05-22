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

    class Session {
        //attributes
        private $session ;
        private static $instance ;

        public static function newInstance() {
            if(!self::$instance instanceof self) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        public function __construct() {}

        function session_start() {
            session_name('osclass') ;
            session_start() ;

            $this->session = $_SESSION ;
            if ($this->_get('messages') == '') {
                $this->_set( 'messages', array() ) ;
            }
            if( $this->_get('keepForm') == '' ){
                $this->_set( 'keepForm', array() ) ;
            }
            if( $this->_get('form') == '' ){
                $this->_set( 'form', array() ) ;
            }
        }

        function session_destroy() {
            session_destroy() ;
        }

        function _set($key, $value) {
            $_SESSION[$key] = $value ;
            $this->session[$key] = $value ;
        }

        function _get($key) {
            if (!isset($this->session[$key])) {
                return '' ;
            }

            return ($this->session[$key]) ;
        }

        function _drop($key) {
            unset($_SESSION[$key]) ;
            unset($this->session[$key]) ;

        }

        function _setReferer($value) {
            $_SESSION['osc_http_referer'] = $value ;
            $this->session['osc_http_referer'] = $value ;
            $_SESSION['osc_http_referer_state'] = 0 ;
            $this->session['osc_http_referer_state'] = 0 ;
        }

        function _getReferer() {
            if(isset($this->session['osc_http_referer'])) {
                return ($this->session['osc_http_referer']) ;
            } else {
                return '';
            }
        }

        function _dropReferer() {
            unset($_SESSION['osc_http_referer']) ;
            unset($this->session['osc_http_referer']) ;
            unset($_SESSION['osc_http_referer_state']) ;
            unset($this->session['osc_http_referer_state']) ;
        }

        function _view() {
            print_r($this->session) ;
        }

        function _setMessage($key, $value, $type) {
            $messages = $this->_get('messages') ;
            $messages[$key]['msg'] = $value ;
            $messages[$key]['type'] = $type ;
            $this->_set('messages', $messages) ;
        }

        function _getMessage($key) {
            $messages = $this->_get('messages') ;
            if ( isset($messages[$key]) ) {
                return ( $messages[$key] ) ;
            } else {
                return ( '' ) ;
            }
        }

        function _dropMessage($key) {
            $messages = $this->_get('messages') ;
            unset($messages[$key]) ;
            $this->_set('messages', $messages) ;
        }

        function _keepForm($key) {
            $aKeep = $this->_get('keepForm');
            $aKeep[$key] = 1;
            $this->_set('keepForm',$aKeep);
        }

        function _dropKeepForm($key = '') {
            $aKeep = $this->_get('keepForm');
            if($key!='') {
                unset( $aKeep[$key] );
                $this->_set('keepForm', $aKeep);
            } else {
                $this->_set('keepForm', array());
            }
        }

        function _setForm($key, $value) {
            $form = $this->_get('form') ;
            $form[$key] = $value ;
            $this->_set('form', $form) ;
        }

        function _getForm($key = '') {
            $form = $this->_get('form') ;
            if($key!='') {
                if ( isset($form[$key]) ) {
                    return ( $form[$key] ) ;
                } else {
                    return ( '' ) ;
                }
            } else {
                return $form;
            }
        }
        
        function _getKeepForm() {
            return $this->_get('keepForm');
        }

        function _viewMessage() {
            print_r($this->session['messages']) ;
        }

        function _viewForm() {
            print_r($_SESSION['form']) ;
        }

        function _viewKeep() {
            print_r($_SESSION['keepForm']) ;
        }
        
        function  _clearVariables() {
            $form = $this->_get('form');
            $aKeep = $this->_get('keepForm');
            if( is_array($form) ) {
                foreach($form as $key => $value) {
                    if( !isset($aKeep[$key]) ) {
                        unset($_SESSION['form'][$key]) ;
                        unset($this->session['form'][$key]) ;
                    }
                }
            }

            if(isset($this->session['osc_http_referer_state'])) {
                $this->session['osc_http_referer_state']++;
                $_SESSION['osc_http_referer_state']++;
                if((int)($this->session['osc_http_referer_state'])>=2) {
                    $this->_dropReferer();
                }
            }
        }
    }

?>