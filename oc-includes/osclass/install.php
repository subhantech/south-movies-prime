<?php
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

error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE);

define( 'ABS_PATH', dirname(dirname(dirname(__FILE__))) . '/' );
define( 'LIB_PATH', ABS_PATH . 'oc-includes/' ) ;
define( 'CONTENT_PATH', ABS_PATH . 'oc-content/' ) ;
define( 'TRANSLATIONS_PATH', CONTENT_PATH . 'languages/' ) ;
define( 'OSC_INSTALLING', 1 );

require_once LIB_PATH . 'osclass/Logger/Logger.php' ;
require_once LIB_PATH . 'osclass/Logger/LogDatabase.php' ;
require_once LIB_PATH . 'osclass/Logger/LogOsclass.php' ;
require_once LIB_PATH . 'osclass/classes/database/DBConnectionClass.php';
require_once LIB_PATH . 'osclass/classes/database/DBCommandClass.php';
require_once LIB_PATH . 'osclass/classes/database/DBRecordsetClass.php';
require_once LIB_PATH . 'osclass/classes/database/DAO.php';
require_once LIB_PATH . 'osclass/core/Session.php';
require_once LIB_PATH . 'osclass/core/Params.php';
require_once LIB_PATH . 'osclass/model/Preference.php';
require_once LIB_PATH . 'osclass/helpers/hDatabaseInfo.php';
require_once LIB_PATH . 'osclass/helpers/hDefines.php';
require_once LIB_PATH . 'osclass/helpers/hErrors.php';
require_once LIB_PATH . 'osclass/helpers/hLocale.php';
require_once LIB_PATH . 'osclass/helpers/hPreference.php';
require_once LIB_PATH . 'osclass/helpers/hSearch.php';
require_once LIB_PATH . 'osclass/helpers/hTranslations.php';
require_once LIB_PATH . 'osclass/helpers/hSanitize.php';
require_once LIB_PATH . 'osclass/default-constants.php';
require_once LIB_PATH . 'osclass/install-functions.php';
require_once LIB_PATH . 'osclass/utils.php';
require_once LIB_PATH . 'osclass/core/Translation.php';
require_once LIB_PATH . 'osclass/plugins.php';
require_once LIB_PATH . 'osclass/locales.php';


Session::newInstance()->session_start() ;

$locales = osc_listLocales();

if(Params::getParam('install_locale')!='') {
    Session::newInstance()->_set('userLocale', Params::getParam('install_locale')) ;
    Session::newInstance()->_set('adminLocale', Params::getParam('install_locale')) ;
}

if(Session::newInstance()->_get('adminLocale')!='' && key_exists(Session::newInstance()->_get('adminLocale'), $locales)) {
    $current_locale = Session::newInstance()->_get('adminLocale');
} else if(isset($locales['en_US'])) {
    $current_locale = 'en_US';
} else {
    $current_locale = key($locales);
}

Session::newInstance()->_set('userLocale', $current_locale);
Session::newInstance()->_set('adminLocale', $current_locale);


$translation = new Translation(true);



$step = Params::getParam('step');
if( !is_numeric($step) ) {
    $step = '1';
}

if( is_osclass_installed( ) ) {
    $message = __('You appear to have already installed OSClass. To reinstall please clear your old database tables first.');
    osc_die('OSClass &raquo; Error', $message) ;
}

switch( $step ) {
    case 1:
        $requirements = get_requirements() ;
        $error        = check_requirements($requirements) ;
        break;
    case 2:
        if( Params::getParam('save_stats') == '1'  || isset($_COOKIE['osclass_save_stats'])) {
            setcookie('osclass_save_stats', 1, time() + (24*60*60) );
        } else {
            setcookie('osclass_save_stats', 0, time() + (24*60*60) );
        }

        if( Params::getParam('ping_engines') == '1' || isset($_COOKIE['osclass_ping_engines']) ) {
            setcookie('osclass_ping_engines', 1, time() + (24*60*60) );
        } else {
            setcookie('osclass_ping_engines', 0, time()+ (24*60*60) );
        }

        break;
    case 3:
        if( Params::getParam('dbname') != '' ) {
            $error = oc_install();
        }
        break;
    case 4:
        if( Params::getParam('result') != '' ) {
            $error = Params::getParam('result');
        }
        $password = Params::getParam('password', false, false);
        break;
    case 5:
        $password = Params::getParam('password', false, false);
        break;
    default:
        break;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en-US">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php _e('OSClass Installation'); ?></title>
        <script src="<?php echo get_absolute_url(); ?>oc-includes/osclass/installer/jquery.js" type="text/javascript"></script>
        <script src="<?php echo get_absolute_url(); ?>oc-includes/osclass/installer/jquery-ui.js" type="text/javascript"></script>
        <script src="<?php echo get_absolute_url(); ?>oc-includes/osclass/installer/vtip/vtip.js" type="text/javascript"></script>
        <script src="<?php echo get_absolute_url(); ?>oc-includes/osclass/installer/jquery.jsonp.js" type="text/javascript"></script>
        <script src="<?php echo get_absolute_url(); ?>oc-includes/osclass/installer/install.js" type="text/javascript"></script>
        <script src="<?php echo get_absolute_url(); ?>oc-admin/themes/modern/js/location.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo get_absolute_url(); ?>oc-includes/osclass/installer/install.css" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo get_absolute_url(); ?>oc-includes/osclass/installer/vtip/css/vtip.css" />
    </head>
    <body>
        <div id="wrapper">
            <div id="container">
                <div id="header" class="installation">
                    <h1 id="logo">
                        <img src="<?php echo get_absolute_url(); ?>oc-includes/images/osclass-logo.png" alt="OSClass" title="OSClass"/>
                    </h1>
                    <?php if(in_array($step, array(2,3,4))) { ?>
                    <ul id="nav">
                        <li class="<?php if($step == 2) { ?>actual<?php } elseif($step < 2) { ?>next<?php } else { ?>past<?php }?>">1 - Database</li>
                        <li class="<?php if($step == 3) { ?>actual<?php } elseif($step < 3) { ?>next<?php } else { ?>past<?php }?>">2 - Target</li>
                        <li class="<?php if($step == 4) { ?>actual<?php } elseif($step < 4) { ?>next<?php } else { ?>past<?php }?>">3 - Categories</li>
                    </ul>
                    <div class="clear"></div>
                    <?php } ?>
                </div>
                <div id="content">
                <?php if($step == 1) { ?>
                    <h2 class="target"><?php _e('Welcome');?></h2>
                    <form action="install.php" method="POST">
                        <div class="form-table">
                            <?php if( count($locales) > 1 ) { ?>
                                <div>
                                    <label><?php _e('Choose language') ; ?></label>
                                    <select name="install_locale" id="install_locale" onChange="window.location.href='?install_locale='+document.getElementById(this.id).value">
                                        <?php foreach($locales as $k => $locale) {?>
                                        <option value="<?php echo $k ; ?>" <?php if( $k == $current_locale ) { echo 'selected="selected"' ; } ?>><?php echo $locale['name'] ; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>
                            <?php if($error) { ?>
                            <p><?php _e('Check the next requirements:');?></p>
                            <div class="requirements_help">
                                <p><b><?php _e('Requirements help:'); ?></b></p>
                                <ul>
                                <?php foreach($requirements as $k => $v) { ?>
                                    <?php  if(!$v['fn'] && $v['solution'] != ''){ ?>
                                    <li><?php echo $v['solution']; ?></li>
                                    <?php } ?>
                                <?php } ?>
                                    <li><a href="http://forums.osclass.org/"><?php _e('Need more help?');?></a></li>
                                </ul>
                            </div>
                            <?php } else { ?>
                            <p><?php _e('All right! All the requirements have met:');?></p>
                            <?php } ?>
                            <ul>
                            <?php foreach($requirements as $k => $v) { ?>
                                <li><?php echo $v['requirement']; ?> <img src="<?php echo get_absolute_url(); ?>oc-includes/images/<?php echo $v['fn'] ? 'tick.png' : 'cross.png'; ?>" alt="" title="" /></li>
                            <?php } ?>
                            </ul>
                            <div class="more-stats">
                                <input type="checkbox" name="ping_engines" id="ping_engines" checked="checked" value="1"/>
                                <label for="ping_engines">
                                    <?php _e('Allow my site to appear in search engines like Google.');?>
                                </label>
                                <br/>
                                <input type="checkbox" name="save_stats" id="save_stats" checked="checked" value="1"/>
                                <input type="hidden" name="step" value="2" />
                                <label for="save_stats">
                                    <?php _e('Help make OSClass better by automatically sending usage statistics and crash reports to OSClass.');?>
                                </label>
                            </div>
                        </div>
                        <?php if($error) { ?>
                        <p class="margin20">
                            <input type="button" class="button" onclick="document.location = 'install.php?step=1'" value="<?php _e('Try again');?>" />
                        </p>
                        <?php } else { ?>
                        <p class="margin20">
                            <input type="submit" class="button" value="<?php _e('Run the install');?>" />
                        </p>
                    <?php } ?>
                    </form>
                <?php } elseif($step == 2) {
                         display_database_config();
                    } elseif($step == 3) {
                        if( !isset($error["error"]) ) {
                            display_target();
                        } else {
                            display_database_error($error, ($step - 1));
                        }
                    } elseif($step == 4) {
                        display_categories($error, $password);
                    } elseif($step == 5) {
                        // ping engines
                        ping_search_engines( $_COOKIE['osclass_ping_engines'] ) ;
                        setcookie('osclass_save_stats', '', time() - 3600);
                        setcookie('osclass_ping_engines', '', time() - 3600);
                        display_finish($password);
                    }
                ?>
                </div>
                <div id="footer">
                    <ul>
                        <li>
                            <a href="<?php echo get_absolute_url(); ?>readme.php" target="_blank"><?php _e('Readme'); ?></a>
                        </li>
                        <li>
                            <a href="http://admin.osclass.org/feedback.php" target="_blank"><?php _e('Feedback'); ?></a>
                        </li>
                        <li>
                            <a href="http://forums.osclass.org/index.php" target="_blank"><?php _e('Forums');?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>
