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

    class PageForm extends Form {

        static public function primary_input_hidden($page = null) {
            if(isset($page['pk_i_id'])) {
                parent::generic_input_hidden("id", $page["pk_i_id"]) ;
            }
        }

        static public function internal_name_input_text($page = null) {
            $internal_name = '';
            if( is_array($page) && isset($page['s_internal_name']) ) {
                $internal_name = $page['s_internal_name'];
            }
            if( Session::newInstance()->_getForm('s_internal_name') != '' ) {
                $internal_name = Session::newInstance()->_getForm('s_internal_name');
            }
            parent::generic_input_text('s_internal_name', $internal_name, null, (isset($page['b_indelible']) && $page['b_indelible'] == 1) ? true : false) ;
        }

        static public function multilanguage_name_description($locales, $category = null) {
            $num_locales = count($locales);
            if($num_locales > 1) echo '<div class="tabber">';
            foreach($locales as $locale) {
                if($num_locales > 1) {
                    echo '<div class="tabbertab">';
                    echo '<h2>' . $locale['s_name'] . '</h2>';
                }
                echo '<div class="FormElement">';
                echo '<div class="FormElementName">' . __('Title') . '</div>';
                echo '<div class="FormElementInput">' ;
                $title = '';
                if(isset($category['locale'][$locale['pk_c_code']])) {
                    $title = $category['locale'][$locale['pk_c_code']]['s_title'];
                }
                if( Session::newInstance()->_getForm($locale['pk_c_code'] . '#s_title') != '' ) {
                    $title = Session::newInstance()->_getForm($locale['pk_c_code'] . '#s_title');
                }
                parent::generic_input_text($locale['pk_c_code'] . '#s_title', $title) ;
                echo '</div>' ;
                echo '</div>';
                echo '<div class="FormElement">';
                echo '<div class="FormElementName">' . __('Body') . '</div>';
                echo '<div class="FormElementInput">' ;
                $description = '';
                if(isset($category['locale'][$locale['pk_c_code']])) {
                    $description = $category['locale'][$locale['pk_c_code']]['s_text'];
                }
                if( Session::newInstance()->_getForm($locale['pk_c_code'] . '#s_text') != '' ) {
                    $description = Session::newInstance()->_getForm($locale['pk_c_code'] . '#s_text');
                }
                parent::generic_textarea($locale['pk_c_code'] . '#s_text', $description) ;
                echo '</div>';
                echo '</div>';
                if($num_locales > 1) {
                    echo '</div>';
                }
             }
             if($num_locales > 1) {
                 echo '</div>';
             }
        }
    }

?>