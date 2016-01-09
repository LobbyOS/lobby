lobby.load(function(){
  lobby.app = $.extend(lobby.app, {
    
    exam_time: 60, // The exam time in minutes
  
    // The practical questions are inserted in this key
    practicalQuestions: {},
    
    /**
     * HTML page calls this function
     */
    startExam: function(){
      this.startTimer();
      this.events();
      this.loadQuestion("short", 0);
      this.loadQuestion("multiple", 0);
      
      $(".exam .toggleGroup:first").addClass("ui-state-active");
      this.loadQuestion("note", 0, 1);
    },
    
    /**
     * Start the countdown timer
     */
    startTimer: function(){
      /**
      * Just a message
      */
      localStorage["end_message"] = "Hey hacker, you can extend the time, but you're cheating. XD : Subin";
      if(new Date(localStorage['end_time']) != "Invalid Date"){
        date = new Date(localStorage['end_time']);
      }else{
        date = new Date();
        date.setMinutes(date.getMinutes() + 60);
        localStorage['end_time'] = date;
      }
      $('#time').countdown(date, function(event) {
        if(event.strftime('%H:%M:%S') == "00:00:00"){
          $(".workspace").css("overflow", "hidden");
          $(".workspace #expired_overlay").fadeIn(1000, function(){
            $(".workspace .exam").remove();
          });
        }else{
          $(this).html(event.strftime('%H:%M:%S'));
        }
      });
    },
    /**
     * Bind event listeners to the page where needed
     */
    events: function(){
      $(".exam .toggleQuestion").live("click", function(){
        type = $(this).data("type");
        qid = $(this).data("id");
        if(type == "note"){
          lobby.app.loadQuestion(type, qid, $(".exam .toggleGroup.ui-state-active").data("id"));
        }else{
          lobby.app.loadQuestion(type, qid);
        }
      });
      
      $(".exam .toggleGroup").live("click", function(){
        if(typeof $(this).attr("disabled") == "undefined"){
          $(".exam .toggleGroup").removeClass('ui-state-active');
          $(this).addClass('ui-state-active');
        
          gid = $(this).data("id");
          lobby.app.loadQuestion("note", 0, gid);
        }
      });
      
      /**
       * Short Answer
       * ------------
       * Only allow ticking of one checkbox
       */
      $(".exam #short-box .options .option").live("click", function(event){
        $(this).parent().find(".option input[type=checkbox]").prop('checked', false);
        $(this).find("input[type=checkbox]").prop('checked', true);
        
        var id = $(this).parents(".questionArea").attr("id");
        $(".exam #short-box .toggleQuestion[data-id="+ id +"]").removeClass("blue").addClass("green");
      });
      
      /**
       * Multiple Choice
       * ---------------
       * Only allow ticking of 2 checkboxes
       */
      $(".exam #multiple-box .options .option").live("change", function(event){
        var id = $(this).parents(".questionArea").attr("id");
        
        /**
         * We check for value 3, because this `click` callback is
         * called after the checkbox is ticked natively by HTML engine
         */
        if($(this).parent().find("input[type=checkbox]:checked").length == 3){
          /**
           * Check if the option, user clicked is not checked
           */
          if($(this).find("input[type=checkbox]").is(":checked") === true){
            $(this).find("input[type=checkbox]").prop("checked", false);
            alert("You already chose 2 options.\nUnselect one and choose again");
          }else{
            /**
             * Make the button non green, because user hasn't chose 2 options
             */
            $(".exam #multiple-box .toggleQuestion[data-id="+ id +"]").removeClass("green").addClass("blue");
          }
        }else{
          /**
           * Make the button green, because user clicked 2 options
           */
          if($(this).parent().find("input[type=checkbox]:checked").length == 2){
            $(".exam #multiple-box .toggleQuestion[data-id="+ id +"]").removeClass("blue").addClass("green");
          }else{
            $(".exam #multiple-box .toggleQuestion[data-id="+ id +"]").removeClass("green").addClass("blue");
          }
        }
      });
      
      /**
       * Short Note
       * ----------
       * Only allow ticking of one checkbox and only one group
       */
      $(".exam #note-box .options .option").live("click", function(event){
        $(this).parent().find(".option input[type=checkbox]").prop('checked', false);
        $(this).find("input[type=checkbox]").prop('checked', true);
        
        var id = $(this).parents(".questionArea").attr("id");
        $(".exam #note-box .toggleQuestion[data-id="+ id +"]").removeClass("blue").addClass("green");
        
        /**
         * Chose one group, so disable other group
         */
        $(".exam #note-box .toggleGroup").not(".ui-state-active").attr("disabled", true);
        $(".exam #note-box .toggleGroup.ui-state-active").addClass("green");
      });
      
      /**
       * Finish Theory Exam
       */
      $("#finishTheory").live('click', function(){
        // Check if user answered all questions
        if($(".toggleQuestion.blue").length != 0){
          alert("All questions aren't answered. Please answer all questions");
        }else{
          $(".exam #short-box, .exam #multiple-box, .exam #note-box").fadeOut(500, function(){
            $(".exam #practical-box").fadeIn(500);
            lobby.app.practical();
          });
          $(this).attr("id", "finishExam").text("Finish Examination");
        }
      });
      
      $("#finishExam").live('click', function(){
        // Data of answers
        var data = {};
        $(".workspace#kerala-it-exam .exam input[type=checkbox]:checked").each(function(){
          name = $(this).attr("name");
          data[name] = $(this).val();
        });clog(data);
        
        // What's Programming without a but of fun ?
        lobby.app.ajax("validate.php", data, function(json){
          json = JSON.parse(json);
          $("#finished_overlay h2").text(json.score + "/50*");
          $("#finished_overlay #result_analysis").html(json.analysis);
  
          $(".workspace").css("overflow", "hidden");
          $(".workspace .exam").remove();
          $(".workspace #finished_overlay").fadeIn(1000);
        });
      });
    },
    
    /**
     * Load the `nth` question
     * group_id is present only in note questions
     */
    loadQuestion: function(type, i, group_id){
      var questionMainContainer = ".exam #"+ type +"-box ";
      if(typeof group_id == "undefined"){
        var questionContainer = questionMainContainer;
      }else{
        var questionContainer = ".exam #"+ type +"-box #"+ group_id +".group ";
      }
      $(questionMainContainer + ".questionArea").hide();
      $(questionContainer + "#"+ i +".questionArea").fadeIn(200);
    },
    
    /**
     * Do stuff for practical phase of examination
     */
    practical: function(){
      $(".exam #practical-box .tabs li").live("click", function(){
        $(".tabs li").removeClass("cur");
        $(this).addClass("cur");
        // Load 1st choice of group
        $(".exam #practical-box .choices li:first").click();
      });
      
      $(".choices li").live("click", function(){
        group_id = $("#practical-box .tabs li.cur").data("id");
        choice_id = $(this).data("id");
        $(".exam #practical-box .questionArea .question").html(lobby.app.practicalQuestions[group_id][choice_id]['question']);
        $(".exam #practical-box .questionArea").show();
        
        $(".choices li").removeClass("cur");
        $(this).addClass("cur");
      });
      
      // Load 1st question
      $(".exam #practical-box .tabs li:first").click();
    },
    
    /**
     * The Testing Object
     */
    test: {
      /**
       * Finish the theory examination
       */
      finishTheory: function(){
        $(".toggleQuestion.blue").remove();
        $("#finishTheory").click();
      },
      
      /**
       * Finish Exam
       */
      finishExam: function(){
        this.finishTheory();
        $(".workspace#kerala-it-exam .exam .options").each(function(){
          $(this).find("input:first").prop("checked", true);
        });
        $("#finishExam").click();
      }
    }
  });
});
