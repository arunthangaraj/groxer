GroxerAdminApp.controller('CityListing_Controller', ['$scope', '$http', 'Notification', '$state','$modal', function($scope, $http, Notification, $state,$modal){

  $scope.showLoading=false;
  $scope.cities={};
  $scope.areas={};

  
   $scope.cityview = function()
    {
        $http.post(GROXER_API_LINK+'?action=cityView')
                        .success(function(res){
                            if (res.success)
                            {
                                $scope.cities = res.cities;
                                $scope.daily_count = $scope.cities.length;
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
        return Math.ceil($scope.cities.length/$scope.itemsPerPage)-1;
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

$scope.addcity = function()
{
    $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/add_city.html',
                                controller: function($scope, $modalInstance, $modal, $http) 
                                {
                                    
                                    $scope.stateList=function()
                                    {
                                      $http.post(GROXER_API_LINK+'?action=state')
                                     .success(function(res){
                                     if (res.success)
                                      {
                                        $scope.states=res.states;
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

                   $scope.districtsList= function()
                   {
        
                     $http.post(GROXER_API_LINK+'?action=districts',{id : $scope.stateId})
                        .success(function(res){
                            if (res.success)
                            {
                                $scope.districts=res.districts;
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

                     $scope.insertCity = function()
                                     {

                                        $http.post(GROXER_API_LINK+'?action=insertCity',{id:$scope.districtId,city_name:$scope.cityname})
                                        .success(function(res){
                                        if (res.success)
                                        {
                                            
                                            $modalInstance.dismiss('cancel');
                                            $state.reload();
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

                                     $scope.cancel = function () {
                                         $modalInstance.dismiss('cancel');
                                     };

                                     
                                },
                                resolve: {}
                            };
                            
                            $modal.open($scope.opts); 
                        };



 

    $scope.editCity = function(cid,did)
    {
        $scope.showLoading=true;
         $http.post(GROXER_API_LINK+'?action=editCity',{cityId:cid,districtId:did})
         .success(function(res){
         if (res.success)
        {
            $scope.showLoading=false;
            
             $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/edit_city.html',
                                controller: function($scope, $modalInstance, $modal, $http) 
                                {
                                    
                                    $scope.calleditcity =  function()
                                    {
                                            $scope.districts = res.districts;
                                            $scope.cityname= res.cname;
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
                             
        else
        {
            Notification.error(res.message);
        }
        })
        .error(function (res){
             Notification.error("Check your internet connection");
                            
         });

               
    }


     $scope.deleteCity = function(cid)
    {

      
          
             $scope.opts = {
                                backdrop: true,
                                backdropClick: true,
                                dialogFade: true,
                                keyboard: true,
                                scope: $scope,
                                templateUrl: 'views/delete_city.html',
                                controller: function($scope, $modalInstance, $modal, $http) 
                                {
                                    
                                    $scope.ok =  function()
                                    {
                                            $http.post(GROXER_API_LINK+'?action=deleteCity',{id:cid})
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