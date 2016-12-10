GroxerAdminApp.controller('ResetPassword_Controller', ['$scope','$http','$state','Notification','CheckAdmin', function($scope, $http, $state, Notification, CheckAdmin){

        CheckAdmin.validate();

       $scope.resetPassword = function(){
        
         $http.post(Groxer_API_LINK+'?action=passwordreset',
                 {
                     email: $scope.email
                 })
                         .success(function(data){
                            if(data.success)
                            {
                                Notification.success(data.message);
                                $state.go('signIn');
                            }
                             
                            else
                            {
                                Notification.error(data.message);
                            }
                })
                        .error(function (data){
                            Notification.error('Check your internet connection');
                });
        };
	
}])