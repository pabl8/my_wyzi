<?php

class WyzRegistrationForm_Builder {


    public function __construct() {
    
        $this->settings_page_init();
    }


  
    public function settings_page_init() {
        ?>
<h4><?php esc_html_e( 'You can manage the order of Registration form fields that appear in registration page. Click on any Form Field to start', 'wyzi-business-finder' );?></h4>
        <form method="POST"><input type="submit" value="Reset" class="wyz_reset_btn button-primary menu-save" name="reg-form-builder-reset"/></form>
        <div id="nav-menus-frame" ng-app="wyzBusinessRegistrationFormFields">
            <div id="menu-settings-column" class="metabox-holder" ng-controller="postbox_menu">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="postboxClass">
                        <button ng-click="togglePostbox()" aria-expanded="false" class="handlediv button-link" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Format', 'wyzi-business-finder' );?></span><span aria-hidden="true" class="toggle-indicator"></span></button>
                        <h3 class="hndl ui-sortable-handle">
                            <span><?php esc_html_e( 'Form Fields', 'wyzi-business-finder' );?></span>
                        </h3>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('fname', 'First Name', $event)" class="button-secondary">First Name</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('lname', 'Last Name', $event)" class="button-secondary">Last Name</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('subscription', 'Subscription', $event)" class="button-secondary">subscription</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('recaptcha', 'Recaptcha', $event)" class="button-secondary">Recaptcha</a>
                            </p>

                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('text', 'Text Box', $event)" class="button-secondary">Text Box</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('number', 'Number', $event)" class="button-secondary">Number</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('email', 'Email', $event)" class="button-secondary">Email</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('date', 'Date', $event)" class="button-secondary">Date</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('url', 'Url', $event)" class="button-secondary">URL</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('file', 'Attachment', $event)" class="button-secondary">Attachment</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textarea', 'Text Area', $event)" class="button-secondary">Text Area</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('wysiwyg', 'Wysiwyg', $event)" class="button-secondary">Wysiwyg</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('selectbox', 'List', $event)" class="button-secondary">List</a>
                            </p>
                            <hr>
                            <p>Woocommerce Fields</p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_company', 'Billing Company', $event)" class="button-secondary">Billing Company</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_country', 'Billing Country', $event)" class="button-secondary">Billing Country</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_address_1', 'Billing Address 1', $event)" class="button-secondary">Billing Address 1</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_address_2', 'Billing Address 2', $event)" class="button-secondary">Billing Address 2</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_city', 'Billing City', $event)" class="button-secondary">Billing City</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_postcode', 'Billing Postcode', $event)" class="button-secondary">Billing Postcode</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_state', 'Billing State', $event)" class="button-secondary">Billing State</a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('billing_phone', 'Billing Phone', $event)" class="button-secondary">Billing Phone</a>
                            </p>
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
                                        <h2 class="hndle ui-sortable-handle" ng-dblclick="togglePostboxField($index)"><span>{{field.label}}</span></h2>
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