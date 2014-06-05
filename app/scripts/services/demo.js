'use strict';

app.service('DemoService', function ($resource){

    var UserService = $resource('/dist/user.php');

    this.getUser = function(){

        return UserService.get().$promise;
        
    };

    return this;
});