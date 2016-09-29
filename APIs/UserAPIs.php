<?php
/**
 * Created by PhpStorm.
 * User: Ahmed Al-Ahmed
 * Date: 28/09/16
 * Time: 09:01 م
 * test..
 */
    require_once 'auth.php';
    require_once 'dbconfig.php';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');


    function securityCode(){
        # Security Code ..
        if(isset($_POST['adminID']) && isset($_POST['token'])) {

            $msg = '';
            $status = 0;



            if (!Authentication::isSafeID($_POST['adminID']) && (Authentication::getUserType($_POST['adminID']) != 'admin') ) {
                $msg = 'Admin ID is not valid .';
                $status = 0;
                echo json_encode(array("status" => $status, "message" => $msg));
                exit(0);
            }

            $auth = new Authentication($_POST['adminID']);

            if (!$auth->checkToken($_POST['token'])) {
                $msg = 'Token is not valid .';
                $status = 2;
                echo json_encode(array("status" => $status, "message" => $msg));
                exit(0);
            }
        }
    }


# 1475159173-8b3c979c30244f0d2a4c865098ab5ac


    if($_SERVER['REQUEST_METHOD'] == 'POST'){



        if(isset($_POST['cmd']) && $_POST['cmd'] == 'add_new_client'){

            if(Authentication::checkInput($_POST['fname']) &&
                Authentication::checkInput($_POST['lname']) &&
                Authentication::checkInput($_POST['email']) &&
                Authentication::checkInput($_POST['pass']) &&
                Authentication::checkInput($_POST['userCol']) &&
                Authentication::checkInput($_POST['userType']) &&
                Authentication::isSafeID($_POST['userType'])){


                try{

                    $q = "INSERT INTO User(userFName, userLName, userEmail,userPassword, userType, Usercol)";
                    $q .= "VALUES(:FN, :LN,:E ,:P, :T, :C)";
                    $stmt = $conn->prepare($q);

                    $stmt->bindValue(":FN", $_POST['fname']);
                    $stmt->bindValue(":LN", $_POST['lname']);
                    $stmt->bindValue(":P", $_POST['pass']);
                    $stmt->bindValue(":E", $_POST['email']);
                    $stmt->bindValue(":T", $_POST['userType']);
                    $stmt->bindValue(":C", $_POST['userCol']);


                    $stmt->execute();
                    echo json_encode(array("status"=>1, "msg"=>"New User Added."));
                    exit(0);

                }catch (PDOException $e){
                    echo json_encode(array("status"=>0, "msg"=>$e->getMessage()));
                    exit(0);
                }




            }



        }


        if(isset($_POST['cmd']) && $_POST['cmd'] == 'remove_user'){

            if(isset($_POST['userID']) && !empty($_POST['userID'])
                && Authentication::isSafeID($_POST['userID'])){
                $userRole = strtolower(Authentication::getUserType($_POST['adminID']));
                securityCode();
                try{

                    $stmt = $conn->prepare("DELETE FROM User WHERE userID=:ID");
                    $stmt->bindValue(":ID", $_POST['userID']);
                    $stmt->execute();

                    echo json_encode(array("status"=>1, "msg"=>"user has removed."));
                    exit(1);

                }catch (PDOException $e){
                    echo json_encode(array("status"=>0, "msg"=>$e->getMessage()));
                    exit(0);
                }

            }

        }



        if(isset($_POST['cmd']) && $_POST['cmd'] == 'login'){
            if(Authentication::checkInput($_POST['email']) && Authentication::checkInput($_POST['pass'])){

                $stmt = $conn->prepare("SELECT * FROM User WHERE userEmail=:E AND userPassword=:P");
                $stmt->bindValue(":E", $_POST['email']);
                $stmt->bindValue(":P", $_POST['pass']);

                $stmt->execute();

                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(count($res) > 0){
                    $user_id = $res['userID'];
                    $auth = new Authentication($user_id);
                    $token = $auth->getToken();

                    echo json_encode(array("status"=>1, "id" => $user_id, "token"=>$token));
                    exit(1);
                }else{
                    echo json_encode(array("status"=>0,"msg"=>"username or password is wrong."));
                }

            }else{
                echo json_encode(array("status"=>2,"msg"=>"parameters are invalid."));
            }
        }

    }




    else
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            if(isset($_GET['cmd'])) {


                switch ($_GET['cmd']){

                    case 'get_user_by_id' :{

                        
                        if(Authentication::checkInput($_GET['userID'])
                            && Authentication::isSafeID($_GET['userID'])){
                            $stmt = $conn->prepare("SELECT * FROM User WHERE userID=:ID");
                            $stmt->bindValue(":ID", $_GET['userID']);
                            $stmt->execute();

                            echo json_encode(array("status"=>1, "data"=>$stmt->fetchAll(PDO::FETCH_ASSOC)));
                            exit(1);

                        }else{
                            echo json_encode(array("status"=>0, "msg"=>"User ID is not valid."));
                            exit(1);
                        }

                    }

                    case 'get_all_users' : {
                        $stmt = $conn->prepare("SELECT * FROM User ");
                        $stmt->execute();
                        echo json_encode(array("status"=>1, "data"=>$stmt->fetchAll(PDO::FETCH_ASSOC)));
                        exit(1);
                    }

                    default: {
                        echo json_encode(array("status"=>2, "msg"=>"command not found."));
                        exit(1);
                    }


                }













            }




        }













?>