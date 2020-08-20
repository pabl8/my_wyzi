

var app = angular.module("wyzBusinessCustomFormFields", ['ui.sortable']);


app.service('wyzBusinessCustomFormFieldsService', function () {
    var formJson = [];
    if(wyziSideBarData.form_data !== ''){
        var formJson = wyziSideBarData.form_data;
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
    $scope.addFormField = function (type, title, event) {
        if(added[type] && type!='additionalContent')
            return;
        added[type]=true;
        event.preventDefault();
        var jsonLength = formJson.length;
        formJson.push({
            id: jsonLength,
            type: type,
            title: title,
            //urlid: type.toLowerCase(),
            //additionalContent: additionalContent,
            //active: false,
            //partial: partial,
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
    added = { About: false ,Opening_Hours: false ,Contact_Info: false ,Social_Media: false ,Tags: false, Claim: false, Recent_Ratings: false , All_Ratings: false, Map: false };
    $scope.fields = formJson;
    $scope.showSaveSpinner = false;
    $scope.getPartials = function(type){
        var partial = wyziSideBarData.partials;

            partial +=  'sidebar.html';

        return partial;
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
            action: 'wyzi_business_sidebar_save_form',
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


