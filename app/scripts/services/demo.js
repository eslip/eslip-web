'use strict';

app.service('DemoService', function ($resource){

    var EslipService = $resource('/user.php');
    var LogoutService = $resource('/logout.php');

    this.getEslipData = function(){

        return EslipService.get().$promise;
        
    };

    this.logout = function(){

        return LogoutService.save().$promise;
        
    };

    return this;
});