GroxerAdminApp.controller('ProductsList_Controller', ['$scope','$http','Upload','Notification', function($scope, $http, Upload, Notification){

	$('#product_assig').removeClass("active");
    $('#das').removeClass("active");
    $('#cust').removeClass("active");
    $('#cat').removeClass("active");
    $('#product').addClass("active");
    $('#area').removeClass("active");

        $scope.products = [];
		$scope.product_sort = 'product_name';

		$http.post(GROXER_API_LINK_PRODUCTS+"?action=getProducts",
		{

		})
		.success(function(res) {
			$scope.products = res.products;
			$scope.productsCount = $scope.products.length;
            $scope.getArray = res.products;
		})
		.error(function(res) {

		});

		$scope.itemsPerPage = 10;
        $scope.currentPage = 0;
        $scope.range = function ()
        {
            var rangeSize = 3;
            var ps = [];
            var start;
            start = $scope.currentPage;
            //  console.log($scope.pageCount(),$scope.currentPage)
            if (start > $scope.pageCount() - rangeSize)
            {
                start = $scope.pageCount() - rangeSize + 1;
            }
            for (var i = start; i < start + rangeSize; i++)
            {
                if (i >= 0)
                {
                    ps.push(i);
                }
            }
            return ps;
        };
        //previous button code
        $scope.prevPage = function ()
        {
            if ($scope.currentPage > 0)
            {
                $scope.currentPage--;
            }
        };
        //disable previous buuton
        $scope.DisablePrevPage = function ()
        {
            return $scope.currentPage === 0 ? "disabled" : "";
        };
        //page count code
        $scope.pageCount = function ()
        {
            return Math.ceil($scope.products.length / $scope.itemsPerPage) - 1;
        };
        //next button code
        $scope.nextPage = function ()
        {
            if ($scope.currentPage != $scope.pageCount())
            {
                $scope.currentPage++;
            }
        };
        //disable next button code
        $scope.DisableNextPage = function ()
        {
            return $scope.currentPage === $scope.pageCount() ? "disabled" : "";
        };
        //current page 
        $scope.setPage = function (n)
        {
            $scope.currentPage = n;
        };

        $scope.removeProduct= function(product)
        {
            $http.post(GROXER_API_LINK_PRODUCTS+"?action=removeProduct",
            {
                product_id: product.product_id
            })
            .success(function(res) {
                $scope.products.splice($scope.products.indexOf(product),1);
                $scope.productsCount = $scope.products.length;

                Notification.success("Product removed successfully");
            })
            .error(function(res) {

            });
        }
}]);