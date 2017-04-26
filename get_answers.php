<?php
$root = $_SERVER['DOCUMENT_ROOT'];
$questionID = $_GET["qid"];

//Get all the answers data
include_once($root . "/portfolio/db.php");
$conn->select_db("scylla_poll");
 
//Insert question into DB and get question ID back 
if(!($stmt = $conn->prepare("SELECT name, votes, id FROM `answers` WHERE qid = ?"))){
  echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
}
if(!$stmt->bind_param('i', $questionID)){
  echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
}
if(!$stmt->execute()){
  echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
}

if(!$stmt->bind_result($name, $votes, $aid)){
  echo "Bind-result failed: (" . $stmt->errno . ") " . $stmt->error;
}

$answers = array();
$i = 0;
while($stmt->fetch()){
  $answers[$i][0] = $name;
  $answers[$i][1] = $votes;
  $answers[$i][2] = $aid;
  $i++;
}

$stmt->close();
$conn->close();

echo json_encode($answers);
?>