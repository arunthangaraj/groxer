GroxerAdminApp.controller('AreaListing_Controller', ['$scope', '$http', 'Notification', '$state','$modal', function($scope, $http, Notification, $state,$modal){

  $scope.showLoading=false;
  
  $scope.areas={};

  
  
 $scope.areaview = function()
    {
        $http.post(GROXER_API_LINK+'?action=areaView')
                        .success(function(res){
                            if (res.success)
                            {
                                $scope.areas = res.areas;
                                $scope.daily_countArea = $scope.areas.length;
                            }
                             
                            else
                            {
                                Notification.error(res.message);
                            }
                })
                        .error(function (res){
                            Notification.error("Check your internet connection");
                            
                });
    }


$scope.itemsPerPageArea = 10;
    $scope.currentPageArea = 0;
    $scope.rangeArea = function() 
    {
        var rangeSize = 3;
        var ps = [];
        var start;
        start = $scope.currentPageArea;
        //  console.log($scope.pageCountArea(),$scope.currentPageArea)
        if ( start > $scope.pageCountArea()-rangeSize ) 
        {
            start = $scope.pageCountArea()-rangeSize+1;
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
    $scope.prevPageArea = function() 
    {
        if ($scope.currentPageArea > 0) 
        {
            $scope.currentPageArea--;
        }
    };
    //disable previous buuton
    $scope.DisablePrevPageArea = function() 
    {
        return $scope.currentPageArea === 0 ? "disabled" : "";
    };
    //page count code
    $scope.pageCountArea = function() 
    {
        return Math.ceil($scope.areas.length/$scope.itemsPerPageArea)-1;
    };
    //next button code
    $scope.nextPageArea = function() 
    {
        if ($scope.currentPageArea != $scope.pageCountArea()) 
        {
            $scope.currentPageArea++;
        }
    };
    //disable next button code
    $scope.DisableNextPageArea = function() 
    {
        return $scope.currentPageArea === $scope.pageCountArea() ? "disabled" : "";
    };
    //current page 
    $scope.setPageArea = function(n) 
    {
        $scope.currentPageArea = n;
    };
    
    

 $scope.addarea = function()
{
    $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/add_area.html',
                                controller: function($scope, $modalInstance, $modal, $http) 
                                {
                                    
                                    $scope.cityList = function () 
                                    {

                                         $http.post(GROXER_API_LINK+'?action=cityList')
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            $scope.cities = res.cities;
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

                                     $scope.insertArea = function()
                                     {
                                        $http.post(GROXER_API_LINK+'?action=insertArea',{id:$scope.cityId,area_name:$scope.areaname})
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
                        };

   

     $scope.deleteArea = function(aid)
    {

        
            
             $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/delete_area.html',
                                controller: function($scope, $modalInstance, $modal, $http) 
                                {
                                    
                                    $scope.ok =  function()
                                    {
                                            $http.post(GROXER_API_LINK+'?action=deleteArea',{id:aid})
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            $state.reload();
                                            $modalInstance.dismiss('cancel');
                                            
                                            
                                            Notification.success("Removed Sucessfully");

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

                                     $scope.insertcity = function()
                                     {
                                        $http.post(GROXER_API_LINK+'?action=insertCity',{id:$scope.districtId,city_name:$scope.cityname})
                                        .success(function(res){
                                        if (res.success)
                                        {
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
	
}])