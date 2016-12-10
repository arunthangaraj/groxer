GroxerAdminApp.controller('ViewCustomer_Controller', ['$scope', '$http', 'Notification', '$state',  function($scope, $http, Notification, $state){
   
   $scope.stateaction=function()
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

    $scope.districtsaction= function()
    {
        
        $http.post(GROXER_API_LINK+'?action=districts',{id : $scope.state})
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

    $scope.cityaction= function()
    {

         

        $http.post(GROXER_API_LINK+'?action=city',
                {
                    id : $scope.district
                    
                })
                        .success(function(res){
                            if (res.success)
                            {
                                $scope.cities=res.cities;
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


    $scope.areaaction= function()
    {
        
        $http.post(GROXER_API_LINK+'?action=area',
                {
                    id : $scope.city
                    
                })
                        .success(function(res){
                            if (res.success)
                            {
                                $scope.areas=res.areas;
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
	

	$scope.viewcustomer= function()
	{
		$http.post(GROXER_API_LINK+'?action=viewcustomer')
                        .success(function(res){
                            if (res.success)
		                    {
                                $scope.customers=res.customers;
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

   $scope.saveCustomer = function()
    {
        $http.post(GROXER_API_LINK+'?action=saveCustomer',
                {
                    fname: $scope.fname,
                    lname: $scope.lname,
                    mobno: $scope.mobno,
                    land: $scope.land,
                    custemail: $scope.custemail,
                    address: $scope.address,
                    state: $scope.state,
                    district: $scope.district,
                    city: $scope.city,
                    area: $scope.area
                })
                        .success(function(res){
                            if (res.success)
                            {
                                localStorage['adminId'] = res.userid;
                                localStorage['adminName'] = res.userName;

                                Notification.success(res.message);

                                $state.go('changePassword');
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
	
}])