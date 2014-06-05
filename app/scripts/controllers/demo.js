'use strict';

app.controller('DemoCtrl', function (DemoService, $scope){

    $scope.user;

    var userPromise = DemoService.getUser();

    userPromise.then(function(data){
        $scope.user = data;
    });

    // iniciar el ESLIP Plugin
    ESLIP.init();

});
