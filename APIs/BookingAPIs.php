<?php

require_once 'auth.php';
require_once 'dbconfig.php';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(Authentication::isLoggedIn()){
		if(isset($_POST['userID']) && !empty($_POST['userID']) && Authentication::isSafeID($_POST['userID']) &&
		   isset($_POST['roomID']) && !empty($_POST['roomID']) && Authentication::isSafeID($_POST['roomID']) &&
		   isset($_POST['fromDate']) && !empty($_POST['fromDate']) &&
		   isset($_POST['toDate']) && !empty($_POST['toDate']) &&
		   isset($_POST['cmd']) && !empty($_POST['cmd']) && $_POST['cmd'] == 'add_booking') {
			
			try {
				$fromDate = date('Y-m-d', strtotime($_POST['fromDate']));
				$toDate = date('Y-m-d', strtotime($_POST['toDate']));
				$stmt = $conn->prepare("INSERT INTO roombooking VALUES(:rid, :uid, :fd, :td)");
				$stmt->bindValue(':rid', $_POST['roomID']);
				$stmt->bindValue(':uid', $_POST['userID']);
				$stmt->bindValue(':fd', $fromDate);
				$stmt->bindValue(':td', $toDate);
				$stmt->execute();
				
			} catch(PDOException $e) {
				$msg = $e->getMessage();
				$status = 3;
				echo json_encode(array('status'=>$status, 'data'=>$msg));
				exit();
			}
			
			$msg = 'Booking Added successfuly!';
			$status = 1;
			echo json_encode(array('status'=>$status, 'data'=>$msg));
			exit();
		} else // handling cancel booking 
			if(Authentication::checkInput($_POST['userID']) &&
			   Authentication::checkInput($_POST['roomID']) &&
			   Authentication::checkInput($_POST['cmd']) && $_POST['cmd'] == 'delete_booking') {
				try {
					$stmt = $conn->prepare('DELETE FROM roombooking WHERE roomID = :rid AND userID = :uid');
					$stmt->bindValue(':rid', $_POST['roomID']);
					$stmt->bindValue(':uid', $_POST['userID']);
					$stmt->execute();
				} catch(PDOException $e ) {
					$msg = $e->getMessage();
					$status = 3;
					echo json_encode(array('status'=>$status, 'data'=>$msg));
					exit();
				}
				$msg = 'Booking deleted successfuly!';
				$status = 1;
				echo json_encode(array('status'=>$status, 'data'=>$msg));
				exit();
			} else {
				$msg = 'Some info missing!';
				$status = 3;
				echo json_encode(array('status'=>$status, 'data'=>$msg));
				exit();
			}			
		
	
		
	} else {
		$msg = 'User not logged in!';
		$status = 3;
		echo json_encode(array('status'=>$status, 'data'=>$msg));
		exit();
	}
}

?>
