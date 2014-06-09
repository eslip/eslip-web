'use strict';

app.service('DemoService', function ($resource){

    var UserService = $resource('/user.php');
    var LogoutService = $resource('/logout.php');

    this.getUser = function(){

        return UserService.get().$promise;
        
    };

    this.logout = function(){

        return LogoutService.save().$promise;
        
    };

    return this;
});