'use strict';

app.controller('DemoCtrl', function (DemoService, $scope){

    $scope.eslip = {};

    $scope.logout = function(){

        var logoutPromise = DemoService.logout();

        logoutPromise.then(function(data){
            $scope.eslip = {};
        });
    };

    function init(){
        var eslipDataPromise = DemoService.getEslipData();

        eslipDataPromise.then(function(data){
            $scope.eslip = data.eslip;
        });

        // iniciar el ESLIP Plugin
        ESLIP.init();
    }

    // init controller
    init();

});
