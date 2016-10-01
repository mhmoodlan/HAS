<?php

require_once 'auth.php';
require_once 'dbconfig.php';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if(true) {
		if(Authentication::checkInput($_POST['userID']) &&
		   Authentication::checkInput($_POST['paymentAmount'])) {
			   $stmt = $conn->prepare('SELECT roomID FROM roombooking WHERE userID = :id');
			   $stmt->bindValue(':id', $_POST['userID']);
			   $stmt->execute();
			   $rooms = $stmt->fetchAll();
			   $total = 0;
				foreach($rooms as $room) {
					$stmt = $conn->prepare('SELECT serviceID FROM roomservices WHERE roomID = :id');
					$stmt->bindValue(':id', $room['roomID']);
					$stmt->execute();
					$services = $stmt->fetchAll();
					foreach($services as $service) {
						$stmt = $conn->prepare('SELECT servicePrice FROM service WHERE serviceID = :id');
						$stmt->bindValue(':id', $service['serviceID']);
						$stmt->execute();
						$price = $stmt->fetchAll();
						$total += $price[0]['servicePrice'];
					}
				}
				$stmt = $conn->prepare('SELECT amount FROM payment WHERE userID = :uid');
				$stmt->bindValue(':uid', $_POST['userID']);
				$stmt->execute();
				$result = $stmt->fetchAll();
				$alreadyPaid = 0;
				foreach($result as $row) {
					$alreadyPaid += $row['amount'];
				}
				$total -= $alreadyPaid;
				
				if($_POST['paymentAmount'] > $total) {
					$msg = 'Too much money!';
					$status = 3;
					echo json_encode(array('status'=>$status, 'data'=>$msg));
					exit();
				}
				
				try {
					$stmt = $conn->prepare('INSERT INTO payment(userID, amount) VALUES(:uid, :amount)');
					$stmt->bindValue(':uid', $_POST['userID']);
					$stmt->bindValue(':amount', $_POST['paymentAmount']);
					$stmt->execute();
				} catch(PDOException $e) {
					$msg = $e->getMessage();
					$status = 3;
					echo json_encode(array('status'=>$status, 'data'=>$msg));
					exit();
				}
				$rest = $total - $_POST['paymentAmount'];
				$msg = "Payment done Successfuly!, User payment left : $rest  .";
				$status = 1;
				echo json_encode(array('status'=>$status, 'data'=>$msg));				
		}
	}
}

?>