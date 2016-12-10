GroxerAdminApp.controller('ResetPasswordAuth_Controller', ['$scope','$state','Notification','$stateParams', '$http', function($scope, $state, Notification, $stateParams, $http){
	
	var token = $stateParams.auth;

	if(token == '')
	{
		Notification.error('Invalid access');
		$state.go('signIn');
	}

	$scope.isPasswordMatch=function()
	{
        return ($scope.password === $scope.confirmPassword)? true:false;            
    };


	$scope.resetPassword = function()
	{

		$http.post(GROXER_API_LINK+'?action=resetPasswordAuth',
                {
                    token: token,
                    code: $scope.code,
                    password: $scope.password
                })
                        .success(function(data){
                            if(data.success)
                            {
                                Notification.success('Password changed successfully');
                                $state.go('signIn');
                            }
                             
                            else
                            {
                                Notification.error(data.message);
                                    $scope.old_password="";
                                    $scope.password = '';
                                    $scope.confirmPassword = '';
                            }
                })
                        .error(function (data){
                            Notification.error("Check your internet connetion");
                });
	};

}])