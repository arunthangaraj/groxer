GroxerAdminApp.controller('SubCategoryListing_Controller', function($scope,$location,$http,$window,Notification,$anchorScroll,$modal,$window) {
    

$scope.subcategories={};

 $scope.viewsubcategory=function()
 {
    $http.post(GROXER_API_LINK+'?action=viewsubCategory')
               .success(function(data)
                { if(data)
                    {
                       
                       $scope.subcategories = data.subcategories;
                         $scope.daily_countsubcategory = $scope.subcategories.length;
                    }
                })
                    .error(function(data){
                       Notification.error( 'Check your internet connection'); 
                });
 }

 $scope.itemsPerPageCategories = 10;
    $scope.currentPageCategories = 0;
    $scope.rangeCategories = function() 
    {
        var rangeSize = 3;
        var ps = [];
        var start;
        start = $scope.currentPageCategories;
        //  console.log($scope.pageCountArea(),$scope.currentPageArea)
        if ( start > $scope.pageCountCategories()-rangeSize ) 
        {
            start = $scope.pageCountCategories()-rangeSize+1;
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
    $scope.prevPageCategories = function() 
    {
        if ($scope.currentPageCategories > 0) 
        {
            $scope.currentPageCategories--;
        }
    };
    //disable previous buuton
    $scope.DisablePrevPageCategories = function() 
    {
        return $scope.currentPageCategories === 0 ? "disabled" : "";
    };
    //page count code
    $scope.pageCountCategories = function() 
    {
        return Math.ceil($scope.subcategories.length/$scope.itemsPerPageCategories)-1;
    };
    //next button code
    $scope.nextPageCategories = function() 
    {
        if ($scope.currentPageCategories != $scope.pageCountCategories()) 
        {
            $scope.currentPageCategories++;
        }
    };
    //disable next button code
    $scope.DisableNextPageCategories = function() 
    {
        return $scope.currentPageCategories === $scope.pageCountCategories() ? "disabled" : "";
    };
    //current page 
    $scope.setPageCategories = function(n) 
    {
        $scope.currentPageCategories = n;
    };


    $scope.deletesubcategory = function(sid)
    {
        $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/delete_city.html',
                                controller: function($scope, $modalInstance, $modal, $http, $state,$window,$location,$anchorScroll) 
                                {
                                    
                                    $scope.ok =  function()
                                    {
                                            $http.post(GROXER_API_LINK+'?action=deletesubCategory',{id:sid})
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            $modalInstance.dismiss('cancel');
                                            Notification.success("Removed Sucessfully");
                                            
                                          $state.reload();
                                            
                                            // $anchorScroll('viewsubcategory');


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

 $scope.addsubcategory = function()
 {
    $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/add_subcategory.html',
                                controller: function($scope, $modalInstance, $modal, $http, $state) 
                                {
                                    
                                    $scope.subcategoryView = function () 
                                    {

                                         $http.post(GROXER_API_LINK+'?action=subcategoryView')
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            $scope.categories = res.categories;
                                            
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

                                        
                                    };
                                    
                                     $scope.cancel = function () {
                                         $modalInstance.dismiss('cancel');
                                     };

                                     $scope.insertsubcategory = function()
                                     {
                                        $http.post(GROXER_API_LINK+'?action=insertsubcategory',{id:$scope.categoryId,subcategory_name:$scope.subcategoryname})
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            $state.reload();
                                            $modalInstance.dismiss('cancel');
                                            Notification.success("Inserted Sucessfully");
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
                                },
                                resolve: {}
                            };
                            
                            $modal.open($scope.opts); 
    
 }


})