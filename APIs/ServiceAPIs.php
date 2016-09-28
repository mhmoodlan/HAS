<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25/09/16
 * Time: 08:11 م
 */




require 'dbconfig.php';
include_once 'auth.php';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');




    if($_SERVER['REQUEST_METHOD'] == 'GET'){



        # Get all services.

        if(isset($_GET['cmd'])  &&
            !empty($_GET['cmd'])){


            $cmd = $_GET['cmd'];

            switch ($cmd){

                case 'all_services':{
                    $stmt = $conn->prepare('SELECT * FROM Service');
                    $stmt->execute();

                    $result = $stmt->fetchAll();

                    $arr = [];
                    foreach ($result as $row){

                        $arr[] = array('serviceID'=>$row['serviceID'],
                            'serviceName' => $row['serviceName'],
                            'serviceDescription'=>$row['serviceDescription'],
                            'servicePrice' => $row['servicePrice']);
                    }
                    echo json_encode(array("status" => 1, "data" => $arr));
                    exit();
                    break;

                }


                case 'service_by_id':{
                    if(! isset($_GET['sid']) || empty($_GET['sid']) ||
                        !Authentication::isSafeID($_GET['sid']))


                    $id = $_GET['sid'];
                    $stmt = $conn->prepare("SELECT * FROM Service WHERE serviceID = :id");
                    $stmt->bindValue(":id", $id);
                    $stmt->execute();

                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(array("status" => 1, "data" => $result));

                    break;

                }




            }


        }



    }





    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        if(isset($_POST['adminID']) && isset($_POST['token'])) {

            $msg = '';
            $status = 0;


            if(! Authentication::isSafeID($_POST['adminID'])) {
                $msg = 'Admin ID is not valid .';
                $status = 0;
                echo json_encode(array("status" => $status, "message" => $msg ));
                exit(0);
            }

            $auth = new Authentication($_POST['adminID']);

            if(! $auth->checkToken($_POST['token'])) {
                $msg = 'Token is not valid .';
                $status = 2;
                echo json_encode(array("status" => $status, "message" => $msg ));
                exit(0);
            }





            if(isset($_POST['serviceName']) && isset($_POST['serviceDescription']) &&
                isset($_POST['servicePrice']) && !empty($_POST['serviceName']) && !empty($_POST['serviceDescription']) &&
                !empty($_POST['servicePrice']) && isset($_POST['cmd']) && $_POST['cmd'] == 'add'){






                try{

                    $stmt = $conn->prepare("INSERT INTO Service(ServiceName, ServiceDescription, ServicePrice)VALUES(:N,:D,:P)");


                    $stmt->bindValue(":N", $_POST['serviceName']);
                    $stmt->bindValue(":D", $_POST['serviceDescription']);
                    $stmt->bindValue(":P", $_POST['servicePrice']);

                    $stmt->execute();
                    $msg = 'Service added successfully.';
                    $status = 1;
                    echo json_encode(array("status" => $status, "message" => $msg ));

                }catch (PDOException $e){
                    $msg = $e->getMessage();
                    $status = 3;
                    echo json_encode(array("status" => $status, "message" => $msg ));

                }







            }



            else  if(isset($_POST['serviceName']) && isset($_POST['serviceDescription']) && isset($_POST['serviceID'])
                && !empty($_POST['serviceID']) &&
                Authentication::isSafeID($_POST['serviceID']) &&
                isset($_POST['servicePrice']) && !empty($_POST['serviceName']) && !empty($_POST['serviceDescription']) &&
                !empty($_POST['servicePrice']) && isset($_POST['cmd']) && $_POST['cmd'] == 'update'){






                try{

                    $stmt = $conn->prepare("UPDATE Service SET serviceName=:N, serviceDescription=:D, servicePrice=:P WHERE serviceID=:ID");


                    $stmt->bindValue(":N", $_POST['serviceName']);
                    $stmt->bindValue(":D", $_POST['serviceDescription']);
                    $stmt->bindValue(":P", $_POST['servicePrice']);
                    $stmt->bindValue(":ID", $_POST['serviceID']);

                    $stmt->execute();
                    $msg = 'Service updated successfully.';
                    $status = 1;
                    echo json_encode(array("status" => $status, "message" => $msg ));
                    exit(0);
                }catch (PDOException $e){
                    $msg = $e->getMessage();
                    $status = 3;
                    echo json_encode(array("status" => $status, "message" => $msg ));
                    exit(0);
                }







            }



            else if (isset($_POST['serviceID']) && !empty($_POST['serviceID']) &&
                Authentication::isSafeID($_POST['serviceID']) && isset($_POST['cmd']) && $_POST['cmd']=='delete'){

                try{

                    $stmt = $conn->prepare("DELETE FROM Service WHERE serviceID=:ID");



                    $stmt->bindValue(":ID", $_POST['serviceID']);

                    $stmt->execute();
                    $msg = 'Service deleted successfully.';
                    $status = 1;
                    echo json_encode(array("status" => $status, "message" => $msg ));

                }catch (PDOException $e){
                    $msg = $e->getMessage();
                    $status = 3;
                    echo json_encode(array("status" => $status, "message" => $msg ));

                }

            }
        }






    }





# 1474994429-8b3c979c30244f0d2a4c865098ab5ac



?>