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
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo osc_page_title() ; ?> &raquo; <?php _e('Lost your password') ; ?></title>
        <script type="text/javascript" src="<?php echo osc_admin_base_url() ; ?>themes/modern/js/jquery.js"></script>
        <link type="text/css" href="style/backoffice_login.css" media="screen" rel="stylesheet" />
    </head>

    <body class="recover">
        <div id="login">
            <h1>
                <a href="<?php echo osc_base_url() ; ?>" title="OSClass">
                    <img src="images/osclass-logo.gif" border="0" title="" alt=""/>
                </a>
            </h1>
            <?php osc_show_flash_message('admin') ; ?>
            <div class="message warning">
                <?php _e('Please enter your username or e-mail address') ; ?>.<br />
                <?php _e('You will receive a new password via e-mail') ; ?>.
            </div>

            <form action="<?php echo osc_admin_base_url(true) ; ?>" method="post">
                <input type="hidden" name="page" value="login" />
                <input type="hidden" name="action" value="recover_post" />
                <p>
                    <label><span><?php _e('E-mail') ; ?></span>
                    <input type="text" name="email" id="user_email" class="input" value="" size="20" tabindex="10" /></label>
                </p>
                <?php osc_show_recaptcha(); ?>
                <p class="submit"><input type="submit" name="submit" id="submit" value="<?php _e('Get new password') ; ?>" tabindex="100" /></p>
            </form>
            <p id="nav">
                <a title="<?php _e('Log in') ; ?>" href="<?php echo osc_admin_base_url() ; ?>"><?php _e('Log in') ; ?></a>
            </p>
        </div>
        <p id="backtoblog"><a href="<?php echo osc_base_url() ; ?>" title="<?php printf( __('Back to %s'), osc_page_title() ) ; ?>">&larr; <?php printf( __('Back to %s'), osc_page_title() ) ; ?></a></p>
        <script type="text/javascript">
            $(document).ready(function(){
                $('#user_email').focus(function(){
                        $(this).prev().hide();
                }).blur(function(){
                    if($(this).val() == '') {
                        $(this).prev().show();
                    }
                }).prev().click(function(){
                        $(this).hide();
                });
            });
        </script>
    </body>
</html>