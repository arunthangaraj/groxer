GroxerAdminApp.service('CheckAdmin', ['$state', function($state){
	this.isValid = function()
	{
		return !(localStorage['adminId'] == null || localStorage['adminId'] == 'null');
	};

	this.validate = function()
	{
		if(this.isValid())
		{
			$state.go('changePassword');
		}
	};

	this.verify = function()
	{
		if(! this.isValid())
		{
			$state.go('signIn');
		}
	}
}]);