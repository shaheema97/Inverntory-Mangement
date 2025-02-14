<?php
include_once "../../files/config.php";
include_once "../supplier_group/supplier_group.php";


class supplier {

   public $supplier_id;
   public $supplier_code;
   public $supplier_name;
   public $supplier_group;
   public $supplier_add;
   public $supplier_contactno;
   public $supplier_email;
   public $supplier_status;
   public $supplier_date;
    

// -----------------------------------------------------------------------------------------------------------------------

function __construct(){
    $this->db=new mysqli(host,un,pw,db1);

}

//-----INSERT-SUPPLIER------------------------------------------------------------------------------------------------------------------

function insert_suppier(){

    $CODE=$_POST["supcode"];
    $MAIL=$_POST["supemail"];
    $CONTACT=$_POST["supno"];
    $sql1= "SELECT * FROM supplier WHERE supplier_status='ACTIVE' AND supplier_code='$CODE' OR supplier_email='$MAIL' OR supplier_contactno='$CONTACT' ";
    // echo($sql1);
    $res_code=$this->db->query($sql1);

 
if($res_code->num_rows==0){
    $sql="INSERT INTO supplier (supplier_code,supplier_name,supplier_group,supplier_add,supplier_contactno,supplier_email) 
    VALUES ('$this->supplier_code','$this->supplier_name','$this->supplier_group','$this->supplier_add','$this->supplier_contactno','$this->supplier_email');";
    // echo $sql;
    $this->db->query($sql);
    return true;
}else 
{
    return false;
 }
}

// Import supplier--------------------------------------------------------------------------------------------------------
function import_supplier()
    {
        // Reads the file with name 'doc' and gives to the variable $file
        $file=$_FILES['doc']['tmp_name'];

        // Gets the extension of the file selected
        $a=pathinfo($_FILES['doc']['name'],PATHINFO_EXTENSION);
        print_r($a);
        if($a=='xlsx')
        {
            // include the class excel libraray
            require ("../import/import_excel/PHPExcel.php");
            require ("../import/import_excel/PHPExcel/IOFactory.php");
            
            // create an object     
            $obj=PHPExcel_IOFactory::load($file);
            // this function gets the data one by one and iterates
            foreach($obj->getWorksheetIterator() as $sheet)
            {   
                // echo '<pre>';
                // print_r($sheet); 

                // Get the highest row
                $higest_row=$sheet->getHighestRow();
                for($i=2;$i<=$higest_row;$i++)
                {
                    // Get the column name and the value
                    $supplier_code=$sheet->getCellByColumnAndRow(0,$i)->getValue();
                    $supplier_name=$sheet->getCellByColumnAndRow(1,$i)->getValue();
                    $supplier_group_name=$sheet->getCellByColumnAndRow(2,$i)->getValue();
                    $supplier_add=$sheet->getCellByColumnAndRow(3,$i)->getValue();
                    $supplier_contactno=$sheet->getCellByColumnAndRow(4,$i)->getValue();
                    $supplier_email=$sheet->getCellByColumnAndRow(5,$i)->getValue();

                    $sup_group=new supplier_group();
                    $supplier_group_id=$sup_group->return_sup_groupid($supplier_group_name);


                    // echo"$name";
                    if($supplier_code!='')
                    {
                        $sql1= "SELECT * FROM supplier WHERE supplier_status='ACTIVE' AND supplier_code='$CODE' OR supplier_email='$MAIL' OR supplier_contactno='$CONTACT' ";
                        // echo($sql1);
                        $res_code=$this->db->query($sql1);

                        if($res_code->num_rows==0)
                        {
                            // mysqli_query($con,"INSERT INTO test_import (name,email,age) VALUES ( '$name','$email','$age')");
                            $sql="INSERT INTO supplier (supplier_code,supplier_name,supplier_group,supplier_add,supplier_contactno,supplier_email)
                            VALUES ('$supplier_code','$supplier_name','$supplier_group_id','$supplier_add','$supplier_contactno',
                            '$supplier_email')";
                            $this->db->query($sql);
                            $msg=1;
                        }
                        else
                        {
                            $msg1=2;
                        }
                    }
                }
                if(isset($msg))
                {
                    // echo "Successful";
                        header("location:../supplier/manage_supplier.php?success=1");

                }
                if(isset($msg1))
                {
                // echo "Unsuccessful";
                    header("location:../supplier/manage_supplier.php?notsuccess=1");

                }
            }          
        }
        else 
        {
            echo "Invalid file format";
        }
    }

//---------------------------------------------------------------------------------------------------------------------

function get_all_supplier(){
    $sql="SELECT * FROM supplier WHERE supplier_status='ACTIVE' ";
    //echo $sql;
    $result=$this->db->query($sql);
    $supplier_array=array();

    while($row=$result->fetch_array()){

        $supplier_item=new supplier();
     

        $supplier_item->supplier_id=$row["supplier_id"];
        $supplier_item->supplier_code=$row["supplier_code"];
        $supplier_item->supplier_name=$row["supplier_name"];
         $supplier_item->supplier_group=$row["supplier_group"];

       

        $supplier_item->supplier_add=$row["supplier_add"];
        $supplier_item->supplier_contactno=$row["supplier_contactno"];
        $supplier_item->supplier_email=$row["supplier_email"];
        $supplier_item->supplier_status=$row["supplier_status"];
        $supplier_item->supplier_date=$row["supplier_date"];

        $supplier_array[]=$supplier_item;
    }

    return $supplier_array;
}

// --------------------------------------------------------------------------------------------------------------------------


// ----------------------------------------------------------------------------------------------------------------------
function get_supplier_by_id($supplierid){
    $sql="SELECT * FROM supplier WHERE supplier_id=$supplierid";
    //echo $sql;
    $result=$this->db->query($sql);

   


    $row=$result->fetch_array();
    
        $supplier_item=new supplier();
        $supplier_group1 = new supplier_group();

        $supplier_item->supplier_id=$row["supplier_id"];
        $supplier_item->supplier_code=$row["supplier_code"];
        $supplier_item->supplier_name=$row["supplier_name"];

        $supplier_item->supplier_group=$row["supplier_group"];
        $supplier_item->supplier_group=$supplier_group1->get_supplier_group_by_id($row["supplier_group"]);

        $supplier_item->supplier_add=$row["supplier_add"];
        $supplier_item->supplier_contactno=$row["supplier_contactno"];
        $supplier_item->supplier_email=$row["supplier_email"];

        $supplier_item->supplier_status=$row["supplier_status"];
        $supplier_item->supplier_date=$row["supplier_date"];

       
    return $supplier_item;
}

// ----------------------------------------------------------------------------------------------------------------------------
function edit_supplier($supplierid){

    // $MAIL=$_POST["supemail"];
    // $CONTACT=$_POST["supno"];
    // $sql1= "SELECT * FROM supplier WHERE supplier_status='ACTIVE' AND  supplier_email='$MAIL' OR supplier_contactno='$CONTACT' ";
    // echo($sql1);
    // $res_code=$this->db->query($sql1);
    // if($res_code->num_rows==0){
        $sql="UPDATE supplier SET supplier_name='$this->supplier_name' WHERE supplier_id=$supplierid";
        echo $sql;
        $this->db->query($sql);
        return true;
    // }else {
    //     return false;
    //  }
   
    
}

//----------------------------------------------------------------------------------------------------------------------------
function delete_supplier($supplierid){

   
        $sql="UPDATE supplier SET supplier_status='INACTIVE' WHERE supplier_id=$supplierid ";
        $this->db->query($sql);
        return true;


}

//----------------------------------------------------------------------------------------------------------------------------
function get_supplier_by_code($suppliercode){
    $sql="SELECT * FROM supplier WHERE supplier_code='$suppliercode'";
   // echo $sql;
    $result=$this->db->query($sql);

    // $supplier_array=array();

    $row=$result->fetch_array();
        $supplier_item=new supplier();
        
        $supplier_item->supplier_id=$row["supplier_id"];
        $supplier_item->supplier_code=$row["supplier_code"];
        $supplier_item->supplier_name=$row["supplier_name"];
        

       
    return $supplier_item;
}

//----------------------------------------------------------------------------------------------------------------------------
function get_supplier_by_mail($suppliermail){
    $sql="SELECT * FROM supplier WHERE supplier_email='$suppliermail'";
    //echo $sql;
    $result=$this->db->query($sql);



    $row=$result->fetch_array();
        $supplier_item=new supplier();
        $supplier_item->supplier_id=$row["supplier_id"];
        $supplier_item->supplier_code=$row["supplier_code"];
        $supplier_item->supplier_name=$row["supplier_name"];
        $supplier_item->supplier_contactno=$row["supplier_contactno"];
        $supplier_item->supplier_email=$row["supplier_email"];

       
    return $supplier_item;
}
//...................................................................................................................................

function get_supplier_by_contact($suppliercontact){
    $sql="SELECT * FROM supplier WHERE supplier_contactno='$suppliercontact'";
    //echo $sql;
    $result=$this->db->query($sql);



    $row=$result->fetch_array();
        $supplier_item=new supplier();
        $supplier_item->supplier_id=$row["supplier_id"];
        $supplier_item->supplier_code=$row["supplier_code"];
        $supplier_item->supplier_name=$row["supplier_name"];
        $supplier_item->supplier_contactno=$row["supplier_contactno"];
        $supplier_item->supplier_email=$row["supplier_email"];

       
    return $supplier_item;
}





}















?>