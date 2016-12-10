GroxerAdminApp.controller('CategoryListing_Controller', function($scope,$location,$http,$window,Notification,$anchorScroll,$modal,$window) {
    
$scope.categories={};

$scope.viewcategory=function()
{
    $http.post(GROXER_API_LINK+'?action=viewCategory')
               .success(function(data)
                { if(data)
                    {
                       
                       $scope.categories = data.categories;
                        $scope.daily_count = $scope.categories.length;
                   
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
        return Math.ceil($scope.categories.length/$scope.itemsPerPage)-1;
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
$scope.deleteCategory = function(cid,ccount)
{    if(ccount==0)
        {
             $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/delete_city.html',
                                controller: function($scope, $modalInstance, $modal, $http, $state) 
                                {
                                    
                                    $scope.ok =  function()
                                    {
                                            $http.post(GROXER_API_LINK+'?action=deleteCategory',{id:cid})
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            $modalInstance.dismiss('cancel');
                                            Notification.success("Removed Sucessfully");
                                            $state.reload();
                                            

                                        }
                                        
                                        else
                                        {
                                            Notification.error(res.message);
                                         }
                                        })
                                        .error(function(data)
                                        {
                                            Notification.error("Check your internet connection");
                                        });
                                    }
                                    
                                     $scope.cancel = function () {
                                         $modalInstance.dismiss('cancel');
                                     };
                                },
                                resolve: {}
                            };
                            
                            $modal.open($scope.opts);
        }
        else
        {
           $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/delete_category.html',
                                controller: function($scope, $modalInstance, $modal, $http, $state) 
                                {
                                    
                                    $scope.ok =  function()
                                    {
                                           $modalInstance.dismiss('cancel');
                                    }
                                },
                                resolve: {}

                            
                            };
                            $modal.open($scope.opts); 
        }
                        
    }


 $scope.addcategory=function()
 {
     $scope.opts = {

                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/add_category.html',
                                controller: function($scope, $modalInstance, $modal, $http, $state) 
                                {
                                    
                                    $scope.insertcategory =  function()
                                    {
                                        
                                            $http.post(GROXER_API_LINK+'?action=insertCategory',{category_name:$scope.category_name})
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            $modalInstance.dismiss('cancel');
                                            Notification.success("Inserted Sucessfully");
                                            $state.reload();

                                        }
                             
                                        else
                                        {
                                            Notification.error(res.message);
                                         }
                                        })
                                        .error(function(data)
                                        {
                                            Notification.error("Check your internet connection");
                                        });
                                    }
                                    
                                     $scope.cancel = function () {
                                         $modalInstance.dismiss('cancel');
                                     };
                                },
                                resolve: {}
                            };
                            
                            $modal.open($scope.opts);            

 }   

})