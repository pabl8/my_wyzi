

var app = angular.module("wyzBusinessCustomFormFields", ['ui.sortable']);


app.service('wyzBusinessCustomFormFieldsService', function () {
    var formJson = [];
    if(wyziBuilderData.form_data !== ''){
        var formJson = wyziBuilderData.form_data;
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


var added;
app.controller('postbox_menu',['$scope', 'wyzBusinessCustomFormFieldsService', function ($scope, wyzBusinessCustomFormFieldsService) {
    $scope.postboxClass = "";
    var formJson = wyzBusinessCustomFormFieldsService.getField();
    $scope.addFormField = function (type, label, event) {
        if(type!='separator'&&added[type])
            return;
        added[type]=true;
        event.preventDefault();
        var jsonLength = formJson.length;
        if(type=='separator')
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                required: true,
                //active: false,
                //partial: wyziBuilderData.partials  + 'separator.html',
                cssClass: ''
            });
        else if(type=='category')
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                required: true,
                separateCategories: false,
                parentSelectable: false,
                //active: false,
                //partial: wyziBuilderData.partials  + 'category-element.html',
                cssClass: ''
            });
        else if(type=='time')
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                required: true,
                separateCategories: false,
                parentSelectable: false,
                openMsg: wyziBuilderData.openMsg,
                closedMsg: wyziBuilderData.closedMsg,
                //active: false,
                //partial: wyziBuilderData.partials  + 'time-element.html',
                cssClass: ''
            });
        /*else if(type=='gallery')
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                partial: wyziBuilderData.partials  + 'gallery-element.html',
                cssClass: ''
            });*/
        else if(type=='map')
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                //partial: wyziBuilderData.partials  + 'map-element.html',
                cssClass: ''
            });
        else
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                required: true,
                //active: false,
                //partial: wyziBuilderData.partials  + 'form-element.html',
                cssClass: ''
            });

        wyzBusinessCustomFormFieldsService.setField(formJson);
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
app.controller('postbox_content',['$scope', '$http', 'wyzBusinessCustomFormFieldsService', function ($scope, $http, wyzBusinessCustomFormFieldsService) {
    var formJson = wyzBusinessCustomFormFieldsService.getField();
    added = { name: false ,logo: false ,banner_image: false ,logoBg: false ,desc: false ,about: false, slogan: false ,
              category: false ,categoryIcon: false, time: false, bldg: false, street: false, city: false,
              location: false,range_radius: false,addAddress: false, zipcode: false,map: false,poi: false, phone1: false,phone2: false,email1: false,email2:false,
              gallery: false, website: false,fb: false,twitter: false,gplus: false,linkedin: false,youtube: false,insta: false,
              flicker: false,pinterest: false,comments: false,tags: false, custom:false };
    $scope.fields = formJson;
    $scope.showSaveSpinner = false;
    $scope.getPartials = function(type){
        switch(type){
            case 'separator':
                return wyziBuilderData.partials  + 'separator.html';
            case 'category':
                return wyziBuilderData.partials  + 'category-element.html';
            case 'time':
                return wyziBuilderData.partials  + 'time-element.html';
            case 'map':
                return wyziBuilderData.partials  + 'map-element.html';
        }
        return wyziBuilderData.partials  + 'form-element.html';
    }
    $scope.togglePostboxField = function (index) {
        if ($scope.fields[index].hidden) {
            $scope.fields[index].hidden = false;
        } else {
            $scope.fields[index].hidden = true;
        }
    };
    $scope.addToAdded = function(type){
        added[type]=true;
        return true;
    };
    $scope.removeFormField = function (type,index, event) {
        event.preventDefault();
        added[type]=false;
        formJson.splice(index, 1);
        wyzBusinessCustomFormFieldsService.setField(formJson);
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
            action: 'wyzi_business_form_builder_save_form',
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


