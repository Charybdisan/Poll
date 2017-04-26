var answers;
var percents = [];
var str = "";

function random_hexcode(){
  return "#" + Math.random().toString(16).slice(2, 8);
}

function get_percents(answers){
  var totalVotes = 0;
  //First get total number of votes
  for(var i = 0; i < answers.length; i++){
    totalVotes+=answers[i][1];
  }
  if(totalVotes == 0) totalVotes = 1;
  //Calculate percents
  for(var i = 0; i < answers.length; i++){
    percents[i] = Math.round((Number(answers[i][1]) / totalVotes)*100);
    
  }
}

function create_bars(answers){
  get_percents(answers);
  for(var i = 0; i < answers.length; i++){
    //console.log(answers[i][2]);
    str = $(".jumbotron-contents").html();
    str+=
    "<div class='row'>"
    + " <div class='col-md-8 col-xs-8'>"
    + "  <div data-aid=" + answers[i][2] + " class='vote_bar progress progress-striped active' title='Vote for \"" + answers[i][0] + "\"'>"
    + "    <div style='z-index: 1; position: absolute; margin-left: 6px; width: 100%;'><span class='percent_bar'>" + answers[i][0] + "</span></div>"
    + "    <div id='answer" + i + "' class='progress-bar' role='progressbar' aria-valuenow='" + percents[i] + "' aria-valuemin='0' aria-valuemax='100' style='width: " + percents[i] + "%;" + "background-color: " + random_hexcode() + ";'>" 
    + "    </div>"
    + "  </div>"
    + " </div>"
    + "  "
    + " <div class='col-md-4 col-xs-4'>"
    + "  <span id='voteanswer" + i + "'>" + answers[i][1] + " votes (" + percents[i] + "%)</span>"
    + " </div>"
    + "</div>";
    
    
    $(".jumbotron-contents").html(str);
  }
}

function refresh_results(){
  
  $.ajax({url: "get_answers.php?qid=" + qid, success: function(result){
    answers = JSON.parse(result);
    get_percents(answers);
    
    for(var i = 0; i < answers.length; i++){
      $("#answer" + i).data('aria-valuenow', percents[i]).css("width", percents[i] + "%");
      $("#voteanswer" + i).html(answers[i][1] + " votes (" + percents[i] + "%)"); 
    }
    
  }});


}

$(document).on("ready", function(){
  //Get the answers and create bars
  $.ajax({url: "get_answers.php?qid=" + qid, success: function(result){
    answers = JSON.parse(result);

    create_bars(answers);
    
    //Over time refresh results of answer bars
    setInterval(refresh_results, 2000);
    
    $(".vote_bar").click(function(){
      var aid = $(this).data("aid");
      $.ajax({url: "vote.php?aidreq=" + aid + "&qid=" + qid, success: function(result){
        if(result != "OK"){
          showAlert(result, "alert-danger");
        }
        else{
          refresh_results();
        }
      }});

      
      
    });

    
  }});
  
});