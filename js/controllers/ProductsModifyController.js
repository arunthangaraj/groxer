GroxerAdminApp.controller('ProductsModify_Controller', ['$scope','$http','Upload','$stateParams','Notification','$state', function($scope, $http, Upload, $stateParams, Notification, $state)
{

    $('#product_assig').removeClass("active");
    $('#das').removeClass("active");
    $('#cust').removeClass("active");
    $('#cat').removeClass("active");
    $('#product').addClass("active");
    $('#area').removeClass("active");

	$scope.selectedCategory = [];
    $scope.child_set = [];
    $imgs = [];
    $product_id = '';


    if($stateParams.id)
    {
    	$http.post(GROXER_API_LINK_PRODUCTS+"?action=getProductDetails",
        {
        	prod_id: $stateParams.id
        })
        .success(function(res) {

        	var category = res.products.product_category;

            for(var index=0; index < category.length; index++)
            {
                $scope.selectedCategory.push(category[index].sub_category_id);
            }

            var childSet = res.products.product_child;

            for(var index=0; index < childSet.length; index++)
            {
                $scope.child_set.push(new child(childSet[index].id, childSet[index].qty_type, childSet[index].qty_value, childSet[index].price, childSet[index].img, 1, 0));
                $imgs.push(childSet[index].img);
            }

            $scope.prodName = res.products.product_name;
            $scope.prodCode = res.products.product_code;

            $scope.title = "Edit Product";
            $scope.subTitle = "Edit product details";
            $scope.editProduct = true;
            $product_id = res.products.product_id;
            $scope.remove = $stateParams.remove;

        })
        .error(function(res) {

        });
    }
    else
    {
    	$scope.title = "Add Product";
    	$scope.subTitle = "Add new product";
    	$scope.editProduct = false;
    }

    $http.post(GROXER_API_LINK_PRODUCTS+"?action=getSubCategories",
        {})
        .success(function(res) {

            $scope.subCategories = res.subCategories;


        })
        .error(function(res) {

        });

        $scope.addProduct = function()
        {

            if($scope.prodCode == "")
            {
                Notification.error("Product code cannot be empty");
            }
            else if($scope.prodName == "")
            {
                Notification.error("Product name cannot be empty");
            }

            else if($scope.selectedCategory.length == 0)
            {
                Notification.error("Category cannot be empty");
            }

            else if($scope.child_set.length == 0)
            {
                Notification.error("Quantity cannot be empty");
            }
            else
            {


                var upl = Upload.upload({
                    url: GROXER_API_LINK_PRODUCTS+"?action=addProduct",
                    data: {
                            file: $imgs, 
                            product_code: $scope.prodCode,
                            product_name: $scope.prodName,
                            categories: $scope.selectedCategory,
                            product_child: $scope.child_set
                        }
                });

                upl.then(function(res) {
                    console.log(res);

                    if(res.data.success)
                    {
                        Notification.success(res.data.message);
                        $state.reload();
                    }
                    else
                    {
                        Notification.error(res.data.message);
                    }

                },
                function(res) {

                    console.log(res);

                },
                function(event) {

                });
            }

        }

        $scope.updateProduct = function()
        {

            if($scope.prodName == "")
            {
                Notification.error("Product name cannot be empty");
            }

            else if($scope.selectedCategory.length == 0)
            {
                Notification.error("Category cannot be empty");
            }

            else if($scope.child_set.length == 0)
            {
                Notification.error("Quantity cannot be empty");
            }
            else
            {
                var flag = 0;

                for(var index=0; index < $scope.child_set.length; index++)
                {
                    if($scope.child_set[index].exist == 1 && $scope.child_set[index].remove == 0)
                    {
                        flag = 1;
                    }
                }

                if(flag == 0)
                {
                    Notification.error("Quantity cannot be empty");
                }
                else
                {

                    var upl = Upload.upload({
                        url: GROXER_API_LINK_PRODUCTS+"?action=updateProduct",
                        data: {
                                file: $imgs, 
                                product_id: $product_id,
                                product_code: $scope.prodCode,
                                product_name: $scope.prodName,
                                categories: $scope.selectedCategory,
                                product_child: $scope.child_set
                            }
                    });

                    upl.then(function(res) {

                        // console.log(res);
                        Notification.success(res.data.message);
                        $state.reload();

                    },
                    function(res) {

                    },
                    function(event) {

                    });
                }
            }
        }

        $scope.addQuantity = function()
        {
            var flag = true;

            for(var index=0; index < $scope.child_set.length; index++)
            {
                if($scope.child_set[index].value == $scope.value)
                    flag = false;
            }

            if(flag)
            {
                var c_id = "C"+$scope.child_set.length;
                $scope.child_set.push(new child(c_id, $scope.type, $scope.value, $scope.price, $scope.picfile, 0, 0));
                $imgs.push($scope.picfile);
                $scope.type="";
                $scope.value=null;
                $scope.price=null;
                $scope.picfile = null;
            }
            else
            {
                 Notification.error("Quantity value already exists");
                
            }
        }

        $scope.editChild = function(id)
        {
            $scope.childEdit = true;

            for(var index=0; index < $scope.child_set.length; index++)
            {
                if($scope.child_set[index].id == id)
                {
                    $scope.childId = $scope.child_set[index].id;
                    $scope.type= $scope.child_set[index].type;
                    $scope.value= $scope.child_set[index].value;
                    $scope.price= $scope.child_set[index].price;
                    $scope.picfile = $scope.child_set[index].img;
                    break;
                }
            }
        }

        $scope.updateChild = function()
        {
            $scope.childEdit = false;

            var flag = true;
            var childIndex = 0;

            for(var index=0; index < $scope.child_set.length; index++)
            {
                if($scope.child_set[index].id == $scope.childId)
                    childIndex = index;
                else if($scope.child_set[index].value == $scope.value)
                    flag = false;
            }

            if(flag)
            {
                $scope.child_set[childIndex].type = $scope.type;
                $scope.child_set[childIndex].value = $scope.value;
                $scope.child_set[childIndex].price = $scope.price;
                $scope.child_set[childIndex].img = $scope.picfile;
                $imgs[childIndex] = $scope.picfile;
                        
                $scope.type="";
                $scope.value=null;
                $scope.price=null;
                $scope.picfile = null;
                $scope.childId = null;
            }
            else
            {
                Notification.error("Quantity value already exists");
            }
        }

        $scope.removeChild = function(id)
        {
            for(var index=0; index < $scope.child_set.length; index++)
            {
                if($scope.child_set[index].id == id)
                {
                    if($scope.child_set[index].exists == 0)
                    {
                        $scope.child_set.splice(index,1);
                        $imgs.splice(index,1);
                    }
                    else
                    {
                        $scope.child_set[index].remove = 1;
                    }
                }
            }
        }

        $scope.toggleSelection = function(categoryId)
        {
            var index = $scope.selectedCategory.indexOf(categoryId);
            if(index > -1)
            {
                $scope.selectedCategory.splice(index, 1);
            }
            else
            {
                $scope.selectedCategory.push(categoryId);
            }
        }

    function child(id, type, value, price, img, exist, remove) 
    {
        this.id = id;
        this.type = type;
        if(type == 1)
            this.typeval = "ml";
        else
            this.typeval = "gms";
        this.value = value;
        this.price = price;
        this.img = img;
        this.exist = exist;
        this.remove = remove;
    }
}]);