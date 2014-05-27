'use strict';

var app = angular.module('eslipApp', [
    'ngCookies',
    'ngResource',
    'ngSanitize',
    'ngRoute'
]);

app.config(function ($routeProvider){
    $routeProvider
    .when('/home', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl'
    })
    
    .when('/documentation', {
        templateUrl: 'views/documentation.html',
        controller: 'DocumentationCtrl'
    })
    .when('/support', {
        templateUrl: 'views/support.html',
        controller: 'SupportCtrl'
    })
    .otherwise({
        redirectTo: '/home'
    });
});
