<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 28/09/16
 * Time: 12:10 ص
 */


include_once 'dbconfig.php';
include_once 'auth.php';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');



if($_SERVER['REQUEST_METHOD'] == 'GET') {

    if(isset($_GET['sectionID']) && !empty($_GET['sectionID']) ){


        if(isset($_GET['cmd']) && $_GET['cmd'] == 'get_all_rooms') {

            try{
                $stmt = $conn->prepare("SELECT * FROM Room WHERE sectionID=:ID");
                $stmt->bindValue(":ID", $_GET['sectionID']);

                $stmt->execute();

                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);





                echo json_encode(array("status"=>1, "data"=>$res));
            }catch (PDOException $e){
                echo json_encode(array("status"=>0, "data"=>''));
            }
        }

        if(isset($_GET['cmd']) && $_GET['cmd'] == 'get_room_by_id' && isset($_GET['roomID'])
            && ! empty($_GET['roomID']) && ! Authentication::isSafeID($_GET['roomID'])) {

            try{
                $stmt = $conn->prepare("SELECT * FROM Room WHERE sectionID=:ID AND roomID=:RID");
                $stmt->bindValue(":ID", $_GET['sectionID']);
                $stmt->bindValue(":RID", $_GET['roomID']);

                $stmt->execute();

                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);





                echo json_encode(array("status"=>1, "data"=>$res));
            }catch (PDOException $e){
                echo json_encode(array("status"=>0, "data"=>''));
            }
        }




    }





}




if($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(isset($_POST['adminID']) && isset($_POST['token'])) {

        $msg = '';
        $status = 0;


        if (!Authentication::isSafeID($_POST['adminID'])) {
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


        # Add new Room ...

        if(isset($_POST['sectionID']) && ! empty($_POST['sectionID']) &&
            Authentication::isSafeID($_POST['sectionID'])){




            }





    }



}



?>