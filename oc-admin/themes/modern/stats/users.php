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

    $users            = __get("users") ;
    $max              = __get("max") ;
    $item             = __get("item") ;
    $users_by_country = __get("users_by_country") ;
    $users_by_region  = __get("users_by_region") ;
    $latest_users     = __get("latest_users") ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()) ; ?>">
    <head>
        <?php osc_current_admin_theme_path('head.php') ; ?>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <?php if(count($users)>0) { ?>
        <script type="text/javascript">
            // Load the Visualization API and the piechart package.
            google.load('visualization', '1', {'packages':['corechart']}) ;

            // Set a callback to run when the Google Visualization API is loaded.
            google.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table, 
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {
                var data = new google.visualization.DataTable();

                data.addColumn('string', '<?php _e('Date') ; ?>',0,1);
                data.addColumn('number', '<?php _e('New users') ; ?>');
                <?php $k = 0 ;
                echo "data.addRows(" . count($users) . ");" ;
                foreach($users as $date => $num) {
                    echo "data.setValue(" . $k . ', 0, "'. $date . '");' ;
                    echo "data.setValue(" . $k . ", 1, " . $num . ");" ;
                    $k++ ;
                }
                ?>

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.AreaChart(document.getElementById('placeholder'));
                chart.draw(data, {width: 400, height: 240, vAxis: {maxValue: <?php echo ceil($max * 1.1) ; ?>},legend: {position: 'none'},pointSize: 5});

                var data_country = new google.visualization.DataTable();
                data_country.addColumn('string', '<?php _e('Country') ; ?>');
                data_country.addColumn('number', '<?php _e('Users per country') ; ?>');
                data_country.addRows(<?php echo count($users_by_country) ; ?>);
                <?php foreach($users_by_country as $k => $v) {
                    echo "data_country.setValue(" . $k . ", 0, '" . ( ( $v['s_country'] == NULL ) ? __('Unknown') : $v['s_country'] ) . "');" ;
                    echo "data_country.setValue(" . $k . ", 1, " . $v['num'] . ");" ;
                } ?>

                // Create and draw the visualization.
                new google.visualization.PieChart(document.getElementById('by_country')).draw(data_country, {title:"<?php _e('Users per country') ; ?>"});
                
                var data_region = new google.visualization.DataTable();
                data_region.addColumn('string', '<?php _e('Region') ; ?>');
                data_region.addColumn('number', '<?php _e('Users per region') ; ?>');
                data_region.addRows(<?php echo count($users_by_region) ; ?>);
                <?php foreach($users_by_region as $k => $v) {
                    echo "data_region.setValue(" . $k . ", 0, '" . ( ( $v['s_region'] == NULL ) ? __('Unknown') : $v['s_region'] ) . "');" ;
                    echo "data_region.setValue(" . $k . ", 1, " . $v['num'] . ");" ;
                } ?>

                // Create and draw the visualization.
                new google.visualization.PieChart(document.getElementById('by_region')).draw(data_region, {title:"<?php _e('Users per region') ; ?>"});
            }
            </script>
        <?php } ?>
    </head>
    <body>
        <?php osc_current_admin_theme_path('header.php') ; ?>
        <!-- container -->
        <div id="content">
            <?php osc_current_admin_theme_path ( 'include/backoffice_menu.php' ) ; ?>
            <!-- right container -->
            <div class="right">
                <div class="header_title">
                    <h1 class="settings"><?php _e('Users Statistics') ; ?></h1>
                </div>
                <?php osc_show_flash_message('admin') ; ?>
                <!-- users statistics -->
                <div class="statistics">
                    <div class="actions-header">
                        <a href="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=users&amp;type_stat=day"><?php _e('Last 10 days') ; ?></a>
                        <a href="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=users&amp;type_stat=week"><?php _e('Last 10 weeks') ; ?></a>
                        <a href="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=users&amp;type_stat=month"><?php _e('Last 10 months') ; ?></a>
                    </div>

                    <div class="sortable_div">
                        <div class="float50per">
                        <div class="latest-items ui-dialog ui-corner-all">
                            <h3 class="ui-dialog-titlebar"><?php _e('New users'); ?></h3>
                            <div class="ui-state-body">
                                <div id="placeholder" style="width:400px;height:300px;margin:0; margin:0 auto; padding-bottom: 45px;">
                                    <?php if( count($users) == 0 ) {
                                        _e("There're no statistics yet") ;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="float50per">
                            <div class="latest-items ui-dialog ui-corner-all">
                                    <h3 class="ui-dialog-titlebar"><?php _e('Users per country') ; ?></h3>
                                    <div class="ui-state-body">
                                    <div id="by_country" style="width:400px;height:300px;margin:0 auto;padding-bottom: 45px;">
                                <?php if( count($users_by_country) == 0 ) {
                                    _e("There're no statistics yet") ;
                                }
                                ?>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="float50per">
                            <div class="latest-items ui-dialog ui-corner-all">
                                <h3 class="ui-dialog-titlebar"><?php _e('Users per region') ; ?></h3>
                                <div class="ui-state-body">
                                <div id="by_region" style="width:400px;height:300px;margin:0 auto;padding-bottom: 45px;">
                                    <?php if( count($users_by_region) == 0 ) {
                                        _e("There're no statistics yet") ;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="float50per">
                            <div class="latest-items ui-dialog ui-corner-all">
                                <h3 class="ui-dialog-titlebar"><?php _e('Latest users on the web') ; ?></h3>
                                <div class="ui-state-body">
                                <?php if( count($latest_users) > 0 ) { ?>
                        <table border="0">
                            <tr>
                                <th>ID</th>
                                <th><?php _e('E-Mail') ; ?></th>
                                <th><?php _e('Name') ; ?></th>
                            </tr>
                            <?php foreach($latest_users as $u) { ?>
                            <tr>
                                <td><a href="<?php echo osc_admin_base_url(true); ?>?page=users&amp;action=edit&amp;id=<?php echo $u['pk_i_id'] ; ?>"><?php echo $u['pk_i_id'] ; ?></a></td>
                                <td><a href="<?php echo osc_admin_base_url(true); ?>?page=users&amp;action=edit&amp;id=<?php echo $u['pk_i_id'] ; ?>"><?php echo $u['s_email'] ; ?></a></td>
                                <td><a href="<?php echo osc_admin_base_url(true); ?>?page=users&amp;action=edit&amp;id=<?php echo $u['pk_i_id'] ; ?>"><?php echo $u['s_name'] ; ?></a></td>
                            </tr>
                            <?php } ?>
                        </table>
                        <?php } else { ?>
                            <p><?php _e("There're no statistics yet") ; ?></p>
                        <?php } ?>
                            </div>
                        </div>
                        </div>

                        <div class="float50per">
                            <div class="latest-items ui-dialog ui-corner-all">
                                <h3 class="ui-dialog-titlebar"><?php _e('Avg. items per user') ; ?></h3>
                                <div class="ui-state-body">
                                <?php printf( __('%s items per user'), number_format($item, 2) ) ; ?>
                            </div>
                        </div>
                        </div>

                        <div class="clear"></div>
                    </div>

                </div>
                <!-- /users statistics -->
                <div class="clear"></div>
            </div>
            <!-- /right container -->
        </div>
        <!-- /container -->
        <?php osc_current_admin_theme_path('footer.php') ; ?>
    </body>
</html>