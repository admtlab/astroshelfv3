<?php
/*
Di Bao
Summer 2012
This php script is used to interact with our astroDB server
*/

require_once("../DBFunctions.php");
require_once(dirname(dirname(dirname(__FILE__))) . '/jsend/jsend.class.php');
$mysqli = connectToDB("astroDB");

error_reporting(E_ALL);
date_default_timezone_set('America/New_York');

if(isset($_POST['insert'])) {
	$user_id = $_POST['user_id'];
	$survey = $_POST['survey'];
	$query = $_POST['query'];
	
	$the_insert = "INSERT INTO `query_history` (`user_id`, `survey_name`, `querys`) VALUES (" . $user_id . ", '" . $survey . "', '" . $query . "')";
	$mysqli->query($the_insert);
} else if(isset($_GET['get_bookmarks'])) {
	$user_id = $_GET["user_id"];
	$json_msg = '{ "bookmarks": [[';
	
	#Get Object bookmarks
	$stmt = $mysqli->prepare("SELECT b.user_id, b.id, b.title, b.ts_created, o.name, o.survey_obj_id, o._RA_, o._DEC_ FROM bookmark b JOIN bookmark_of_obj bo ON b.id = bo.bookmark_id JOIN object_info o ON bo.obj_id = o.object_id WHERE b.user_id = ? OR b.user_id = 126 ORDER BY b.ts_created DESC");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($bm_user_id, $id, $title, $ts_created, $name, $obj_id, $ra, $dec);
	$num_rows = 1;
	while($stmt->fetch()) {
		$json_msg .= '{"user_id":'.$bm_user_id.', "id":'.$id.', "title":"'.$title.'", "ts_created":"'.$ts_created.'", "obj_id":"'.$obj_id.'", "name":"'.$name.'", "ra":'.$ra.', "dec":'.$dec.'}';
		if($num_rows != $stmt->num_rows) {
			$json_msg .= ",";
		}
		$num_rows++;
	}
	$json_msg .= "], [";
	
	#Get Location bookmarks
	$stmt = $mysqli->prepare("SELECT b.user_id, b.id, b.title, b.ts_created, l._RA_, l._DEC_ FROM bookmark b JOIN bookmark_of_loc l ON b.id = l.bookmark_id WHERE b.user_id = ? OR b.user_id = 126 ORDER BY b.ts_created DESC");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($bm_user_id, $id, $title, $ts_created, $ra, $dec);
	$num_rows = 1;
	while($stmt->fetch()) {
		$json_msg .= '{"user_id":'.$bm_user_id.', "id":'.$id.', "title":"'.$title.'", "ts_created":"'.$ts_created.'", "ra":'.$ra.', "dec":'.$dec.'}';
		if($num_rows != $stmt->num_rows) {
			$json_msg .= ",";
		}
		$num_rows++;
	}
	$json_msg .= "], [";
	
	#Get Annotation bookmarks
	$stmt = $mysqli->prepare("SELECT b.user_id, b.id, b.title, b.ts_created, a.anno_id, a.anno_title, a.anno_value, oi._RA_, oi._DEC_ FROM bookmark b JOIN bookmark_of_anno ba ON b.id = ba.bookmark_id JOIN annotation a ON ba.anno_id = a.anno_id LEFT OUTER JOIN anno_to_obj atoo ON a.anno_id = atoo.anno_src_id LEFT OUTER JOIN object_info oi ON atoo.obj_tar_id = oi.object_id WHERE b.user_id = ? OR b.user_id = 126 ORDER BY b.ts_created DESC");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($bm_user_id, $id, $title, $ts_created, $anno_id, $anno_title, $anno_value, $ra, $dec);
	$num_rows = 1;
	while($stmt->fetch()) {
		if(!$ra) {
			$ra = 0;
			$dec = 0;
		}
		$json_msg .= '{"user_id":'.$bm_user_id.', "id":'.$id.', "title":"'.$title.'", "ts_created":"'.$ts_created.'", "anno_id":'.$anno_id.', "anno_title":"'.$anno_title.'", "anno_value":"'.$anno_value.'", "ra":'.$ra.', "dec":'.$dec.'}';
		if($num_rows != $stmt->num_rows) {
			$json_msg .= ",";
		}
		$num_rows++;
	}
	$json_msg .= "]]}";
	echo $json_msg;
} else if(isset($_POST['add_bookmark'])) {

	$type = $_POST["type"];
	$mysqli->autocommit(false);
	$stmt = $mysqli->prepare("INSERT INTO bookmark (user_id, title, type, ts_created) VALUES(?, ?, ?, NOW())");
	$stmt->bind_param("iss", $_POST["user_id"], $_POST["title"], $type);
	if(!$stmt->execute()) {
		$mysqli->rollback();
		$mysqli->autocommit(true);
		exit;
	}
	
	$bookmark_id = $mysqli->insert_id;
	if($type == "anno") {
		$stmt = $mysqli->prepare("INSERT INTO bookmark_of_anno (bookmark_id, anno_id) VALUES(?, ?)");
		$stmt->bind_param("ii", $bookmark_id, $_POST["id"]);
	} else if($type == "loc") {
		$stmt = $mysqli->prepare("INSERT INTO bookmark_of_loc (bookmark_id, _RA_, _DEC_) VALUES(?, ?, ?)");
		$stmt->bind_param("idd", $bookmark_id, $_POST["ra"], $_POST["dec"]);
	} else if($type == "obj") {
		$obj_query_bit = "?";
		if(isset($_POST["SDSS_id"])) {
			$obj_query_bit = "(SELECT object_id FROM object_info WHERE survey_obj_id = ?)";
		}
		$stmt = $mysqli->prepare("INSERT INTO bookmark_of_obj (bookmark_id, obj_id) VALUES(?, $obj_query_bit)");
		$stmt->bind_param("ii", $bookmark_id, $_POST["id"]);
	} else {
		$mysqli->rollback();
		$mysqli->autocommit(true);
		exit;
	}
	
	if(!$stmt->execute()) {
		$mysqli->rollback();
		$mysqli->autocommit(true);
		exit;
	}
	
	$mysqli->commit();
	$mysqli->autocommit(true);
} else if(isset($_GET['get_role'])) {
	$user_id = $_GET['user_id'];
	$stmt = $mysqli->prepare("SELECT user_id FROM user WHERE user_id = ? AND role_id IN (SELECT role_id FROM user_role WHERE role_name = 'admin')");
	$stmt->bind_param("i", intval($user_id));
	if(!$stmt->execute()) {
		echo $stmt->error;
		exit;
	}
	$stmt->store_result();
	if($stmt->num_rows == 1) {
		echo 'admin';
	} else {
		echo 'user';
	}
} else if(isset($_GET['get_groupmanage_info'])) {
	$stmt = $mysqli->prepare("SELECT group_id, group_name FROM group_info");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name);
	echo '{"groups":[';
	$row = 1;
	while($stmt->fetch()) {
		echo '{"id":'.$id.', "name":"'.$name.'"}';
		if($row != $stmt->num_rows()) {
			echo ",";
		}
		$row++;
	}
	echo '], "users":[';
	
	$stmt = $mysqli->prepare("SELECT user_id, username FROM user ORDER BY username ASC");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $username);
	$row = 1;
	while($stmt->fetch()) {
		echo '{"id":'.$id.', "username":"'.$username.'"}';
		if($row != $stmt->num_rows()) {
			echo ",";
		}
		$row++;
	}
	echo "]}";
} else if(isset($_GET['add_user_to_group'])) {
	if(!isset($_GET['user_id']) || !isset($_GET['group_id'])) {
		exit;
	}
	$user = $_GET['user_id'];
	$group = $_GET['group_id'];
	$stmt = $mysqli->prepare("SELECT * FROM user_to_group WHERE user_id = ? AND group_id = ?");
	$stmt->bind_param("ii", $user, $group);
	$stmt->execute();
	$stmt->store_result();
	if($stmt->num_rows() > 0) {
		echo "User ".$_GET["user"]." already belongs to ".$_GET["group"].".";
		exit;
	}
	
	$stmt = $mysqli->prepare("INSERT INTO user_to_group(user_id, group_id) VALUES(?, ?)");
	$stmt->bind_param("ii", $user, $group);
	if(!$stmt->execute()) {
		echo "There was an error adding ".$_GET["user"]." to ".$_GET["group"].".";
		exit;
	} else {
		echo "User ".$_GET["user"]." was successfully added to ".$_GET["group"].".";
	}
} else if(isset($_POST['get_anno'])) {
	$anno_id = $_POST['anno_id'];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/" . $anno_id);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$xml_result = curl_exec($ch);
	curl_close($ch);
	
	$xml_result = str_replace(array("\n", "\r", "\t"), '', $xml_result);
	$xml_result = trim(str_replace('"', "'", $xml_result));
	$json_result = json_encode(simplexml_load_string($xml_result));
	echo $json_result;
} else if(isset($_GET['get_anno_for_user'])) {
	$user_id = $_GET['user_id'];
	
	$stmt = $mysqli->prepare("SELECT anno_id, anno_title, anno_value, ts_created FROM annotation WHERE user_id = ?");
	$stmt->bind_param("i", $user_id);
	if(!$stmt->execute()) {
		echo $mysqli->error;
	}
	$stmt->store_result();
	$stmt->bind_result($id, $title, $value, $ts);
	
	$result = '{ "annotations": [';
	$i = 0;
	while($stmt->fetch()) {
		$i++;
		$result .= '{ "id":'.$id.', "title":"'.$title.'", "value":"'.$value.'", "ts_created":"'.$ts.'" }';
		if($i < $stmt->num_rows()) {
			$result .= ', ';
		}
	}
	$result .= ' ] }';
	echo $result;
} else if(isset($_GET['get_anno_for_group'])) {
	$user_id = $_GET['user_id'];
	
	$stmt = $mysqli->prepare("SELECT anno_id, anno_title, anno_value, ts_created, group_name FROM annotation a JOIN (SELECT user_src_id, group_tar_id FROM user_belong_group WHERE group_tar_id IN (SELECT group_tar_id FROM user_belong_group WHERE user_src_id = ?)) u ON a.user_id = u.user_src_id JOIN group_info g ON g.group_id = u.group_tar_id");
	$stmt->bind_param("i", $user_id);
	if(!$stmt->execute()) {
		echo $mysqli->error;
	}
	$stmt->store_result();
	$stmt->bind_result($id, $title, $value, $ts, $group_name);
	
	$result = '{ "annotations": [';
	$i = 0;
	while($stmt->fetch()) {
		$i++;
		$result .= '{ "id":'.$id.', "title":"'.$title.'", "value":"'.$value.'", "ts_created":"'.$ts.'", "group_name":"'.$group_name.'" }';
		if($i < $stmt->num_rows()) {
			$result .= ', ';
		}
	}
	$result .= ' ] }';
	echo $result;
} else if(isset($_POST['insert_res'])){
	$userId = $_POST['record']['user_id'];
	$resType = $_POST['record']['result_type'];
	$resName = $_POST['record']['result_name'];
	$resComment = $_POST['record']['result_comment'];
	$resSize = $_POST['record']['result_size'];

	$jSEND = new jSEND();
	$resContent = $jSEND->getData($_POST['record']['result_content']); 
	$resContent = gzcompress($resContent);
	
	$stmt = $mysqli->stmt_init();
	$stmt->prepare("INSERT INTO `result_history` (`user_id`, `result_type`, `result_name`, `result_comment`, `result_size`, `result_content`) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param('isssis', $userId, $resType, $resName, $resComment, $resSize, $resContent);
	$stmt->execute();
	$stmt->close();
} else if(isset($_GET['select'])){
	$user_id = $_GET['userid'];
	$base = $_GET['base'];
	$offset = $_GET['offset'];
	//echo $user_id;exit;
	$the_query = "SELECT * FROM `query_history` WHERE `user_id` = " . $user_id . " ORDER BY `TS` DESC LIMIT " . $base . ", " . $offset;
	$res = $mysqli->query($the_query);
	$res->data_seek(0);

	$html = "";
	while($row = $res->fetch_assoc()){
		$id = $row['query_his_id'];
		foreach($row as $col => $value){
			if($col == "survey_name"){
				$html .= "<tr><td>". $value ."</td>";
			}elseif($col == "querys"){
				$html .= "<td><a href=\"#\" name=\"restore_tabs3\">" . $value . "</a><br/><br/>
				<div style=\"text-align:right\"><input type=\"button\" name=\"copy_to_direct\" value=\"copy\" style=\"width:50px\"/></div></td>";
			}elseif($col == "TS"){
				$html .= "<td>" . date("F j, Y g:i a", strtotime($value)) . "</td><td><input name=\"delete_tabs3\" type=\"checkbox\" value=". $id ."></td></tr>";
			}else{
				;
			}
		}
	}
	echo $html;
} else if(isset($_POST['select_res'])){
	$userId = $_POST['userId'];
	$content = $_POST['content'];
	
	$the_query = "SELECT * FROM `result_history` WHERE `user_id` = " . $userId . " AND `result_name` LIKE '%" . $content . "%'";
	$res = $mysqli->query($the_query);
	if($res->num_rows == 0){
		$html = "";
		echo $html;
	}else{
		$html = "";
		$res->data_seek(0);
		while($row = $res->fetch_assoc()){
			$html .= "<tr>";
			$html .= "<td>" . $row['result_type'] . "</td>";
			$html .= "<td>" . $row['result_name'] . "</td>";
			$html .= "<td>" . $row['result_comment'] . "</td>";
			$html .= "<td>" . $row['result_size'] . "</td>";
			$html .= "<td><input type='button' name='res_his_recover' value='restore' style='width:60px'/>";
			$html .= "<input type='hidden' name='res_his_id' value='" . $row['result_his_id'] . "'/></td>";
			$html .= "</tr>";
		}
		echo $html;
	}
} else if(isset($_POST['update'])){
	$id = $_POST['id'];
	
	$the_update = "DELETE FROM `query_history` WHERE `query_his_id` = " . $id;
	$mysqli->query($the_update);
} else if(isset($_POST['update_res'])){
	$resultId = $_POST['resultId'];
	$userId = $_POST['userId'];
	
	$the_query = "SELECT `result_content` FROM `result_history` WHERE `result_his_id` = " . $resultId . " AND `user_id` = " . $userId;
	$res = $mysqli->query($the_query);
	if($res->num_rows == 1){
		$res->data_seek(0);
		$row = $res->fetch_assoc();
		echo gzuncompress($row['result_content']);
	}else{
		echo "{error}";
	}
}
exit;
?>