GroxerAdminApp.controller('Overview_Controller', function($scope,$location,$http,$window,Notification,$anchorScroll) {
    $scope.data={};
    $scope.currency=localStorage['currency'];
    $scope.data.loadingFirst=true;
    $scope.showTopSell=false;
    $scope.showMerchant=false;
    $scope.merchants;
    $scope.orders={};
    $http.post(GROXER_API_LINK+'?action=overallDetails')
               .success(function(data)
                { if(data)
                    {
                    $scope.data.totalproduct=data.totalProduct;
                    $scope.data.productprogress=(data.progressTotal).toFixed(2);
                    $scope.data.totalorder=data.totalOrder;
                    $scope.data.orderprogress=(data.progressOrder).toFixed(2);
                    $scope.data.grandtotal=data.grandTotal;
                    $scope.data.progresstotal=(data.progresspre).toFixed(2);
                    $scope.data.loadingFirst=false;
                   
                }
                })
                    .error(function(data){
                       Notification.error( 'Check your internet connection'); 
                });

        $scope.topselling = function()
        {
           $http.post(GROXER_API_LINK+'?action=topSell')
               .success(function(data)
                { 
                    if(data.sells.length>0)
                    {
                        
                    $scope.sells=data.sells;
                    }
                    else
                    {
                        $scope.showTopSell=true;
                    }
                })
                    .error(function(data){
                       Notification.error('Check your internet connection'); 
                });
        }

        $scope.merchantAction = function()
        {
             var value,index;
           $http.post(GROXER_API_LINK+'?action=merchantAction')
               .success(function(data)
                { 
                    if(data.merchants.length>0)
                    {
                        
                    $scope.merchants=data.merchants;
                    $scope.completedOrders=data.completedOrders;
                    $scope.clength=$scope.completedOrders.length;
                   
                    $scope.pendingOrders=data.pendingOrders;
                    }
                    else
                    {
                        $scope.showMerchant=true;
                    }
                })
                    .error(function(data){
                       Notification.error('Check your internet connection'); 
                });
        }

        $scope.vieworders = function()
        {
            $http.post(GROXER_API_LINK+'?action=vieworders')
               .success(function(data)
                { if(data)
                    {
                       
                       $scope.orders = data.orders;
                        $scope.daily_count = $scope.orders.length;
                   
                    }
                })
                    .error(function(data){
                       Notification.error( 'Check your internet connection'); 
                });
        }

$scope.itemsPerPage = 10;
    $scope.currentPage = 0;
    $scope.range = function() 
    {
        var rangeSize = 3;
        var ps = [];
        var start;
        start = $scope.currentPage;
        //  console.log($scope.pageCount(),$scope.currentPage)
        if ( start > $scope.pageCount()-rangeSize ) 
        {
            start = $scope.pageCount()-rangeSize+1;
        }
        for (var i=start; i<start+rangeSize; i++) 
        {
            if(i>=0)
            { 
                ps.push(i);
            }
        }
        return ps;
    };
    //previous button code
    $scope.prevPage = function() 
    {
        if ($scope.currentPage > 0) 
        {
            $scope.currentPage--;
        }
    };
    //disable previous buuton
    $scope.DisablePrevPage = function() 
    {
        return $scope.currentPage === 0 ? "disabled" : "";
    };
    //page count code
    $scope.pageCount = function() 
    {
        return Math.ceil($scope.orders.length/$scope.itemsPerPage)-1;
    };
    //next button code
    $scope.nextPage = function() 
    {
        if ($scope.currentPage != $scope.pageCount()) 
        {
            $scope.currentPage++;
        }
    };
    //disable next button code
    $scope.DisableNextPage = function() 
    {
        return $scope.currentPage === $scope.pageCount() ? "disabled" : "";
    };
    //current page 
    $scope.setPage = function(n) 
    {
        $scope.currentPage = n;
    };
})