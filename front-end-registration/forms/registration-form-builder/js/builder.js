

var app = angular.module("wyzBusinessRegistrationFormFields", ['ui.sortable']);


app.service('wyzBusinessRegistrationFormFieldsService', function () {
    var formJson = [];
    if(wyziRegistrationFormData.form_data !== ''){
        var formJson = wyziRegistrationFormData.form_data;
    }
    return {
        getField: function () {
            return formJson;
        },
        setField: function (value) {
            formJson = value;
        }
    };
});


var oneTimeTypes = ['username','pemail','fname','lname','password','subscription', 'recaptcha','billing_company','billing_country','billing_address_1','billing_address_2',
                    'billing_city','billing_postcode','billing_state','billing_phone' ];

function add_Type(type){
    var l = oneTimeTypes.length;
    for(var i=0;i<l;i++)
        if(type==oneTimeTypes[i]&&(added[type]=true))return;
}

function remove_Type(type){
    var l = oneTimeTypes.length;
    for(var i=0;i<l;i++)
        if(type==oneTimeTypes[i]&&!(added[type]=false))return;
}

function is_onetime_add(type){
    var l = oneTimeTypes.length;
    for(var i=0;i<l;i++)
        if(type==oneTimeTypes[i])return true;
    return false;
}
function canAdd(type){
    return !is_onetime_add(type)||!added[type];
}

var added;
app.controller('postbox_menu',['$scope', 'wyzBusinessRegistrationFormFieldsService', function ($scope, wyzBusinessRegistrationFormFieldsService) {
    $scope.postboxClass = "";
    var formJson = wyzBusinessRegistrationFormFieldsService.getField();
    $scope.addFormField = function (type, label, event) {
        if(!canAdd(type))
            return;
        add_Type(type);
        event.preventDefault();
        var jsonLength = formJson.length;
        if('password'==type)
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                passaglabel: 'password again',
                hideRepPass: false,
                required: true,
                //partial: wyziRegistrationFormData.partials  + 'form-element-password.html',
                cssClass: ''
            });
         else if(is_onetime_add(type)){
            if('recaptcha'==type)
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    //partial: wyziRegistrationFormData.partials + 'recaptcha.html',
                    recaptchaSiteKey:'',
                    recaptchaSecretKey:''
                });
            else
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    required: false,
                    //partial: wyziRegistrationFormData.partials  + 'form-element-once.html',
                    cssClass: ''
                });
        }
        else
        switch (type) {
            case 'selectbox':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    selecttype: 'radio',
                    label: label,
                    hidden: false,
                    //partial: wyziRegistrationFormData.partials + type + '.html',
                    required: false,
                    visible: true,
                    options: [
                        {
                            value: 'option1',
                            label: 'Option 1',
                            selected: false
                        },
                        {
                            value: 'option2',
                            label: 'Option 2',
                            selected: true
                        },
                    ],
                    cssClass: ''
                });
                break;
            case 'email':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    //partial: wyziRegistrationFormData.partials + type + '.html',
                    placeholder: '',
                    required: false,
                    visible: true,
                    cssClass: ''
                });
                break;
            case 'number':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    //partial: wyziRegistrationFormData.partials + type + '.html',
                    placeholder: '',
                    required: false,
                    positiveOnly: false,
                    visible: true,
                    cssClass: ''
                });
                break;
            case 'textarea':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    //partial: wyziRegistrationFormData.partials + type + '.html',
                    defaultValue: '',
                    limit : '',
                    required: false,
                    visible: true,
                    cssClass: ''
                });
                break;
            case 'wysiwyg':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    //partial: wyziRegistrationFormData.partials + type + '.html',
                    required: false,
                    visible: true,
                    mediaupload: true,
                    cssClass: ''
                });
                break;
            default :
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    //partial: wyziRegistrationFormData.partials + type + '.html',
                    placeholder: '',
                    required: false,
                    visible: true,
                    cssClass: ''
                });
                break;
        }

        wyzBusinessRegistrationFormFieldsService.setField(formJson);
    };
    $scope.togglePostbox = function () {
        if ($scope.postboxClass === "") {
            $scope.postboxClass = "closed";
        } else {
            $scope.postboxClass = "";
        }
    };
}]);
/*
 * angular controller for form fields
 */
app.controller('postbox_content',['$scope', '$http', 'wyzBusinessRegistrationFormFieldsService', function ($scope, $http, wyzBusinessRegistrationFormFieldsService) {
    var formJson = wyzBusinessRegistrationFormFieldsService.getField();
    added = { username:false,pemail:false,fname:false,lname:false,password:false, recaptcha:false,billing_company:false,
            billing_country:false,billing_address_1:false,billing_address_2:false,billing_city:false,billing_postcode:false,
            billing_state:false,billing_phone:false};
    $scope.fields = formJson;
    $scope.getPartials = function(type){
        console.log(type);
        if(is_onetime_add(type)){
            if(type=='recaptcha')
                return wyziRegistrationFormData.partials + 'recaptcha.html';
            return wyziRegistrationFormData.partials  + 'form-element-once.html';
        }
        if(type=='password')
            return wyziRegistrationFormData.partials  + 'form-element-password.html';
        return wyziRegistrationFormData.partials + type + '.html';
    }
    $scope.showSaveSpinner = false;
    $scope.togglePostboxField = function (index) {
        if ($scope.fields[index].hidden) {
            $scope.fields[index].hidden = false;
        } else {
            $scope.fields[index].hidden = true;
        }
    };
    $scope.addToAdded = function(type){
        add_Type(type);
        return true;
    };
    $scope.removeFormField = function (type,index, event) {
        if(event)event.preventDefault();
        remove_Type(type);
        formJson.splice(index, 1);
        wyzBusinessRegistrationFormFieldsService.setField(formJson);
    };
    $scope.addSelectBoxOption = function (index, event) {
        event.preventDefault();
        var count = $scope.fields[index].options.length + 1;
        $scope.fields[index].options.push({value: 'option' + count, label: 'Option ' + count, selected: false});
    };
    $scope.removeSelectboxOption = function (index, key, event) {
        event.preventDefault();
        $scope.fields[index].options.splice(key, 1);
    };

    $scope.fieldSortableOptions = {
        stop: function (e, ui) {

        }
    };
    $scope.listOnchange = function (index){
        angular.forEach($scope.fields,function(value,key){
            if(key != index){
                $scope.fields[key].active=false;
            }
        });
        $scope.fields[index].active=true;
    };
    

   $scope.saveFormData = function () {
        $scope.showSaveSpinner = true;
        var data = jQuery.param({
            action: 'wyzi_registration_form_builder_save_form',
            form_data: JSON.stringify($scope.fields)
        });
        
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        $http.post(ajaxurl,data,config).success(function (data, status, headers, config){
            $scope.showSaveSpinner = false;
        }).error(function (data, status, header, config){
            $scope.showSaveSpinner = false;
        });  
    };
}]);


