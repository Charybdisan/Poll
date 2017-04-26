<?php
$root = $_SERVER['DOCUMENT_ROOT'];
$answers = array();
$noBlanks = 0;
$qid = 0;

//Go through each poll answer, filtering out others
//and check if we have enough non-blank answers to go forward
foreach($_POST as $key => $val){
  if($key != "question" && $key != "duplicate" && $key != "captcha" && $val != ""){
    $noBlanks++;
    $answers[] = $val;
  }
}

//If not enough, we can't go forward
if($noBlanks < 2){
  echo "Error: Too many blank answers";
}
//Question can't be blank either
else if($_POST["question"] == ""){
  echo "Error: Blank Question";
}
else{
  //All good! Connect to the database so we can 
  //insert data for the new question and answers tied to it
  include_once($root . "/portfolio/db.php");
  mysqli_select_db($conn, "scylla_poll");
 
  //Insert question into DB and get question ID back 
  if(!($stmt = $conn->prepare("INSERT INTO questions (name) VALUES (?)"))){
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
  }
  if(!$stmt->bind_param('s', $_POST['question'])){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  if(!$stmt->execute()){
    echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
  }
  $qid = $conn->insert_id;
  
  //Insert answers into DB along with question ID into each one
  foreach($answers as $key => $val){
    if(!($stmt = $conn->prepare("INSERT INTO answers (qid, name) VALUES (?, ?)"))){
      echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
      die();
    }
    if(!$stmt->bind_param('is', $qid, $val)){
      echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
      die();
    }
    if(!$stmt->execute()){
      echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
      die();
    }
  }
  
  $stmt->close();
  $conn->close();
  
  
  
}
?>

<script type="text/javascript">
    window.location = "view.php?qid=" + <?php echo $qid;?>;
</script>