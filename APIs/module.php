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
    public static function getAllUserRoles(){
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM UserRole");
        $stmt->execute();
        $res = $stmt->fetchAll(2);
        return $res;
    }
    public static function updateRoleText($id, $text){
        global $conn;
        $stmt = $conn->prepare("UPDATE UserRole SET roleText=:T WHERE roleID=:ID");
        $stmt->bindValue(":T", $text);
        $stmt->bindValue(":ID", $id);
        $stmt->execute();
    }
    public static function getRoleText($id){
        global $conn;
        $stmt = $conn->prepare("SELECT roleText FROM UserRole WHERE roleID=:ID");
        $stmt->bindValue(":ID", $id);
        $stmt->execute();
        $rs = $stmt->fetchAll(2);
        return $rs[0]["roleText"];
    }
    public static function removeRole($text){
        global $conn;
        $stmt = $conn->prepare("DELETE FROM UserRole WHERE roleText=:T");
        $stmt->bindValue(":T", $text);
        $stmt->execute();
    }
    public static function addNewRole($text){
        global $conn;
        $s = $conn->prepare("INSERT INTO UserRole(roleText) VALUES(:T) ");
        $s->bindValue(":T", $text);
        $s->execute();
    }
}