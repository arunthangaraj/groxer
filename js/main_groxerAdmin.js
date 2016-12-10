var GroxerAdminApp = angular.module('GroxerAdminApp', [

		"ui-notification",
		"ngFileUpload",
		'ngSanitize',
		"ngCsv"
	]);

GroxerAdminApp.filter('pagination', function() {
return function(input, start) {
if(input) {
start = +start; //parse to int
return input.slice(start);
}
return [];
}
});

GroxerAdminApp.config(['$stateProvider','$urlRouterProvider',function($stateProvider, $urlRouterProvider) 
{
		$urlRouterProvider.otherwise("/signIn");

		$stateProvider

		.state('signIn', {
			url: "/signIn",
			templateUrl: "views/signIn.html",
			data: {pageTitle: 'Sign In'},
			controller: "SignIn_Controller"
		})

		.state('overview', {
			url: "/overview",
			templateUrl: "views/overView.html",
			data: {pageTitle: 'Overview'},
			controller: "Overview_Controller"
		})

         .state('customer', {
			url: "/customer",
            templateUrl: "views/customer.html",
            data: {pageTitle: 'Customers'},
            controller: "ViewCustomer_Controller"
		})

         .state('citylisting', {
			url: "/citylisting",
            templateUrl: "views/city_listing.html",
            data: {pageTitle: 'City'},
            controller: "CityListing_Controller"
		})

		.state('changePassword', {
			url: "/changePassword",
            templateUrl: "views/changePassword.html",
            data: {pageTitle: 'Change Password'},
            controller: "ChangePassword_Controller"
		})

		.state('resetPassword', {
			url: "/resetPassword",
            templateUrl: "views/resetPassword.html",
            data: {pageTitle: 'Reset Password'},
            controller: "ResetPassword_Controller"
		})

		.state('resetPasswordAuth', {
			url: "/resetPasswordAuth/:auth",
            templateUrl: "views/resetPasswordAuth.html",
            data: {pageTitle: 'Reset Password'},
            controller: "ResetPasswordAuth_Controller"
		})

		.state('categorylisting', {
			url: "/categorylisting",
            templateUrl: "views/category_listing.html",
            data: {pageTitle: 'Categorylisting'},
            controller: "CategoryListing_Controller"
		})
                
                 .state('subcategorylisting', {
			url: "/subcategorylisting",
            templateUrl: "views/subcategorylisting.html",
            data: {pageTitle: 'Categorylisting'},
            controller: "SubCategoryListing_Controller"
		})

		.state('arealisting', {
			url: "/arealisting",
            templateUrl: "views/area_listing.html",
            data: {pageTitle: 'Area'},
            controller: "AreaListing_Controller"
		})

		//ProductsState
		.state('productsList', {
			url: "/products",
			templateUrl: "views/productsList.html",
			data: {pageTitle: 'Products'},
			controller: "ProductsList_Controller"
		})

		.state('productsModify', {
			url: "/products/:id/:remove",
			templateUrl: "views/productsModify.html",
			data: {pageTitle: 'Products'},
			controller: "ProductsModify_Controller"
		})

		.state('productsAssign', {
			url: "/productAssign",
			templateUrl: "views/productsAssign.html",
			data: {pageTitle: 'Products Assign'},
			controller: "ProductsAssign_Controller"
		})
			;
}]);

GROXER_API_LINK = "groxerAdminApi/groxerAdminAPI.php";

GROXER_API_LINK_PRODUCTS = "groxerAdminApi/groxerAdminAPI_Products.php";