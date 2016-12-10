GroxerAdminApp.controller('SignIn_Controller', ['$scope', '$http', 'Notification', '$state', 'CheckAdmin', function($scope, $http, Notification, $state, CheckAdmin){

	CheckAdmin.validate();
	
	$scope.signIn = function()
	{
		$http.post(GROXER_API_LINK+'?action=signin',
                {
                    email: $scope.email,
                    pass: $scope.password
                })
                        .success(function(res){
                            if (res.success)
		                    {
                                localStorage['adminId'] = res.userid;
                                localStorage['adminName'] = res.userName;

                                Notification.success(res.message);

                                $state.go('citylisting');
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