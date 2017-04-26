<?php
$root = $_SERVER['DOCUMENT_ROOT'];
$questionID = $_GET["qid"];
$answerIDReq = $_GET['aidreq']; //The answer the user supposedly wants to vote for
$null = NULL; //Was used for sending binary IP data, now IP is string
$cookie_name = "voted" . $questionID;

$ipStr = $_SERVER['REMOTE_ADDR'];
$ip = $ipStr;

include_once($root . "/portfolio/db.php");
$conn->select_db("scylla_poll");

$okToInsertVote = true;

//Check if the answer the person wants to vote for is in this question
if(($stmt = $conn->prepare("SELECT id FROM `answers` WHERE id = ? AND qid = ?")) === false){
  echo "CHECKANSWER Prepare failed: (" . $conn->errno . ") " . $conn->error;
}
if(($stmt->bind_param('ii', $answerIDReq, $questionID)) === false){
  echo "CHECKANSWER Bind failed: (" . $stmt->errno . ") " . $stmt->error;
}

if(($stmt->execute()) === false){
  echo "CHECKANSWER Execute failed: (" . $conn->errno . ") " . $conn->error;
}

$stmt->store_result();
$stmt->fetch();
$numAnswerRows = $stmt->num_rows;

$stmt->close();

if($numAnswerRows <= 0){
  echo "This answer does not exist in this question!";
}
else{
  //Check if our IP exists
  if(($stmt0 = $conn->prepare("SELECT ip FROM `ips_answered` WHERE ip = ? AND qid = ?")) === false){
    echo "CHECKIP Prepare failed: (" . $conn->errno . ") " . $conn->error;
  }
  if(($stmt0->bind_param('si', $ip, $questionID)) === false){
    echo "CHECKIP Bind failed: (" . $stmt0->errno . ") " . $stmt0->error;
  }
  //$stmt->send_long_data(0, $ip);

  if(($stmt0->execute()) === false){
    echo "CHECKIP Execute failed: (" . $conn->errno . ") " . $conn->error;
  }

  $stmt0->store_result();
  $stmt0->fetch();
  $numIPRows = $stmt0->num_rows;

  $stmt0->close();

  $cookieSet = isset($_COOKIE[$cookie_name]);

  if($numIPRows <= 0 && !$cookieSet){
    //Insert IP
    if(($stmt1 = $conn->prepare("INSERT INTO `ips_answered` (ip, qid) VALUES(?,?)")) === false){
      echo "INSERT_IP Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
      
    if(!$stmt1->bind_param('si', $ip, $questionID)){
      echo "INSERT_IP Bind failed: (" . $stmt1->errno . ") " . $stmt1->error;
    }
     
    if(!$stmt1->execute()){
      echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
    }
   
    $stmt1->close();
    //Increase votes on the answer by 1
    if(!($stmt2 = $conn->prepare("UPDATE `answers` SET votes = votes + 1 WHERE id = ?"))){
      echo "INCR VOTES Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
    if(!$stmt2->bind_param('i', $answerIDReq)){
      echo "INCR VOTES Bind failed: (" . $stmt2->errno . ") " . $stmt2->error;
    }
    if(!$stmt2->execute()){
      echo "INCR VOTES Execute failed: (" . $conn->errno . ") " . $conn->error;
    }
    $stmt2->close();
    
    //Set the cookie so it remembers
    setcookie(
      $cookie_name,
      1,
      time() + (10 * 365 * 24 * 60 * 60)
    );
    
    echo "OK";

  }
  else{
    echo "You have already voted for this!";
  }
  
}
$conn->close();

?>