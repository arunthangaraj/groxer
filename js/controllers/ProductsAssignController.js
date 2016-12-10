GroxerAdminApp.controller('ProductsAssign_Controller', ['$scope', '$http','Upload','Notification','$state', function($scope, $http, Upload, Notification, $state){
	
	$('#product_assig').addClass("active");
    $('#das').removeClass("active");
    $('#cust').removeClass("active");
    $('#cat').removeClass("active");
    $('#product').removeClass("active");
    $('#area').removeClass("active");

    $http.post(GROXER_API_LINK_PRODUCTS+"?action=getCustomers",
		{

		})
		.success(function(res) {
			$scope.customers = res.customers;
			$scope.history = res.history;
		})
		.error(function(res) {

		});

		$scope.upload = function()
		{
			var upl = Upload.upload({
                url: GROXER_API_LINK_PRODUCTS+"?action=uploadcsv",
                data: {
                        prodCsv: $scope.csvFile, 
                        customer_id: $scope.store
                    }
            });

            upl.then(function(res) {

            	Notification.success("Products uploaded successfully");
            	$state.reload();
            },
            function(res) {

            	Notification.error("Products failed to upload");

            },
            function(event) {

            });
		}

}])