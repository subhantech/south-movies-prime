<?php
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

    $category = __get("category") ;
    $locales  = OSCLocale::newInstance()->listAllEnabled() ;
?>
<div class="iframe-category">
    <h3><?php _e('Edit category') ; ?></h3>
    <form action="<?php echo osc_admin_base_url(true) ; ?>" method="post">
        <input type="hidden" name="page" value="ajax" />
        <input type="hidden" name="action" value="edit_category_post" />
        <?php CategoryForm::primary_input_hidden($category) ; ?>
        <fieldset>
            <div class="input-line">
                <label><?php _e('Expirations days') ; ?></label>
                <div class="input micro">
                    <?php CategoryForm::expiration_days_input_text($category) ; ?>
                    <p class="help-inline"><?php _e("If the value is zero it means that there isn't expiration for this category") ; ?></p>
                </div>
            </div>
            <div class="input-line">
                <label></label>
                <div class="input">
                    <?php CategoryForm::multilanguage_name_description($locales, $category) ; ?>
                </div>
            </div>
            <div class="actions">
                <input type="submit" value="<?php echo osc_esc_html( __('Save changes') ) ; ?>" />
                <input type="button" onclick="$('.iframe-category').remove() ;" value="<?php echo osc_esc_html( __('Cancel') ) ; ?>" />
            </div>
        </fieldset>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.iframe-category form').submit(function() {
            $(".jsMessage").hide() ;
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                // Mostramos un mensaje con la respuesta de PHP
                success: function(data) {
                    var ret = eval( "(" + data + ")") ;
                    var message = "" ;
                    if( ret.error == 0 || ret.error == 4 ) {
                        $('.iframe-category').fadeOut('fast', function(){
                            $('.iframe-category').remove();
                        }) ;
                        $(".jsMessage p").attr('class', 'ok') ;
                        message += ret.msg ;
                        $('.iframe-category').parent().parent().find('.name').html(ret.text) ;
                    } else {
                        $(".jsMessage p").attr('class', 'error') ;
                        message += ret.msg ;
                    }

                    $(".jsMessage").fadeIn("fast") ;
                    $(".jsMessage p").html(message) ;
                    $('div.content_list_<?php echo osc_category_id() ; ?>').html('') ;
                },
                error: function(){
                    $(".jsMessage").fadeIn("fast") ;
                    $(".jsMessage p").attr('class', '') ;
                    $(".jsMessage p").html("<?php _e('Ajax error, try again.') ; ?>") ;
                }
            })
            return false ;
        });
    }) ;
    tabberAutomatic() ;
</script>