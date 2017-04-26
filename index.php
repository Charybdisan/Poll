<?php
$title = "Poll Project";
$titleURL = "/portfolio/poll";
$navLinkText = "Back to Portfolio";
$navLinkURL = "/portfolio";
$root = $_SERVER['DOCUMENT_ROOT'];
$css = "css/poll.css";

include_once($root . "/portfolio/header.php");
?>

<?php


?>
      <div>
        <h1>Create a poll</h1>
        <p class="lead">Use this easy-to-use form as a way to quickly start an online poll to share</p>
      </div>

      <div id="alert" class="alert alert-info">
        <span id="span-alert">
            <strong>Warning!</strong> Best check yo self, you're not looking too good.
        </span>
      </div>

      <div class="jumbotron">
        <div class="jumbotron-contents">
            <form name="pollForm" autocomplete="off" action="poll_question.php" onsubmit="return validateForm()" method="post">
                <div class="row">
                  <div class="col-md-6">
                    <p class="lead">
                      <div>
                        <input name="question" type="text" class="poll_question" placeholder="Type question here">
                      </div>
                      <!--<div class="poll_question" contenteditable="true"></div>-->
                    </p>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6 form-group has-feedback">
                    <input name="answer1" maxlength="15" id="inputError2" type="text" class="poll_answer form-control" placeholder="Answer #1">
                    <span class="glyphicon form-control-feedback poll_answer_glyph"></span>
                  </div>
               </div>

               <br>
               <input type="submit" value="Submit" class="btn">
               
           </form>
        </div>
      </div>

<script src="js/poll.js"></script>

<?php
include_once($root . "/portfolio/footer.php");
?>