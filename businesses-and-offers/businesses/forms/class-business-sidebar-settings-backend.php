<?php

class WyzSettingsBusinessTabsForm_Builder {


    public function __construct() {
    
        $this->settings_page_init();
    }


  
    public function settings_page_init() {

        ?>
<h4><?php esc_html_e( 'You can manage the order of Business Sidebar that appear in single the business page. Click on any Form Field to start', 'wyzi-business-finder' );?></h4>
        <form method="POST"><input type="submit" value="Reset" class="wyz_reset_btn button-primary menu-save" name="business-sidebar-reset"/></form>
        <div id="nav-menus-frame" ng-app="wyzBusinessCustomFormFields">
            <div id="menu-settings-column" class="metabox-holder" ng-controller="postbox_menu">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="postboxClass">
                        <button ng-click="togglePostbox()" aria-expanded="false" class="handlediv button-link" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Format', 'wyzi-business-finder' );?></span><span aria-hidden="true" class="toggle-indicator"></span></button>
                        <h3 class="hndl ui-sortable-handle">
                            <span><?php esc_html_e( 'Sidebar Widgets', 'wyzi-business-finder' );?></span>
                        </h3>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('About', 'About', $event, '')" class="button-secondary"><?php esc_html_e('About','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('Opening_Hours', 'Opening Hours', $event, '')" class="button-secondary"><?php esc_html_e('Opening Hours','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('Contact_Info', 'Contact Info', $event, '')" class="button-secondary"><?php esc_html_e('Contact Info','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('Social_Media', 'Social Media', $event, '')" class="button-secondary"><?php esc_html_e('Social Media','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('Tags', 'Tags', $event, '')" class="button-secondary"><?php esc_html_e('Tags','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('Claim', 'Claim', $event, '')" class="button-secondary"><?php esc_html_e('Claim','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('Recent_Ratings', 'Recent Ratings', $event, '')" class="button-secondary"><?php esc_html_e('Recent Ratings','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('All_Ratings', 'All Ratings Categories', $event, '')" class="button-secondary"><?php esc_html_e('All Ratings Categories','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('Map', 'Map', $event, '')" class="button-secondary"><?php esc_html_e('Map','wyzi-business-finder'); ?></a>
                            </p>
                            
                            <?php do_action( 'wyz_additional_business_sidebar_dynamic');?>

                        </div>
                    </div>
                </div>
            </div>
            <div id="poststuff" ng-controller="postbox_content">
                <div id="post-body">
                    <div id="post-body-content">
                        <div id="wyzi-claim-form">
                            
                            <ul class="meta-box-sortables" ui-sortable="fieldSortableOptions" ng-model="fields">
                                <li ng-repeat="(parentIndex,field) in fields track by $index">
                                    <div class="postbox" ng-class="{'closed' : field.hidden }">
                                        <button aria-expanded="false" ng-click="togglePostboxField($index)" class="handlediv button-link" type="button"><span class="screen-reader-text">Toggle panel: Format</span><span aria-hidden="true" class="toggle-indicator"></span></button>
                                        <h2 class="hndle ui-sortable-handle" ng-dblclick="togglePostboxField($index)"><span>{{field.type}}</span></h2>
                                        <div class="inside">
                                            <div id="post-formats-select">
                                                <div ng-include src="getPartials(field.type)"></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}