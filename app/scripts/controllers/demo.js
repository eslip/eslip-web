'use strict';

app.controller('DemoCtrl', function (DemoService, $scope){

    $scope.user;

    $scope.logout = function(){

        var logoutPromise = DemoService.logout();

        logoutPromise.then(function(data){
            $scope.user = '';
        });
    };

    function init(){
        var userPromise = DemoService.getUser();

        userPromise.then(function(data){
            $scope.user = data.user;
        });

        // iniciar el ESLIP Plugin
        ESLIP.init();
    }

    // init controller
    init();

});
