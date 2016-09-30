<?php
/**
 * Created by PhpStorm.
 * User: Ahmed Al-Ahmed
 * Date: 30/09/16
 * Time: 10:39 م
 */




namespace HotelModuleNameSpace;

require_once 'auth.php';
require_once 'dbconfig.php';

class Module
{

    /*
     * $data : id number or email string
     * $type : "id" or "email"
     */

    public static function isUserFound($data, $type){

        global $conn;
        switch ($type){
            case 'id':{
                $stmt = $conn->prepare("SELECT * FROM User WHERE userID=:ID");
                $stmt->bindValue(":ID", $data);
                $stmt->execute();
                $res = $stmt->fetchAll(2);
                if(count($res) > 0) return true;

                return false;

            }

            case 'email':{
                $stmt = $conn->prepare("SELECT * FROM User WHERE userEmail=:E");
                $stmt->bindValue(":E", $data);
                $stmt->execute();
                $res = $stmt->fetchAll(2);
                if(count($res) > 0) return true;

                return false;
            }
            default:{
                print 'not command found.';
                return false;
            }
        }
    }

    public static function userHasBooking($userID){
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM RoomBooking WHERE userID=:ID");
        $stmt->bindValue(":ID", $userID);
        $stmt->execute();
        $res = $stmt->fetchAll(2);
        if(count($res) > 0) return true;

        return false;
    }

    public static function uploadFileToRemoteServer($name, $tmp_name, $target){
        $time = time();
        $file_name = md5($name) . $time . substr($name, strlen($name)-4);
        $file_name = $target . '/' . $file_name;

        if(move_uploaded_file($tmp_name, $file_name)){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}