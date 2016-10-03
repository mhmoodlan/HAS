<?php
	
require 'dbconfig.php';
include_once 'auth.php';


header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	if(isset($_GET['cmd']) && !empty($_GET['cmd'])) {
		$cmd = $_GET['cmd'];
		
		switch($cmd) {
			// get All sections 
			case 'all_sections' : {
				$stmt = $conn->prepare('SELECT * FROM section');
				$stmt->execute();
				$result = $stmt->fetchAll();
				$arr = [];
				foreach($result as $row) {
					$arr[] = array('sectionID'=>$row['sectionID'],
								   'sectionName'=>$row['sectionName'],
								   'sectionDescription'=>$row['sectionDescription'],
								   'sectionRate'=>$row['sectionRate']);
				}
				echo json_encode(array('status' => 1, 'data' =>$arr));
				exit();
				break; 
			}
			// get section by id
			case 'section_by_id' : {
				if( isset($_GET['sid']) && !empty($_GET['sid']) &&  Authentication::isSafeID($_GET['sid'])) {
					$id = $_GET['sid'];
					$stmt = $conn->prepare("SELECT * FROM section WHERE sectionID = :id");
					$stmt->bindValue(":id", $id);
					$stmt->execute();

					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
					echo json_encode(array("status" => 1, "data" => $result));
				}
				break;
			}
			//get All sections images
			case 'all_sections_img': {
				$stmt = $conn->prepare('SELECT * FROM sectionImage ORDER BY sectionID');
				$stmt->execute();
				$result = $stmt->fetchAll();
				$arr = [];
				foreach($result as $row) {
					$arr[] = array('sectionID'=>$row['sectionID'],
					               'sectionImage'=>$row['sectionImage']);
				}
				echo json_encode(array('status' => 1, 'data'=>$arr));
				exit();
				break;
			}
			// get section images
			case 'section_img' : {
				if(isset($_GET['sid']) && !empty($_GET['sid']) && Authentication::isSafeID($_GET['sid'])) {
					$stmt = $conn->prepare('SELECT * FROM sectionImages WHERE sectionID = :id');
					$stmt->bindValue(':id', $_GET['sid']);
					$stmt->execute();
					$result = $stmt->fetchAll();
					$arr = [];
					foreach($result as $row) {
						$arr[] = array('sectionID' =>$row['sectionID'],
									   'imageID'=>$row['imageID'],
									   'sectionImage'=>$row['sectionImage']);
					}
					
					echo json_encode(array('status'=>1,'data'=>$arr));
					exit();
				}
				break;
			}
			// default
			default:
				print 'no such command!';
			break;
		}
	}
}




// handling POST requests
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


	if(isset($_POST['sectionName']) && isset($_POST['sectionDescription']) &&
		isset($_POST['sectionRate']) && !empty($_POST['sectionName']) && !empty($_POST['sectionDescription']) &&
		!empty($_POST['sectionRate']) && isset($_POST['cmd']) && $_POST['cmd'] == 'add'){

		try{

			$stmt = $conn->prepare("INSERT INTO section(sectionName, sectionDescription, sectionRate)VALUES(:N,:D,:P)");


			$stmt->bindValue(":N", $_POST['sectionName']);
			$stmt->bindValue(":D", $_POST['sectionDescription']);
			$stmt->bindValue(":P", $_POST['sectionRate']);

			$stmt->execute();
			$msg = 'Section added successfully.';
			$status = 1;
			echo json_encode(array("status" => $status, "message" => $msg ));

		}catch (PDOException $e){
			$msg = $e->getMessage();
			$status = 3;
			echo json_encode(array("status" => $status, "message" => $msg ));
		}
	}
	
	else  
		if(isset($_POST['sectionName']) && isset($_POST['sectionDescription']) && isset($_POST['sectionID']) &&
			!empty($_POST['sectionID']) &&
			Authentication::isSafeID($_POST['sectionID']) &&
			isset($_POST['sectionRate']) && !empty($_POST['sectionName']) && !empty($_POST['sectionDescription']) &&
			!empty($_POST['sectionRate']) && isset($_POST['cmd']) && $_POST['cmd'] == 'update'){
				
			try {

				$stmt = $conn->prepare("UPDATE section SET sectionName=:N, sectionDescription=:D, sectionRate=:P WHERE sectionID=:ID");


				$stmt->bindValue(":N", $_POST['sectionName']);
				$stmt->bindValue(":D", $_POST['sectionDescription']);
				$stmt->bindValue(":P", $_POST['sectionRate']);
				$stmt->bindValue(":ID", $_POST['sectionID']);

				$stmt->execute();
				$msg = 'Section updated successfully.';
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

	else 
		if (isset($_POST['sectionID']) && !empty($_POST['sectionID']) &&
			Authentication::isSafeID($_POST['sectionID']) && isset($_POST['cmd']) && $_POST['cmd']=='delete'){

			try{
				$stmt = $conn->prepare("DELETE FROM section WHERE sectionID=:ID");
				$stmt->bindValue(":ID", $_POST['sectionID']);
				$stmt->execute();
				$msg = 'Section deleted successfully.';
				$status = 1;
				echo json_encode(array("status" => $status, "message" => $msg ));
			}catch (PDOException $e){
				$msg = $e->getMessage();
				$status = 3;
				echo json_encode(array("status" => $status, "message" => $msg ));
			}
		}
	
	else // Add image to section
		if(isset($_POST['sectionID']) && !empty($_POST['sectionID']) &&
		   Authentication::isSafeID($_POST['sectionID']) && isset($_POST['cmd']) && $_POST['cmd']=='add_img' 
		   && isset($_FILES['sectionImg']) && !empty($_FILES['sectionImg'])) {
			   
		   if($_FILES['sectionImg']['type'] == 'image/jpeg') {
			   $source = $_FILES['sectionImg']['tmp_name'];
			   $target = '../uploads/'. $_FILES['sectionImg']['name'];
			   move_uploaded_file($source, $target) or die ("Couldn't Copy");
			   
			   try {
				$stmt = $conn->prepare('INSERT INTO sectionImages(sectionID, sectionImage) VALUES(:id, :name)');
				$stmt->bindValue(":id", $_POST['sectionID']);
				$stmt->bindValue(":name", $_FILES['sectionImg']['name']);
				

				$stmt->execute();
				$msg = 'Section Image added successfully.';
				$status = 1;
				echo json_encode(array("status" => $status, "message" => $msg ));
			   } catch(PDOException $e) {
					$msg = $e->getMessage();
					$status = 3;
					echo json_encode(array("status" => $status, "message" => $msg ));
			   }
		   } else {
			   $msg = 'Image type not supported';
				$status = 3;
				echo json_encode(array("status" => $status, "message" => $msg ));
		   }
		}
	else // delete section image by id
		if(isset($_POST['imageID']) && !empty($_POST['imageID']) &&
		   Authentication::isSafeID($_POST['imageID']) && isset($_POST['cmd']) && $_POST['cmd']=='delete_img') {
			$stmt = $conn->prepare('SELECT sectionImage FROM sectionImages WHERE imageID = :id');
			$stmt->bindValue(":id", $_POST['imageID']);
			$stmt->execute();
			$result = $stmt->fetchAll();
			$imageName = '../uploads/'. $result[0]['sectionImage'];
			
			if(unlink($imageName)) {
				try {
					$stmt = $conn->prepare('DELETE FROM sectionImages WHERE imageID = :id');
					$stmt->bindValue(':id', $_POST['imageID']);
					$stmt->execute();
				} catch(PDOException $e) {
					$msg = $e->getMessage();
					$status = 3;
					echo json_encode(array('status'=>$status, 'data'=>$msg));
					exit();
				}
				$msg = 'Image deleted successfuly ';
				$status = 1;
				echo json_encode(array('status'=>$status, 'data'=>$msg));
				exit();
			} else {
				$err = "Error deleting image!";
				$status = 3;
				echo json_encode(array('status'=>$status, 'data'=>$err));
				exit();
			}
		}
	} 
}
?>
