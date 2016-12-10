<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include '../../connector/common_library.php';

define('GROXER_SALT',md5('Groxer_.com'));


class GroxerAdminModule extends CommonLibrary { 

    


    public function __construct() {
        parent::__construct();

    }

    public function getRequestAction() {

        switch ($this->_action) {

            case 'signin':
                $this->signIn();
                break;

            case 'logout':
                $this->logout();
                break;

            case 'changepassword':
                $this->changePassword();
                break;

            case 'passwordreset':
                $this->passwordReset();
                break;

            case 'resetPasswordAuth':
                $this->resetPasswordAuth();
                break;

            case 'viewcustomer':
                $this->viewCustomer();
                break; 
            case 'districts':
                $this->districts();
                break; 
            case 'city':
                $this->city();
                break;
            case 'area':
                $this->area();
                break; 
            case 'state':
                $this->state();
                break;  
            case 'saveCustomer':
                $this->saveCustomer();
                break;
            case 'cityView':
                $this->cityView();
                break;
            case 'districtList':
                $this->districtList();
                break;  

            case 'insertCity':
                $this->insertCity();
                break;  
            case 'areaView':
                $this->areaView();
                break; 
            case 'cityList':
                $this->cityList();
                break;  

            case 'insertArea':
                $this->insertArea();
                break; 
            case 'editCity':
                $this->editCity();
                break; 
            case 'deleteCity' :
                $this->deleteCity();
                break;
            case 'deleteArea' :
                $this->deleteArea();
                break;
            case 'overallDetails':
                $this->overallDetails();
                break;
            case 'topSell':
                $this->topSell();
                break;
            case 'merchantAction':
                $this->merchantAction();
                break;
            case 'viewCategory':
                $this->viewCategory();
                break;  
            case 'deleteCategory':
                $this->deleteCategory();
                break;  
            case 'insertCategory':
                $this->insertCategory();
                break;    
            case 'viewsubCategory':
                $this->viewsubCategory();
                break; 
            case 'deletesubCategory':
                $this->deletesubCategory();
                break;                                                                  
            case 'subcategoryView':
                $this->subcategoryView();
                break;   
            case 'insertsubcategory':
                $this->insertsubcategory();
                break; 
            case 'vieworders':
                $this->vieworders();
                break;                  
        }
    }

public function vieworders()
{
     $success = false;
        $result = array();
       
        $stmt="select i.invoice_no,i.payment_status,i.updated_on,i.user_order_id as order_id,i.user_id,
udt.name,it.price,it.merchant_price,c.business_name,i.merchant_id,sum(it.price) as gTotal,sum(it.merchant_price) 
as gTotal_m,count(it.qty) as items from ".self::USER_DETAILS_TABLE_NAME. " as udt inner join ".self::USER_ORDER_TABLE_NAME." as i on udt.user_id=i.user_id inner join ".self::ORDER_ITEMS_TABLE_NAME." as it on i.user_order_id=it.user_order_id inner join ".self::CUSTOMERS_TABLE_NAME." as c on i.merchant_id=c.customer_id group by i.invoice_no order by i.updated_on desc ";      
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000")
        {
          
            foreach ($resultSet as $resultSet1)
            {
                $paymentStatus = 'Processing';
                $statusClass='label label-info';
                if($resultSet1['payment_status'] == 1)
                {
                    $paymentStatus = 'Approved';
                    $statusClass='label label-success';
                }
                if($resultSet1['payment_status'] == 2)
                {
                    $paymentStatus = 'Declined';
                    $statusClass='label label-warning';
                }
                
               
                $result[] = array(
                    "invoice_no" => $resultSet1['invoice_no'],
                    "status" => $paymentStatus,
                    "name" => $resultSet1['name'],
                    "price" => $resultSet1['price'],
                    "merchant_price" => $resultSet1['merchant_price'],
                    "purchased_on" => date("M d, Y",strtotime($resultSet1['updated_on']) ),
                    "merchant_id"=>$resultSet1['merchant_id'],
                    "statusClass"=>$statusClass,
                    "business_name"=>$resultSet1['business_name'],
                    "items"=>$resultSet1['items']
                    
                );
            }
            
            $success = true;
             }
        
        
        
        $this->_response = array('success' => $success, 'orders' => $result);
}

public function insertsubcategory()
{
    $success=true;
    $subcategoryname = $this->_paramsData['subcategory_name'];
    $id = $this->_paramsData['id'];
    $stmt = 'insert into '.self::SUB_CATEGORY_TABLE_NAME.' (`sub_category_name`,`category_id`)values(?,?)';
                $prepare = $this->_objDatabase->prepare($stmt);
                $prepare->execute(array($subcategoryname,$id));
    $this->_response = array('success' => $success); 
}

public function subcategoryView()
{
     
    $result[]=array('name' => 'Select');
        
       $success=false;
       
        $stmt = "select * from ".self::CATEGORY_TABLE_NAME." where category_archive=0 ";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "id" => $resultSet1['category_id'],
                     "name" => $resultSet1['category_name']
                );
               
             }

            $success = true;
            }
            $this->_response = array('success' => $success, 'categories' => $result);
}

public function deletesubCategory()
{
    $id = $this->_paramsData['id'];
        $stmt = "update ".self::SUB_CATEGORY_TABLE_NAME." set sub_category_archive=1 where sub_category_id=$id";

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $this->_response = array('success' => true);
}


public function viewsubCategory()
{
       $success=false;
    $stmt = "select * from ".self::SUB_CATEGORY_TABLE_NAME." as s inner join ".self::CATEGORY_TABLE_NAME." as c on s.category_id=c.category_id where s.sub_category_archive=0";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "sname" => $resultSet1['sub_category_name'],
                     "id" => $resultSet1['sub_category_id'],
                     "cname" => $resultSet1['category_name'],
                     "cid" => $resultSet1['category_id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'subcategories' => $result);
}


public function insertCategory()
{
    $success=true;
    $category_name = $this->_paramsData['category_name'];
    $now=date('Y-m-d h-i-s');
    $stmt = 'insert into '.self::CATEGORY_TABLE_NAME.' (`category_name`,`created_on`)values(?,?)';
                $prepare = $this->_objDatabase->prepare($stmt);
                $prepare->execute(array($category_name,$now));
    $this->_response = array('success' => $success);
}


public function deleteCategory()
{
    $id = $this->_paramsData['id'];
        $stmt = "update ".self::CATEGORY_TABLE_NAME." set category_archive=1 where category_id=$id";

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $this->_response = array('success' => true);
}
public function viewCategory()
{
        $success=false;
       
        $stmt = "select c.category_id,c.category_name,c.created_on,count(s.category_id) as countc from ".self::CATEGORY_TABLE_NAME." as c inner join ".self::SUB_CATEGORY_TABLE_NAME." as s on c.category_id=s.category_id where c.category_archive=0 and s.sub_category_archive=0 group by c.category_id ";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "id" => $resultSet1['category_id'],
                     "name" => $resultSet1['category_name'],
                      "date_create" => date('d-M-Y',strtotime($resultSet1['created_on'])),
                      "ccount" => $resultSet1['countc']
                );
               
             }
            
            $success = true;
            }
           
            $stmt = "select * from ".self::CATEGORY_TABLE_NAME." where category_archive=0 and category_id not in(select c.category_id from ".self::CATEGORY_TABLE_NAME." as c inner join ".self::SUB_CATEGORY_TABLE_NAME." as s on c.category_id=s.category_id where c.category_archive=0 and s.sub_category_archive=0 group by c.category_id )  ";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "id" => $resultSet1['category_id'],
                     "name" => $resultSet1['category_name'],
                      "date_create" => date('d-M-Y',strtotime($resultSet1['created_on'])),
                      "ccount" => 0
                );
               
             }
            
            
            }
           
            $this->_response = array('success' => $success, 'categories' => $result);
}

public function merchantAction()
{
 $success=false;
        $result=[];
        $completedCount=0;
        $pendingCount=0;
        $mid =  $this->_paramsData['merchantid']; 
        $stmt = "select i.payment_status,count(distinct(i.user_order_id)) as Tot_orders,cus.customer_id,sum(merchant_price*qty) as  tot_amount,cus.business_name from ".self::CUSTOMERS_TABLE_NAME." as cus inner join ".self::USER_ORDER_TABLE_NAME." as i on cus.customer_id=i.merchant_id inner join ".self::ORDER_ITEMS_TABLE_NAME." as it on i.user_order_id=it.user_order_id group by cus.business_name order by i.updated_on desc limit 0,10";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                               
                $result[] = array(     
                     "merchantName" => $resultSet1['business_name'],
                     "totalOrders" => $resultSet1['Tot_orders'],
                     "totalAmount" => number_format($resultSet1['tot_amount'],2,'.',','),
                     "customer_id" => $resultSet1['customer_id']
                );
               
            $success = true;
            }
            $stmt = "select count(i.payment_status) as completed,cus.business_name,cus.customer_id from ".self::CUSTOMERS_TABLE_NAME." as cus inner join ".self::USER_ORDER_TABLE_NAME." as i on cus.customer_id=i.merchant_id where i.payment_status=1 group by cus.business_name order by i.updated_on desc limit 0,10";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSetComplete = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSetComplete as $resultSetComplete1)
            {
               
                
                if($resultSetComplete1['completed']!=0)
                {
                    $result1[] = array(     
                     "customer_id" => $resultSetComplete1['customer_id'],
                     "statusCompleted" => $resultSetComplete1['completed']
                     );
                }
                
               
            }
        
          }  
          $stmt = "select * from ".self::CUSTOMERS_TABLE_NAME." where customer_id not in(select merchant_id from ".self::USER_ORDER_TABLE_NAME." where payment_status=1)";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSetComplete = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSetComplete as $resultSetComplete1)
            {
               
                
                
                    $result1[] = array(     
                     "customer_id" => $resultSetComplete1['customer_id'],
                     "statusCompleted" => 0
                     );
                
                
               
            }
        
          }      //end
             $stmt = "select count(i.payment_status) as completed,cus.business_name,cus.customer_id from ".self::CUSTOMERS_TABLE_NAME." as cus inner join ".self::USER_ORDER_TABLE_NAME." as i on cus.customer_id=i.merchant_id where i.payment_status=0 group by cus.business_name order by i.updated_on desc limit 0,10";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSetPending = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSetPending as $resultSetPending1)
            {
               if($resultSetPending1['completed']>0)
                {
                    $result2[] = array(     
                     "customer_id" => $resultSetPending1['customer_id'],
                     "statusPending" => $resultSetPending1['completed']
                     );
                }
            }
            }


            $stmt = "select * from ".self::CUSTOMERS_TABLE_NAME." where customer_id not in(select merchant_id from ".self::USER_ORDER_TABLE_NAME." where payment_status=0)";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSetPending = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSetPending as $resultSetPending1)
            {
               
                
                
                    $result2[] = array(     
                     "customer_id" => $resultSetPending1['customer_id'],
                     "statusPending" => 0
                     );
                
                
               
            }
        
          }   
            $this->_response = array('success' => $success, 'merchants' => $result, 'completedOrders' =>   $result1, 'pendingOrders' => $result2 );   
}    
}

 public function topSell()
    {
        
        $success=false;
        $result=[];
        $stmt = "select p.product_name,c.product_child_name,oi.price,c.product_child_id,count(c.product_child_id) as sold  from products_tb as p inner join product_child_tb as c on p.product_id=c.parent_product_id inner join order_items_tb as oi on c.product_child_id=oi.product_id inner join user_order_tb as uo on oi.user_order_id=uo.user_order_id and uo.payment_status=1 group by c.product_child_id order by sold desc limit 0,10";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000")
        {
            foreach ($resultSet as $resultSet1)
            {
                $result[] = array(     
                    "img"=>rawurldecode("../fuel/products/images/".md5($resultSet1['product_child_id']).".jpg"),
                    "product_name" => $resultSet1['product_name'],
                    "child_name"=>$resultSet1['product_child_name'],
                    "price"=>$resultSet1['price'],
                    "sold"=>$resultSet1['sold']
                );
               
            }
            
            $success = true;
        }
        
        $this->_response = array('success' => $success, 'sells' => $result);
    }

public function overallDetails()
{
    $success=true;
        $mid =  $this->_paramsData['merchantid']; 
        $stmt = "select count((oi.product_id)) as gTotal,count(pct.product_child_id) as
 count_or,sum(oi.merchant_price*oi.qty) as tot_revenue,sum(oi.merchant_price) 
 as total_rev from ".self::USER_ORDER_TABLE_NAME." as uo inner join ".self::ORDER_ITEMS_TABLE_NAME." as oi on uo.user_order_id=oi.user_order_id inner join ".self::PRODUCTS_CHILD_TABLE_NAME." as
 pct on oi.product_id=pct.product_child_id where uo.payment_status=1";       
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $arr1_profit =$prep->fetch(PDO::FETCH_BOTH);
 
        
                if($arr1_profit['count_or']!=0)
                {
                $co=$arr1_profit['count_or'];
                
                $totalSold=$arr1_profit['gTotal'];
                
                $sum_prog=$arr1_profit['gTotal']/$co;             

                $grandTotal= number_format($arr1_profit['tot_revenue'],2,'.',',');

                $grandper=$arr1_profit['tot_revenue']/$arr1_profit['total_rev'];
                
                $stmt="select count(invoice_no) as invoice_count from ".self::USER_ORDER_TABLE_NAME;

                $prep1 = $this->_objDatabase->prepare($stmt);
                $prep1->execute();
               
            
                $arr1_orders =$prep1->fetch(PDO::FETCH_BOTH);

                $invoice_count=$arr1_orders['invoice_count'];

                $per_invo=$invoice_count/100;
                }
               else {
                   $totalSold=0;
                   $sum_prog=0;
                   $grandTotal=0;
                   $grandper=0;
                   $invoice_count=0;
                   $per_invo=0;
               }
                $this->_response = array('success' => $success,'totalProduct'=>$totalSold, 'progressTotal'=>$sum_prog,'grandTotal'=>$grandTotal,'progresspre'=>$grandper,'totalOrder'=>$invoice_count,'progressOrder'=>$per_invo);        
}

public function deleteArea()
{
    $aid =  $this->_paramsData['id']; 
         $success=true;
         $stmt = "delete from ".self::AREA_TABLE_NAME." where area_id='$aid' ";       
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
         $this->_response = array('success' => $success);
}

public function deleteCity()
{
    $cid =  $this->_paramsData['id']; 
         $success=true;
         $stmt = "delete from ".self::CITY_TABLE_NAME." where city_id='$cid' ";       
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
         $this->_response = array('success' => $success);
}

public function editCity()
{
    $success=false;
    $cname=null;
    $disId=null;
    $cityId = $this->_paramsData['cityId'];
    $districtId = $this->_paramsData['districtId'];

            $stmt = "select * from ".self::DISTRICTS_TABLE_NAME." order by name asc";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "dname" => $resultSet1['name'],
                     "did" => $resultSet1['id']
                    
                );
               
             }

            $success = true;
            }
            $this->_response = array('success' => $success, 'districts' => $result, 'cname' => $cname);

}

public function insertArea()
{
    $success=true;
    $area_name = $this->_paramsData['area_name'];
    $id = $this->_paramsData['id'];
    $stmt = 'insert into '.self::AREA_TABLE_NAME.' (`area_name`,`city_id`)values(?,?)';
                $prepare = $this->_objDatabase->prepare($stmt);
                $prepare->execute(array($area_name,$id));
    $this->_response = array('success' => $success);
}


public function cityList()
{
    $success=false;
    $result[]=array('name' => 'Select');
        
        $stmt = "select * from ".self::CITY_TABLE_NAME." order by city_name asc";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "name" => $resultSet1['city_name'],
                     "id" => $resultSet1['city_id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'cities' => $result);
}

public function areaView()
{
    $success=false;
    $stmt = "select * from ".self::AREA_TABLE_NAME." as a inner join ".self::CITY_TABLE_NAME." as c on a.city_id=c.city_id";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "aname" => $resultSet1['area_name'],
                     "aid" => $resultSet1['area_id'],
                     "cname" => $resultSet1['city_name'],
                     "cid" => $resultSet1['city_id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'areas' => $result);
}    

public function insertCity()
{
    $success=true;
    $city_name = $this->_paramsData['city_name'];
    $id = $this->_paramsData['id'];
    $stmt = 'insert into '.self::CITY_TABLE_NAME.' (`city_name`,`district_id`)values(?,?)';
                $prepare = $this->_objDatabase->prepare($stmt);
                $prepare->execute(array($city_name,$id));
    $this->_response = array('success' => $success);
}


public function districtList()
{
    $success=false;
    $result[]=array('name' => 'Select');
        
        $stmt = "select * from ".self::DISTRICTS_TABLE_NAME." order by name asc";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "name" => $resultSet1['name'],
                     "id" => $resultSet1['id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'districts' => $result);
}

public function cityView()
{
    $success=false;
    $stmt = "select * from ".self::CITY_TABLE_NAME." as c inner join ".self::DISTRICTS_TABLE_NAME." as d on c.district_id=d.id";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "cname" => $resultSet1['city_name'],
                     "cid" => $resultSet1['city_id'],
                     "dname" => $resultSet1['name'],
                     "did" => $resultSet1['id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'cities' => $result);
}

public function saveCustomer()
{
         $success=false;
         $now=date('Y-m-d h-i-s');
         $fname = $this->_paramsData['fname'];
         $lname = $this->_paramsData['lname'];
         $custemail= $this->_paramsData['custemail'];
         $mobno = $this->_paramsData['mobno'];
         $land = $this->_paramsData['land'];
         $address = $this->_paramsData['address'];
         $state = $this->_paramsData['state'];
         $district = $this->_paramsData['district'];
         $city_name = $this->_paramsData['city'];
         $area_name = $this->_paramsData['area'];
         $stmt = "select customer_id from ".self::CUSTOMERS_TABLE_NAME." where email='$custemail' limit 0,1";
         $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $resultSet = $prep->fetchAll();
       
         
       
        if( count($resultSet) == 0)
        {
            if($fname!='')
            {

            $fnam=strtolower($fname);

            $fname_low=preg_replace('/\s+/', '', $fnam);


          


               
               // copy('../merchant/index.html','../'.$fname_low.'/index.html');

                $data_array = array($fname, $lname,$custemail,$mobno,$land,$address,$state,$district,$city_name,$area_name,$fname_low,$now);    
               $stmt="insert into `".self::CUSTOMERS_TABLE_NAME."`(`business_name`, `customer_name`,`email`,`mobile_no`, `landline`,`address`, `state_id`,`district_id`,`city_id`,`area_id`,`folder_name`,`date_created`) values (?,?,?,?,?,?,?,?,?,?,?,?)";        
               $prep = $this->_objDatabase->prepare($stmt);
               $prep->execute($data_array);
                $last_id_cust =$this->_objDatabase->lastInsertId();
                 $business_code='Gx0000'.$last_id_cust;
                $stmt = 'update `'.self::CUSTOMERS_TABLE_NAME.'` set `business_code`=? where `customer_id`=?';

                $prep = $this->_objDatabase->prepare($stmt);

                $prep->execute(array($business_code,$last_id_cust));

                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
                $password_gen = substr( str_shuffle( $chars ), 0, 5 );

                $hash_password_gen = hash('sha512',$password_gen.GROXER_SALT);
  
                $stmt = 'insert into '.self::MERCHANT_LOGIN_TABLE.' (`user_name`,`password`,`merchant_id`)values(?,?,?)';
                $prepare = $this->_objDatabase->prepare($stmt);
                $prepare->execute(array($custemail,$hash_password_gen,$last_id_cust));


                $stmt = "CREATE TABLE IF NOT EXISTS merchant_".$last_id_cust."(mp_id INT NOT NULL AUTO_INCREMENT,product_id INT(11) NOT NULL,UNIQUE (product_id),PRIMARY KEY(mp_id))";

                $prepare = $this->_objDatabase->prepare($stmt);
                $prepare->execute();

                // $subject="Groxer Merchant Link";

                // $site='groxer.com';

                // $content='Your Shop Link is &nbsp; http://www.groxer.com/'.$fname_low.'/#/shop <br><br><strong>Your Store Admin Login Details:</strong><br>Your Username Is &nbsp;'.$custemail.'<br><br>Your Password Is &nbsp;'.$password_gen;

                // $flag11=call_mail('no-reply@groxer.com',$custemail,$subject,$content,$lname,$site);


                // $target_path = "../fuel/customers/images/";
                // $target_path1 = $target_path . basename( $_FILES['customerimg']['name']); 
                // $slidimg=basename($_FILES['customerimg']['name']);
                // $explode_zip_name=explode('.',$slidimg);
                // $ext=$explode_zip_name[1];
                // $cust_img=$target_path.md5($last_id_cust).'.'.$ext;
                // move_uploaded_file($_FILES['customerimg']['tmp_name'], $cust_img); 




                $success=true;
      
}

        }
        $this->_response = array('success' => $success);
}

    public function state()
    {
        $success=false;
        $result[]=array('name' => 'Select');
        $stmt = "select * from ".self::STATE_TABLE_NAME;
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "name" => $resultSet1['name'],
                     "id" => $resultSet1['id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'states' => $result);
    }

    public function area()
    {
        $id = $this->_paramsData['id'];
        $success=false;
        $result[]=array('name' => 'Select');
        $stmt = "select * from ".self::AREA_TABLE_NAME." where city_id=$id";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "name" => $resultSet1['area_name'],
                     "id" => $resultSet1['area_id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'areas' => $result);
    }

    public function city()
    {
        $id = $this->_paramsData['id'];
        $result[]=array('name' => 'Select');
        $success=false;
        $stmt = "select * from ".self::CITY_TABLE_NAME." where district_id=$id";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "name" => $resultSet1['city_name'],
                     "id" => $resultSet1['city_id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'cities' => $result);
    }

    public function districts()
    {
        $id = $this->_paramsData['id'];
        $result[]=array('name' => 'Select');
        $success=false;
        $stmt = "select * from ".self::DISTRICTS_TABLE_NAME." where state_id=$id";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "name" => $resultSet1['name'],
                     "id" => $resultSet1['id']
                    
                );
               
             }
            $success = true;
            }
            $this->_response = array('success' => $success, 'districts' => $result);
    }

    public function viewCustomer()
    {
        $success=false;
        $stmt = "select * from ".self::CUSTOMERS_TABLE_NAME." as c inner join ".self::STATE_TABLE_NAME." as s on c.state_id = s.id order by c.date_created";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();
        $resultSet = $prep->fetchAll();
        $error = $prep->errorInfo();
        if($error[0] == "00000") 
        {
            
            foreach ($resultSet as $resultSet1)
            {
                
                $result[] = array(     
                     "code" => $resultSet1['business_code'],
                    "business_name" => $resultSet1['business_name'],
                    "customer_name"=> $resultSet1['customer_name'],
                    "phone" => $resultSet1['mobile_no'],
                    "address" => $resultSet1['address'],
                    "state" => $resultSet1['name'],
                    "date_create" => date("M d, Y",strtotime($resultSet1['date_created']))
                );
               
            }
            
           
             
            $success = true;
            }
            $this->_response = array('success' => $success, 'customers' => $result);
    }


    public function resetPasswordLinkMail($email, $name, $value) 
    {
        $html ='
<style type="text/css">
html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
@media only screen and (max-device-width: 680px), only screen and (max-width: 680px) { 
    *[class="table_width_100"] {
        width: 96% !important;
    }
    *[class="border-right_mob"] {
        border-right: 1px solid #dddddd;
    }
    *[class="mob_100"] {
        width: 100% !important;
    }
    *[class="mob_center"] {
        text-align: center !important;
    }
    *[class="mob_center_bl"] {
        float: none !important;
        display: block !important;
        margin: 0px auto;
    }   
    .iage_footer a {
        text-decoration: none;
        color: #929ca8;
    }
    img.mob_display_none {
        width: 0px !important;
        height: 0px !important;
        display: none !important;
    }
    img.mob_width_50 {
        width: 40% !important;
        height: auto !important;
    }
}
.table_width_100 {
    width: 680px;
}
</style>



<div id="mailsub" class="notification" align="center">

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;"><tr><td align="center" bgcolor="#eff3f8">



<table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%" style="max-width: 680px; min-width: 300px;">
    <!--header -->
    <tr><td align="center" bgcolor="#eff3f8">
        <!-- padding -->
        <table width="96%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="left"><!-- 

                Item -->
                <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
                <div class="mob_center_bl" style="float: left; display: inline-block; width: 115px;">
                    <table class="mob_center" width="115" border="0" cellspacing="0" cellpadding="0" align="left" style="border-collapse: collapse;">
                        <tr><td align="left" valign="middle">
                            <!-- padding -->
                            <table width="115" border="0" cellspacing="0" cellpadding="0" >
                                <tr><td align="left" valign="top" class="mob_center">
                                    <!--<a href="#" target="_blank" style="color: #596167;  font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                    <font face="Arial, Helvetica, sans-seri; font-size: 35px;" size="6" color="#4db3a5">
                                    Groxer
                                    </font></a>-->
                                    <img src="'.self::DIR_LINK.'/logo/logo.png">
                                </td></tr>
                            </table>                        
                        </td></tr>
                    </table></div><!-- Item END--><!--[if gte mso 10]>
                    </td>
                    <td align="right">
                <![endif]--><!-- 

                Item -->
                
                <div class="mob_center_bl" style="float: right; display: inline-block; width: 88px;">
                    <table width="88" border="0" cellspacing="0" cellpadding="0" align="right" style="border-collapse: collapse;">
                        <tr><td align="right" valign="middle">
                            <!-- padding -->
                            
                        </td></tr>
                    </table></div>
                    <!-- Item END--></td>
            </tr>
        </table>
        <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
        <!-- padding -->
    </td></tr>
    <!--header END-->

    <!--content 1 -->
    <tr><td align="center" bgcolor="#ffffff">
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="center">
                <!-- padding -->
                
                <div style="line-height: 44px;">
                    <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 34px;">
                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 34px; color: #57697e;">
                        Welcome to the Groxer
                    </span></font>
                </div>
                <!-- padding --><div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <tr><td align="center">
                <div style="line-height: 30px;">
                    <font face="Arial, Helvetica, sans-serif" size="5" color="#4db3a4" style="font-size: 17px;">
                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 17px; color: #4db3a4;">
                        Hi&nbsp;'.$name.', your Reset Password Link!
                    </span></font>
                </div>
                <!-- padding --><div style="height: 35px; line-height: 35px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <tr><td align="center">
                        <table width="80%" align="center" border="0" cellspacing="0" cellpadding="0">
                            <tr><td align="center">
                                <div style="line-height: 24px;">
                                    <font face="Arial, Helvetica, sans-serif" size="4" color="#57697e" style="font-size: 16px;">
                                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #57697e;">
                                        <table class="container content" align="center">
        <tr>
            <td>
                <table width="594" class="row note">
                <tr>
                    <td width="586" class="wrapper last">
                        
                        <p>
                             Please click the following Link to Reset your Password:                        </p>
                        <!-- BEGIN: Note Panel -->
                        <table class="twelve columns" style="margin-bottom: 10px" >
                        
                        <tr>
                            <td class="panel" >
                                <a href="'.self::DIR_LINK.'/groxerAdmin/#/resetPasswordAuth/'.$value.'">
                                '.self::DIR_LINK.'/groxerAdmin/#/resetPasswordAuth/'.$value.'</a>   
                                
                                
                                </td>
                                
                                
                            
                        </tr>
                        
                        </table>                        
                             If clicking the URL above does not work, copy and paste the URL into a browser window.                     </p>
                                            </td>
                </tr>
                
                </table>                </td>
        </tr>
        </table>
                                    </span></font>
                                </div>
                            </td></tr>
                        </table>
                <!-- padding --><div style="height: 45px; line-height: 45px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            
        </table>        
    </td></tr>
    
    <tr><td class="iage_footer" align="center" bgcolor="#eff3f8">
        <!-- padding --><div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="center">
                <font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
                <span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
                    2015 &copy; Groxer.com.
                </span></font>              
            </td></tr>          
        </table>
        
        <!-- padding --><div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
    </td></tr>
    
</table>

</td></tr>
</table>
';


        $subject = "Reset Password Link";

        $headers = "From:Groxer <noreply@groxer.com>\r\n";
        $headers.= "MIME-Version: 1.0\r\n";
        $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $fla = mail($email, $subject, $html, $headers);
    }
    
    public function resetPasswordAuthenticationMail($email, $name, $authCode)
    {
        $html='
<style type="text/css">
html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
@media only screen and (max-device-width: 680px), only screen and (max-width: 680px) { 
    *[class="table_width_100"] {
        width: 96% !important;
    }
    *[class="border-right_mob"] {
        border-right: 1px solid #dddddd;
    }
    *[class="mob_100"] {
        width: 100% !important;
    }
    *[class="mob_center"] {
        text-align: center !important;
    }
    *[class="mob_center_bl"] {
        float: none !important;
        display: block !important;
        margin: 0px auto;
    }   
    .iage_footer a {
        text-decoration: none;
        color: #929ca8;
    }
    img.mob_display_none {
        width: 0px !important;
        height: 0px !important;
        display: none !important;
    }
    img.mob_width_50 {
        width: 40% !important;
        height: auto !important;
    }
}
.table_width_100 {
    width: 680px;
}
</style>



<div id="mailsub" class="notification" align="center">

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;"><tr><td align="center" bgcolor="#eff3f8">



<table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%" style="max-width: 680px; min-width: 300px;">
    <!--header -->
    <tr><td align="center" bgcolor="#eff3f8">
        <!-- padding -->
        <table width="96%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="left"><!-- 

                Item -->
                <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
                <div class="mob_center_bl" style="float: left; display: inline-block; width: 115px;">
                    <table class="mob_center" width="115" border="0" cellspacing="0" cellpadding="0" align="left" style="border-collapse: collapse;">
                        <tr><td align="left" valign="middle">
                            <!-- padding -->
                            <table width="115" border="0" cellspacing="0" cellpadding="0" >
                                <tr><td align="left" valign="top" class="mob_center">
                                    <!--<a href="#" target="_blank" style="color: #596167;  font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                    <font face="Arial, Helvetica, sans-seri; font-size: 35px;" size="6" color="#4db3a5">
                                    Groxer
                                    </font></a>-->
                                    <img src="'.self::DIR_LINK.'/logo/logo.png">
                                </td></tr>
                            </table>                        
                        </td></tr>
                    </table></div><!-- Item END--><!--[if gte mso 10]>
                    </td>
                    <td align="right">
                <![endif]--><!-- 

                Item -->
                
                <div class="mob_center_bl" style="float: right; display: inline-block; width: 88px;">
                    <table width="88" border="0" cellspacing="0" cellpadding="0" align="right" style="border-collapse: collapse;">
                        <tr><td align="right" valign="middle">
                            <!-- padding -->
                            
                        </td></tr>
                    </table></div>
                    <!-- Item END--></td>
            </tr>
        </table>
        <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
        <!-- padding -->
    </td></tr>
    <!--header END-->

    <!--content 1 -->
    <tr><td align="center" bgcolor="#ffffff">
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="center">
                <!-- padding -->
                
                <div style="line-height: 44px;">
                    <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 34px;">
                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 34px; color: #57697e;">
                        Welcome to the Groxer
                    </span></font>
                </div>
                <!-- padding --><div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <tr><td align="center">
                <div style="line-height: 30px;">
                    <font face="Arial, Helvetica, sans-serif" size="5" color="#4db3a4" style="font-size: 17px;">
                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 17px; color: #4db3a4;">
                        Hi&nbsp;'.$name.'!
                    </span></font>
                </div>
                <!-- padding --><div style="height: 35px; line-height: 35px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <tr><td align="center">
                        <table width="80%" align="center" border="0" cellspacing="0" cellpadding="0">
                            <tr><td align="center">
                                <div style="line-height: 24px;">
                                    <font face="Arial, Helvetica, sans-serif" size="4" color="#57697e" style="font-size: 16px;">
                                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #57697e;">
                                        <table class="container content" align="center">
        <tr>
            <td>
                <table width="594" class="row note">
                <tr>
                    <td width="586" class="wrapper last">
                        
                        <p>Your Authentication Code for Changing the Password: '.$authCode.'</p>
                                            </td>
                </tr>
                
                </table>                </td>
        </tr>
        </table>
                                    </span></font>
                                </div>
                            </td></tr>
                        </table>
                <!-- padding --><div style="height: 45px; line-height: 45px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            
        </table>        
    </td></tr>
    
    <tr><td class="iage_footer" align="center" bgcolor="#eff3f8">
        <!-- padding --><div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="center">
                <font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
                <span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
                    2015 &copy; Groxer.com.
                </span></font>              
            </td></tr>          
        </table>
        
        <!-- padding --><div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
    </td></tr>
    
</table>

</td></tr>
</table>
';
        
        $subject = "Reset Password Authentication Code";

        $headers = "From:Groxer <noreply@groxer.com>\r\n";
        $headers.= "MIME-Version: 1.0\r\n";
        $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $fla = mail($email, $subject, $html, $headers);
    }

    public function resetUpdationMail($email,$name)
    {
        $html ='
<style type="text/css">
html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
@media only screen and (max-device-width: 680px), only screen and (max-width: 680px) { 
    *[class="table_width_100"] {
        width: 96% !important;
    }
    *[class="border-right_mob"] {
        border-right: 1px solid #dddddd;
    }
    *[class="mob_100"] {
        width: 100% !important;
    }
    *[class="mob_center"] {
        text-align: center !important;
    }
    *[class="mob_center_bl"] {
        float: none !important;
        display: block !important;
        margin: 0px auto;
    }   
    .iage_footer a {
        text-decoration: none;
        color: #929ca8;
    }
    img.mob_display_none {
        width: 0px !important;
        height: 0px !important;
        display: none !important;
    }
    img.mob_width_50 {
        width: 40% !important;
        height: auto !important;
    }
}
.table_width_100 {
    width: 680px;
}
</style>



<div id="mailsub" class="notification" align="center">

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;"><tr><td align="center" bgcolor="#eff3f8">



<table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%" style="max-width: 680px; min-width: 300px;">
    <!--header -->
        <tr><td align="center" bgcolor="#eff3f8">
        <!-- padding -->
        <table width="96%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="left"><!-- 

                Item -->
                <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
                <div class="mob_center_bl" style="float: left; display: inline-block; width: 115px;">
                    <table class="mob_center" width="115" border="0" cellspacing="0" cellpadding="0" align="left" style="border-collapse: collapse;">
                        <tr><td align="left" valign="middle">
                            <!-- padding -->
                            <table width="115" border="0" cellspacing="0" cellpadding="0" >
                                <tr><td align="left" valign="top" class="mob_center">
                                   <!-- <a href="#" target="_blank" style="color: #596167;  font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                    <font face="Arial, Helvetica, sans-seri; font-size: 35px;" size="6" color="#4db3a5">
                                    Groxer
                                    </font></a> -->
                                    <img src="'.self::DIR_LINK.'/logo/logo.png">
                                </td></tr>
                            </table>                        
                        </td></tr>
                    </table></div><!-- Item END--><!--[if gte mso 10]>
                    </td>
                    <td align="right">
                <![endif]--><!-- 

                Item -->
                
                <div class="mob_center_bl" style="float: right; display: inline-block; width: 88px;">
                    <table width="88" border="0" cellspacing="0" cellpadding="0" align="right" style="border-collapse: collapse;">
                        <tr><td align="right" valign="middle">
                            <!-- padding -->
                            
                        </td></tr>
                    </table></div>
                    <!-- Item END--></td>
            </tr>
        </table>
        <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
        <!-- padding -->
    </td></tr>
    
    <tr><td align="center" bgcolor="#ffffff">
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="center">
                <!-- padding -->
                
                <div style="line-height: 44px;">
                    <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 34px;">
                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 34px; color: #57697e;">
                        Welcome to the Groxer
                    </span></font>
                </div>
                <!-- padding --><div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <tr><td align="center">
                <div style="line-height: 30px;">
                    <font face="Arial, Helvetica, sans-serif" size="5" color="#4db3a4" style="font-size: 17px;">
                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 17px; color: #4db3a4;">
                        Hi&nbsp;'.$name.'!
                    </span></font>
                </div>
                <!-- padding --><div style="height: 35px; line-height: 35px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <tr><td align="center">
                        <table width="80%" align="center" border="0" cellspacing="0" cellpadding="0">
                            <tr><td align="center">
                                <div style="line-height: 24px;">
                                    <font face="Arial, Helvetica, sans-serif" size="4" color="#57697e" style="font-size: 16px;">
                                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #57697e;">
                                        <table class="container content" align="center">
        <tr>
            <td>
                <table width="594" class="row note">
                <tr>
                    <td width="586" class="wrapper last">
                        
                        <p>
                            Your Password Changed.                      </p>
                        <!-- BEGIN: Note Panel -->
                        <table class="twelve columns" style="margin-bottom: 10px" >
                        
                        <tr>
                            <td class="panel" >
                                
                                
                                </td>
                                
                                
                            
                        </tr>
                        
                        </table>                        
                            
                                            </td>
                </tr>
                
                </table>                </td>
        </tr>
        </table>
                                    </span></font>
                                </div>
                            </td></tr>
                        </table>
                <!-- padding --><div style="height: 45px; line-height: 45px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            
        </table>        
    </td></tr>
    
    <tr><td class="iage_footer" align="center" bgcolor="#eff3f8">
        <!-- padding --><div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="center">
                <font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
                <span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
                    2015 &copy; Groxer.com.
                </span></font>              
            </td></tr>          
        </table>
        
        <!-- padding --><div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div> 
    </td></tr>
    
</table>

</td></tr>
</table>
';

    $subject = "Password Changed";

        $headers = "From:Groxer <noreply@groxer.com>\r\n";
        $headers.= "MIME-Version: 1.0\r\n";
        $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $fla = mail($email, $subject, $html, $headers);

    }

    public function getIpAddress() {

        return (empty($_SERVER['HTTP_CLIENT_IP']) ? (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ?
                                $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']) : $_SERVER['HTTP_CLIENT_IP']);
    }

    public function signIn() {
        $success = false;
        $userId = 0;

        $email = $this->_paramsData['email'];
        $pass = $this->_paramsData['pass'];

        $pass1 = hash('sha512', $pass.GROXER_SALT);

        $stmt = "select id,user_name,password from " . self::GROXER_ADMIN_LOGIN. " where user_name='$email' limit 0,1";


        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $resultSet = $prep->fetchAll();

        if (count($resultSet) > 0) 
        {
            if($resultSet[0]['password']==$pass1)
            {
            
                $loggedIn_time = date('Y-m-d H:i:s');
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $user_ip = $this->getIpAddress();

                $dataArray = array($resultSet[0]['id'],$user_agent,$user_ip,$loggedIn_time);

                $stmt = "insert into ".self::GROXER_ADMIN_LOGGED_HISTORY." (admin_id,user_agent,user_ip,loggedIn_time) values (?,?,?,?)";

                $prep = $this->_objDatabase->prepare($stmt);
                $prep->execute($dataArray);

                $success = true;
                $message = "Welcome ".$email;
                $userId = $resultSet[0]['id'];
            }
            else
            {
                $message = "Check your Username and Password";
            }
        } 
        else
        {
            $message = "Check your Username and Password";
        }


        $this->_response = array('success' => $success,'stmt'=>$resultSet[0]['password'], "message" => $message, 'userid' => $userId, 'userName' => $email);
    }

    public function logout()
    {
        $userid = $this->_paramsData['userId'];
        $created = date('Y-m-d H:i:s');

        $stmt = "select max(history_id) as history from ".self::SUPER_ADMIN_LOGGED_HISTORY." where admin_id=?";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($userid));
        $resultSet = $prep->fetchAll();

        $stmt = "update ".self::SUPER_ADMIN_LOGGED_HISTORY." set loggedOut_time=? where history_id=?";

        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute(array($created,$resultSet[0]['history']));

        $this->_response = array('success' => true);
    }

    public function changePassword() {
        $success = false;

        $userId = $this->_paramsData['userid'];

        $oldPassword = $this->_paramsData['old'];
        $newPassword = $this->_paramsData['new'];

       // $oldPassword = hash('sha512', $oldPassword . GROXER_SALT);
        //$newPassword = hash('sha512', $newPassword . GROXER_SALT);

        $stmt = "select id from " . self::GROXER_SUPERADMIN_LOGIN_TABLE_NAME. " where id=$userId and password='$oldPassword' limit 0,1";
        $prep = $this->_objDatabase->prepare($stmt);
        $prep->execute();

        $resultSet = $prep->fetchAll();

        if (count($resultSet) > 0) {
            $stmt = 'update `' . self::GROXER_SUPERADMIN_LOGIN_TABLE_NAME. '` set `password`=? where `id`=?';

            $prep = $this->_objDatabase->prepare($stmt);

            $prep->execute(array($newPassword, $userId));
            $success = true;
            $message = "Password Changed Successfully";
        } else {
            $message = "Old Password Match Failed";
            $success = false;
        }

        $this->_response = array('success' => $success, 'message' => $message);
    }

    public function passwordReset() 
    {
        $success = false;
        $email = $this->_paramsData['email'];
        $createdDate = date('Y-m-d H:i:s');
        
        $stmt="select id,user_name,reset_token_expiry from ".self::GROXER_SUPERADMIN_LOGIN_TABLE_NAME." where user_name='$email' limit 0,1";
        $prep=  $this->_objDatabase->prepare($stmt);

        $prep->execute();

        $resultSet = $prep->fetchAll();
        
        if(count($resultSet) > 0)
        {
            if($this->dateCompare($createdDate, $resultSet[0]['reset_token_expiry']))
            {
                $success=true;  
                $name = $resultSet[0]['user_name'];
                
                $authCode = substr(md5('YmdHis'),mt_rand(0,23),8);
                
                $rand = mt_rand(1, 99999);
                $value=md5($_SERVER['HTTP_USER_AGENT'].' '.  $this->getIpAddress().' '.$rand.' '.$resultSet[0]['id']);
                $message="A reset Token Valid for one hour is sent to this Email Id";
                
                $this->resetPasswordLinkMail($email, $name, $value);
                $this->resetPasswordAuthenticationMail($email, $name, $authCode);
                
                $sEndDate = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($createdDate)));
                $stmt = 'update `'.self::GROXER_SUPERADMIN_LOGIN_TABLE_NAME.'` set `reset_token`=?, `reset_token_expiry`=?, auth_code=? where `user_name`=?';

                $prep = $this->_objDatabase->prepare($stmt);

                $prep->execute(array($value, $sEndDate, $authCode, $email));
            }
            else
            {
                $message="Reset Mail Already Sent";
            }
        }
        else
        {
            $message = "This Email is not in our records";
        }
        
        $this->_response = array('success' => $success, 'message' => $message);
    }

    public function resetPasswordAuth()
    {
        $success = false;
        $token = $this->_paramsData['token'];
        $code = $this->_paramsData['code'];
        $password = $this->_paramsData['password'];
        $createdDate = date('Y-m-d H:i:s');

        $password = hash('sha512', $password . GROXER_SALT);

        $stmt = "select id,user_name,reset_token_expiry from ".self::GROXER_SUPERADMIN_LOGIN_TABLE_NAME." where reset_token='$token' and auth_code='$code'";

        $result = $this->_objDatabase->query($stmt);
        $resultSet = $result->fetch(PDO::FETCH_ASSOC);

        if($resultSet['id'] > 0)
        {
            if($this->dateCompare($createdDate, $resultSet['reset_token_expiry']))
            {
                $message = "Reset link expired. Please try forgot password";
            }
            else
            {
                $success = true;

                $stmt = "update ".self::GROXER_SUPERADMIN_LOGIN_TABLE_NAME." set reset_token=?, reset_token_expiry=?, auth_code=?, password=? where id=?";
                $prep  = $this->_objDatabase->prepare($stmt);
                $prep->execute(array('',$createdDate,'',$password,$resultSet['user_id']));

                $this->resetUpdationMail($reultSet['name'],$resultSet['name']);

                
            }
        }
        else
        {
            $message = "Invalid authentication code";
        }

        $this->_response = array("success" => $success, "message" => $message);
    }

    public function dateCompare($date1, $date2) 
    {
        $format = "Y-m-d H:i:s";

        $date1 = DateTime::createFromFormat($format, $date1);
        $date2 = DateTime::createFromFormat($format, $date2);

        return $date1 > $date2;
    }

}


$GroxerAdmin = new GroxerAdminModule();
$GroxerAdmin->getResponse();
?>