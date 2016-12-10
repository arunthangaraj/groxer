<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include '../../connector/common_library.php';

define('GROXER_SALT',md5('Groxer_.com'));


class GroxerAdminModuleProducts extends CommonLibrary { 

    public function __construct() {
        parent::__construct();

    }

    public function getRequestAction() {

        switch ($this->_action) {

            case 'getProducts':
                $this->getProducts();
                break;

            case 'getSubCategories':
                $this->getSubCategories();
                break;

            case 'addProduct':
                $this->addProduct();
                break;

            case 'updateProduct':
                $this->updateProduct();
                break;

            case 'removeProduct':
                $this->removeProduct();
                break;

            case 'getProductDetails':
                $this->getProductDetails();
                break;

            case 'getCustomers':
                $this->getCustomers();
                break;

            case 'uploadcsv':
                $this->uploadcsv();
                break;
        }
    }

    public function getProducts()
    {
        $stmt = "select product_code,product_id,product_name from ".self::PRODUCTS_TABLE_NAME;

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $productSet = $prep->fetchAll();

        $resultProducts = array();

        foreach($productSet as $product)
        {
            $stmt = "select order_item_id from ".self::ORDER_ITEMS_TABLE_NAME." where product_id=?";
            $prep = $this->_objDatabase->prepare($stmt);
            $prep->execute(array($product['product_id']));
            $result = $prep->fetchAll();

            if(count($result) > 0)
            {
                $remove = 0;
            }
            else
                $remove = 1;

            $resultProducts[] = array(
                "product_id" => $product['product_id'],
                "product_code" => $product['product_code'],
                "product_name" => $product['product_name'],
                "remove" => $remove
                );
        }

        $this->_response = array("products" => $resultProducts);
    }

    public function getProductDetails()
    {
        $productId = $this->_paramsData['prod_id'];

        $stmt = "select product_code,product_id,product_name from ".self::PRODUCTS_TABLE_NAME." where product_id=?";

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($productId));

        $productSet = $prep->fetchAll();

        $stmt = "select category.sub_category_id,sub_category_name from ".self::PRODUCT_SUB_CATEGORY_TABLE_NAME." as prodCat "
                . "inner join ".self::SUB_CATEGORY_TABLE_NAME." as category on category.sub_category_id=prodCat.sub_category_id "
                . "where product_id=?";

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($productId));

        $categorySet = $prep->fetchAll();

        $stmt = "select product_child_id,product_qty_type,product_qty_value,product_price from ".self::PRODUCTS_CHILD_TABLE_NAME." where parent_product_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($productId));

        $childSet = $prep->fetchAll();

        $resultChildSet = array();

        foreach($childSet as $child)
        {
            $img = rawurldecode(self::DIR_LINK."/fuel/products/images/".md5($child['product_child_id']).".jpg");
            $resultChildSet[] = array (
                "id" => $child['product_child_id'],
                "qty_type" => $child["product_qty_type"],
                "qty_value" => $child["product_qty_value"],
                "price" => $child["product_price"],
                "img" => $img
                );
        }
        
            $resultProducts = array(
            	'product_code' => $productSet[0]['product_code'],
                'product_id' => $productSet[0]['product_id'],
                'product_name' => $productSet[0]['product_name'],
                'product_child' => $resultChildSet,
                'product_category' => $categorySet
            );
        
        $this->_response = array('products' => $resultProducts, "test" => $productId);
    }

    public function getSubCategories()
    {
        $stmt = "select sub_category_id,sub_category_name,category_id from ".self::SUB_CATEGORY_TABLE_NAME;
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $categorySet = $prep->fetchAll();

        $this->_response = array("subCategories" => $categorySet);
    }

    public function addProduct()
    {
        $success = false;
        $product_code = $_POST['product_code'];
        $product_name = $_POST['product_name'];

        $child_set = $_POST['product_child'];
        $category_id_set = $_POST['categories'];

        $stmt = "select product_id from ".self::PRODUCTS_TABLE_NAME." where product_code=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($product_code));

        $resultSet = $prep->fetchAll();

        if(count($resultSet) > 0)
        {
            $message = "Duplicate product code";
        }
        else
        {
            $stmt = "insert into ".self::PRODUCTS_TABLE_NAME." (product_name,product_code) values(?,?)";
            $prep = $this->_objDatabase->prepare($stmt);
            $prep->execute(array($product_name,$product_code));

            $productId = $this->_objDatabase->lastInsertId();

            foreach($category_id_set as $category_id)
            {
                $stmt = "insert into ".self::PRODUCT_SUB_CATEGORY_TABLE_NAME." (product_id,sub_category_id,created_on) values(?,?,?)";
                $prep = $this->_objDatabase->prepare($stmt);
                $prep->execute(array($productId,$category_id,date('Y-m-d H:i:s')));
            }

            $tar_dir = "../../fuel/products/images/";

            for ($index =0; $index < count($child_set); $index++) 
            { 
                $name = $child_set[$index]['value']." ".$child_set[$index]['typeval'];
                $stmt = "insert into ".self::PRODUCTS_CHILD_TABLE_NAME." (product_qty_type,product_qty_value,parent_product_id,product_price,product_child_name,created_on) values(?,?,?,?,?,?)";
                $prep = $this->_objDatabase->prepare($stmt);
                $prep->execute(array($child_set[$index]['type'],$child_set[$index]['value'],$productId,$child_set[$index]['price'],$name,date('Y-m-d H:i:s')));

                $filename = md5($this->_objDatabase->lastInsertId());

                $tar_file = $tar_dir.$filename.".jpg";
                move_uploaded_file($_FILES['file']["tmp_name"][$index], $tar_file);

            }

            $success = true;
            $message = "Products added successfully";
        }

        $this->_response = array("success" => $success, "message" => $message);
    }

    public function removeProduct()
    {
        $success = false;
        $product_id = $this->_paramsData['product_id'];;

        $stmt = "delete from ".self::PRODUCTS_TABLE_NAME." where product_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($product_id));

        $stmt = "delete from ".self::PRODUCT_SUB_CATEGORY_TABLE_NAME." where product_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($product_id));

        $stmt = "select product_child_id from ".self::PRODUCTS_CHILD_TABLE_NAME." where parent_product_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($product_id));

        $childSet = $prep->fetchAll();

        $tar_dir = "../../fuel/products/images/";

        foreach($childSet as $child)
        {
            $filename = md5($child['product_child_id']);

            $tar_file = $tar_dir.$filename.".jpg";
            unlink($tar_file);
        }

        $stmt = "delete from ".self::PRODUCTS_CHILD_TABLE_NAME." where parent_product_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($product_id));

        $this->_response = array("success" => true);
    }

    public function updateProduct()
    {
        $success = false;
        $product_id = $_POST['product_id'];
        $product_code = $_POST['product_code'];
        $product_name = $_POST['product_name'];

        $child_set = $_POST['product_child'];
        $category_id_set = $_POST['categories'];

        $stmt = "update ".self::PRODUCTS_TABLE_NAME." set product_name=? where product_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($product_name,$product_id));

        $stmt = "delete from ".self::PRODUCT_SUB_CATEGORY_TABLE_NAME." where product_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($product_id));

        foreach($category_id_set as $category_id)
        {
            $stmt1 = "insert into ".self::PRODUCT_SUB_CATEGORY_TABLE_NAME." (product_id,sub_category_id,created_on) values(?,?,?)";
            $prep1 = $this->_objDatabase->prepare($stmt1);
            $prep1->execute(array($product_id,$category_id,date('Y-m-d H:i:s')));
        }

            $tar_dir = "../../fuel/products/images/";

        for ($index =0; $index < count($child_set); $index++) 
        { 
            if($child_set[$index]['exist'] == 1)
            {
                if($child_set[$index]['remove'] == 1)
                {
                    $stmt = "delete from ".self::PRODUCTS_CHILD_TABLE_NAME." where product_child_id=?";
                    $prep = $this->_objDatabase->prepare($stmt);
                    $prep->execute(array($child_set[$index]['id']));

                    $filename = md5($child_set[$index]['id']);

                    $tar_file = $tar_dir.$filename.".jpg";
                    unlink($tar_file);
                }
                else
                {
                    $name = $child_set[$index]['value']." ".$child_set[$index]['typeval'];
                    $stmt = "update ".self::PRODUCTS_CHILD_TABLE_NAME." set product_qty_type=?, product_qty_value=?, product_price=? ,product_child_name=? where product_child_id=?";
                    $prep = $this->_objDatabase->prepare($stmt);
                    $prep->execute(array($child_set[$index]['type'],$child_set[$index]['value'],$child_set[$index]['price'],$name,$child_set[$index]['id']));

                    $filename = md5($child_set[$index]['id']);

                    $tar_file = $tar_dir.$filename.".jpg";
                    move_uploaded_file($_FILES['file']["tmp_name"][$index], $tar_file);
                }
            }
            else
            {
                $name = $child_set[$index]['value']." ".$child_set[$index]['typeval'];
                $stmt = "insert into ".self::PRODUCTS_CHILD_TABLE_NAME." (product_qty_type,product_qty_value,parent_product_id,product_price,product_child_name,created_on) values(?,?,?,?,?,?)";
                $prep = $this->_objDatabase->prepare($stmt);
                $prep->execute(array($child_set[$index]['type'],$child_set[$index]['value'],$product_id,$child_set[$index]['price'],$name,date('Y-m-d H:i:s')));

                $filename = md5($this->_objDatabase->lastInsertId());

                $tar_file = $tar_dir.$filename.".jpg";
                move_uploaded_file($_FILES['file']["tmp_name"][$index], $tar_file);
            }
        }

            $success = true;
            $message = "Products updated successfully";

        $this->_response = array("success" => $success, "message" => $message);
    }

    public function getCustomers()
    {

        $stmt = "select customer_id,business_name from ".self::CUSTOMERS_TABLE_NAME;

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $customerSet = $prep->fetchAll();

        $stmt = "select business_name,filename,product.when,ext from ".self::CUSTOMERS_TABLE_NAME. " as customer inner join ".self::PRODUCT_CSV_TABLE." as product on customer.customer_id=product.customer_id";

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $history = $prep->fetchAll();

        $this->_response = array("success" => true, "customers" => $customerSet, "history" => $history);
    }

    public function uploadcsv()
    {
        $customer_id = $_POST['customer_id'];

        $now=date('Ymdhis');
        $when=date('Y-m-d H:i:s');

        $strUpload_imgName=basename($_FILES['prodCsv']['name']);
        $strUpload_imgNameSplit=explode('.',$strUpload_imgName);
        $strUpload_tempPath='../temp/'.$now.'.'.$strUpload_imgNameSplit[1];
        $strUpload_targetPath='../product_csv/';

        if($_FILES['prodCsv']['name']!='')
        {
            $strUploadExists=1;
            $strUpload_targetPath=$strUpload_targetPath.$now.'.'.$strUpload_imgNameSplit[1]; 
            
            if(move_uploaded_file($_FILES['prodCsv']['tmp_name'], $strUpload_tempPath)) 
             {
                rename($strUpload_tempPath,$strUpload_targetPath);
                $strUpload_sucess=1;    
                
                $stmt = "insert into ".self::PRODUCT_CSV_TABLE."(`customer_id`,`filename`,`ext`,`filerename`,`when`) values(?,?,?,?,?)";
                $prep = $this->_objDatabase->prepare($stmt);
                $prep->execute(array($customer_id,$strUpload_imgNameSplit[0],$strUpload_imgNameSplit[1], $now, $when));

                $strUpload_strCsvLastId=$this->_objDatabase->lastInsertId();
                // unlink($strUpload_tempPath); 
            }

            if($strUpload_strCsvLastId!=0)
             {
                    $strUpload_fieldName=array();
                    $strLastIds=0;
                    $strUpload_file=fopen($strUpload_targetPath,"r");
                    $strUpload_i=0;
                    while(! feof($strUpload_file))
                    {
                        $strUpload_varArray=fgetcsv($strUpload_file);
                        $strUpload_timestamp=date("Ymdhisu");

                        if($strUpload_varArray[0]=='' || $strUpload_varArray[1]=='')
                        {
                            //nothing
                        }
                        else
                        {
                            if($strUpload_i>0)  
                            {
                                
                                $prodCode=trim($strUpload_varArray[0]);
                                // $prodName=trim($strUpload_varArray[1]);
                                
                                $stmt = "insert into ".self::MERCHANT_TABLE_NAME."$customer_id (`product_id`) values(?)";
                                $prep = $this->_objDatabase->prepare($stmt);
                                $prep->execute(array($prodCode));
                            }
                        }
                        $strUpload_i++;
                    }
             }
        }

        $this->_response = array("success" => true);
    }

}


$GroxerAdmin = new GroxerAdminModuleProducts();
$GroxerAdmin->getResponse();
?>