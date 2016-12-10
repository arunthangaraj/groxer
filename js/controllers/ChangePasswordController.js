GroxerAdminApp.controller('ChangePassword_Controller', ['$scope','$http', 'Notification', '$state', 'CheckAdmin', function($scope, $http, Notification, $state, CheckAdmin){

    CheckAdmin.verify();

    $scope.email = localStorage['adminName'];

    $scope.isPasswordMatch=function(){
        return ($scope.password === $scope.confirmPassword)? true:false;            
    };
    
    $scope.changePassword = function(){
    
        $http.post(GROXER_API_LINK+'?action=changepassword',
        {
            userid: localStorage['adminId'],
            old: $scope.old_password,
            new: $scope.password
        })
                .success(function(data){
                    if(data.success)
                    {
                        Notification.success(data.message);
                        // $state.go('myProfile');
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
                   Notification.error("Check your internet connection");
        });
    };

}])