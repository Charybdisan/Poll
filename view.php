<?php
$title = "Poll Project - Back";
$titleURL = "/portfolio/poll";
$navLinkText = "Back to Portfolio";
$navLinkURL = "/portfolio";
$root = $_SERVER['DOCUMENT_ROOT'];
$css = "css/poll.css";
$questionID = $_GET["qid"];
$question = "...";

include_once($root . "/portfolio/header.php");

//We need to do an initial call to the DB
//to get the question associated with this question ID.
//Answers + progress bar elements will be created via an ajax call
include_once($root . "/portfolio/db.php");
mysqli_select_db($conn, "scylla_poll");
 
//Insert question into DB and get question ID back 
if(!($stmt = $conn->prepare("SELECT name FROM `questions` WHERE id = ?"))){
  echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
}
if(!$stmt->bind_param('i', $questionID)){
  echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
}
if(!$stmt->execute()){
  echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
}
if(!$stmt->bind_result($question)){
  echo "Bind-result failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->fetch();

$stmt->close();
$conn->close();

echo "<script>var qid = " . $questionID . ";</script>";
?>
<div>
    <h3><?php echo $question;?></h3>
    <p class="lead">Tap on a bar to vote for it</p>
</div>
<div id="alert" class="alert alert-info">
    <span id="span-alert">
        <strong>Warning!</strong> Best check yo self, you're not looking too good.
    </span>
</div>

<div class="jumbotron">
    <!-- Where the answers are placed -->
    <div class="jumbotron-contents">
        <p class='lead'>Votes are in real-time</p>
    </div>
</div>

<script src="js/poll_view.js"></script>

<?php
include_once($root . "/portfolio/footer.php");
?>