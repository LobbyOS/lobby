$.extend(lobby.app, {
  options : {
    id: '.show_host .text',
    auto: false
  },
  
  number_in_words : {
    1 : "First",
    2 : "Second",
    3 : "Third",
    4 : "Fourth",
    5 : "Fifth",
    6 : "Sixth",
    7 : "Seventh",
    8 : "Eighth",
    9 : "Nineth",
    10 : "Tenth",
    11 : "Eleventh",
    12 : "Twelveth",
    13 : "Thirteenth",
    14 : "Fourteenth",
    15 : "Last"
  },
  
  resources : {
    /**
     * Scripts
     */
    "chart_bar" : lobby.app.src + "/src/js/chart.bar.js",
    
    /**
     * Images
     */
    "img_bg" : lobby.app.src + "/src/image/bg.png",
    "img_lifelines" : lobby.app.src + "/src/image/Lifelines.png",
    "img_lozenge" : lobby.app.src + "/src/image/Lozenge.png",
    
    /**
     * Audio
     */
    "audio_audiencepoll" : lobby.app.src + "/src/Data/Audio/audiencepoll.ogg",
    "audio_bg" : lobby.app.src + "/src/Data/Audio/bg.ogg",
    "audio_correct" : lobby.app.src + "/src/Data/Audio/correct.ogg",
    "audio_end" : lobby.app.src + "/src/Data/Audio/end.ogg",
    "audio_fiftyfifty" : lobby.app.src + "/src/Data/Audio/fiftyfifty.ogg",
    "audio_intro" : lobby.app.src + "/src/Data/Audio/intro.ogg",
    "audio_locked" : lobby.app.src + "/src/Data/Audio/locked.ogg",
    "audio_millionaire" : lobby.app.src + "/src/Data/Audio/millionaire.ogg",
    "audio_ovation" : lobby.app.src + "/src/Data/Audio/ovation.ogg",
    "audio_phoneafriend" : lobby.app.src + "/src/Data/Audio/phoneafriend.ogg",
    "audio_rules" : lobby.app.src + "/src/Data/Audio/rules.ogg",
    "audio_suspense" : lobby.app.src + "/src/Data/Audio/suspense.ogg",
    "audio_timeout" : lobby.app.src + "/src/Data/Audio/timeout.ogg",
    "audio_wrong" : lobby.app.src + "/src/Data/Audio/wrong.ogg",
  },
  
  init : function(){
  
    // Loading Bar
    Fr.resources.push(this.resources);
    Fr.resources.progress = function(progress, no, total){
      if(no != total){
        no++;
      }
      $(".workspace#millionaire .loading .progress .inner").css("width", progress + "%");
      $(".workspace#millionaire .loading .status").html("Loading "+ no + "/" + total + "<br clear/>" + progress + "%");
    };
    
    Fr.resources.load(function(){
      // Append Audio elements
      $.each(lobby.app.resources, function(i, elem){
        if(i.substr(0, 5) == "audio"){
          $(".workspace .audios").append("<audio id='"+ i +"' src='"+ elem +"'></audio>");
        }
      });
      
      $(".workspace#millionaire").css({backgroundImage : "url("+ lobby.app.src +"/src/image/bg.png)"});
      $(".workspace#millionaire .loading").fadeOut(1000, function(){
        $(".clearfix").fadeIn(1000);
      });
      
      $(".workspace").append("<script async src='"+ lobby.app.resources['chart_bar'] +"'>" + "<" + "/" + "script>");
      
      lobby.app.scene.intro();
      setTimeout("lobby.app.scene.rules()", 30000);
    });
  },
  
  scene : {
    intro : function(){
      $(".workspace .show_host .timer").hide();
      lobby.app.audio("intro");
      lobby.app.tell("It's the most popular game show on the planet");
      lobby.app.tell("It's the most watched game show on the planet", 5);
      lobby.app.tell("It's the most exciting game show", 10);
      lobby.app.tell("Welcome To Who Wants To Be A Millionaire", 15);
      lobby.app.tell("Let's Play <br/> <h2>Who Wants To Be A Millionaire</h2>", 20);
    },
  
    rules: function(){
      lobby.app.audio("rules");
      
      lobby.app.tell("There are 15 questions starting from $100 to <br/> $1 Million");
      $(".clearfix .right .questions .question:last").addClass("cur");
      var interval = setInterval(function(){
        $(".clearfix .right .questions .question.cur").prev().addClass("cur");
        last = $(".clearfix .right .questions .question.cur:last").removeClass("cur");
        if(last.length == 0){
          clearInterval(interval);
        }
      }, 250);
      
      setTimeout(function(){
        lobby.app.tell("There Are 3 Lifelines");
        
        setTimeout(function(){
          lobby.app.tell("<h2>50:50</h2><p>Remove Two Wrong Answers</p>", 0, 0);
          $(".clearfix .right .lifeline:first").addClass("active");
        }, 4000);
        
        setTimeout(function(){
          lobby.app.tell("<h2>Phone A Friend</h2><p>Ask Computer To Tell The Answer</p>", 0, 0);
          $(".clearfix .right .lifeline").removeClass("active");
          $(".clearfix .right .lifeline:first").next().addClass("active");
        }, 8000);
        
        setTimeout(function(){
          lobby.app.tell("<h2>Audience Vote</h2><p>Audience will help you choose the right answer</p>", 0 ,0);
          $(".clearfix .right .lifeline").removeClass("active");
          $(".clearfix .right .lifeline:last").addClass("active");
          
          setTimeout(function(){
            $(".clearfix .right .lifeline").removeClass("active");
            lobby.app.scene.game();
          }, 4000);
        }, 12000);
      }, 5000);
    },
    
    game: function(){
      lobby.app.audio("ovation");
      
      setTimeout(function(){
        lobby.app.game.start();
      }, 4000);
    }
  },
  
  game: {
    money : 0,
    question_id : "0-0",
    question_level : 0,
    
    events: function(){
      // Chose Option, Check And Show status
      $(".workspace .clearfix .left .options .option").die("click").live("click", function(){
        if($(".clearfix .left .options .option.locked").length == 0 && typeof $(this).data("disabled") == "undefined"){
          elem = $(this);
          elem.addClass("locked");
          $(".clearfix .left .show_host .timer").TimeCircles().stop();
          
          lobby.app.audio("locked");
          lobby.app.audio("suspense", true);
        
          chosen_answer = encodeURIComponent($(this).find(".text").text());
          letter = $(this).find(".letter").text().substr(0, 1);
          
          lobby.app.tell("Computer, Please Lock Option " + letter);
          lobby.app.ajax("answer.php", {question_id : lobby.app.game.question_id, "answer": chosen_answer}, function(r){
            setTimeout(function(){
              if(r == "correct"){
                elem.addClass("correct");
                
                lobby.app.audio("bgoff");
                
                if(lobby.app.game.question_level == 15){
                  $(".clearfix .left .show_host .timer").fadeOut(500);
                  
                  lobby.app.audio("ovation");
                  lobby.app.tell("Congratulations, You're A <h1>Millionaire !</h1>");
                  setTimeout(function(){
                    lobby.app.audio("millionaire");
                  }, 2500);
                  setTimeout(function(){
                    lobby.app.game.end();
                  }, 7000);
                }else{
                  lobby.app.audio("correct");
                  lobby.app.tell("Correct Answer !", 0, 0);
                  setTimeout(function(){
                    lobby.app.game.loadQuestion(lobby.app.game.question_level + 1);
                  }, 5000);
                }
              }else if(r.substr(0, 1) == "{"){
                data = JSON.parse(r);
                var correct = data.correct;
                $(".workspace .clearfix .left .options .option").each(function(){
                  if($(this).find(".text").text() == correct){
                    $(this).addClass("correct");
                  }
                });
                
                lobby.app.audio("bgoff");
                lobby.app.audio("wrong");
                $(".clearfix .left .show_host .timer").hide();
                lobby.app.tell("Wrong Answer, You Got <h2>"+ data.money +"</h2>", 0, 0);
                
                setTimeout(function(){
                  lobby.app.game.end();
                }, 3000);
              }
            }, 5000);
          });
        }
      });
      
      /**
       * Lifelines
       */
      $(".workspace .clearfix .right .lifelines .lifeline").die("click").live("click", function(){
        id = $(this).attr("id");
        
        if(id == "fifty-fifty" && $(this).hasClass("used") == false){
          elem = $(this);
          elem.addClass("active");
          
          // Pause timer
          $(".workspace .show_host .timer").TimeCircles().stop();
          
          lobby.app.tell("Computer, Please Take Off Two Wrong Answers", 0, 0);
          
          // Disable buttons
          lobby.app.game.remove_events();
          
          lobby.app.ajax("fifty-fifty.php", {question_id : lobby.app.game.question_id}, function(r){
            d = JSON.parse(r);
            
            setTimeout(function(){
              lobby.app.audio("fiftyfifty");
              $(".workspace .clearfix .left .options .option").each(function(){
                if($(this).find(".text").text() == d[0] || $(this).find(".text").text() == d[1] ){
                  $(this).data("disabled", true);
                  $(this).find(".text").html("");
                }
                elem.removeClass("active").addClass("used");
              });
              
              $(".workspace .show_host .timer").TimeCircles().start();
              lobby.app.game.events();
            }, 1000);
          });
        }else if(id == "phoneafriend"  && $(this).hasClass("used") == false){
          elem = $(this);
          elem.addClass("active");
          
          // Pause timer
          $(".workspace .show_host .timer").TimeCircles().stop();
          
          $(".workspace .clearfix .left .phoneafriend-container").show();
          
          options = [];
          $(".workspace .clearfix .left .options .option").each(function(){
            if($(this).find(".text").text() != ""){
              options.push($(this).find(".text").text());
            }
          });
          
          lobby.app.ajax("phoneafriend.php", {question_id : lobby.app.game.question_id, "options": options}, function(r){
            lobby.app.audio("phoneafriend");
            setTimeout(function(){
              $(".workspace .clearfix .left .phoneafriend-container .text").hide().fadeIn(1000);
              $(".workspace .clearfix .left .phoneafriend-container .text").text("I think the answer is '" + r + "'");
            }, 1000);
            setTimeout(function(){
              $(".workspace .show_host .timer").TimeCircles().start();
              $(".workspace .clearfix .left .phoneafriend-container").fadeOut(500);
              $(".workspace .clearfix .left .phoneafriend-container .text").text('');
            }, 5000);
            elem.removeClass("active").addClass("used");
          });
        }else if(id == "audience-vote"  && $(this).hasClass("used") == false){
          elem = $(this);
          elem.addClass("active");
          
          /**
           * Audience Vote
           */
          $(".workspace .clearfix .left .audiencevote-container").fadeIn(1000);
          
          // Pause timer
          $(".workspace .show_host .timer").TimeCircles().stop();
          
          lobby.app.audio("bgoff");
          lobby.app.audio("audiencepoll");
          var ctx = $(".workspace .clearfix .left .audiencevote-container canvas").get(0).getContext("2d");

          var audience_vote_chart = new Chart(ctx).Bar({
            labels: ["A", "B", "C", "D"],
            datasets: [
              {
                label: "My First dataset",
                fillColor: "rgba(246,162,0,1)",
                strokeColor: "rgba(255,255,255,1)",
                data: [65, 59, 80, 20]
              },
            ]
          }, {
            animation: true,
            scaleFontColor: "#fff",
            barShowStroke : false,
            tooltipTemplate: "<%= value %>",
            tooltipEvents: [],
            showScale: false
          });
          
          bar_update = setInterval(function(){
            maximum = 90, minimum = 10;
            audience_vote_chart.datasets[0].bars[0].value = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
            audience_vote_chart.datasets[0].bars[1].value = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
            audience_vote_chart.datasets[0].bars[2].value = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
            audience_vote_chart.datasets[0].bars[3].value = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
            audience_vote_chart.update();
            audience_vote_chart.showTooltip(audience_vote_chart.datasets[0].bars, true);
          }, 400);
          
          lobby.app.ajax("audience-vote.php", {question_id : lobby.app.game.question_id}, function(r){
            setTimeout(function(){
              clearInterval(bar_update);
              
              $.each(JSON.parse(r), function(option, percentage){
                index = $(".workspace .clearfix .left .options .option :contains('"+ option +"')").parents(".option").index();
                audience_vote_chart.datasets[0].bars[index].value = percentage;
                
                $(".workspace .clearfix .left .audiencevote-container .results span").eq(index).append("<div>"+ percentage +"%</div>");
              });
              audience_vote_chart.update();
            }, 9000);
            setTimeout(function(){
              $(".workspace .clearfix .left .audiencevote-container").fadeOut(500);
          
              // Pause timer
              $(".workspace .show_host .timer").TimeCircles().start();
              
              lobby.app.audio("bg");
            }, 12000);
            elem.addClass("used");
          });
        }
      });
    },
    
    remove_events: function(){
      $(".workspace .clearfix .left .options .option").die("click");
      $(".workspace .clearfix .right .lifelines .lifeline").die("click");
    },
    
    start: function(){
      lobby.app.game.loadQuestion(1);
      lobby.app.game.events();
    },
    
    end: function(){
      lobby.app.audio("bgoff");
      lobby.app.audio("end");
      this.remove_events();
      
      setTimeout(function(){
        if(confirm("Thank You For Playing. Want to Play Again ?") == true){
          window.location = lobby.app.url + "/single";
        }
      }, 7000);
    },
    
    timeout: function(){
      if(typeof timeoutCalled == "undefined"){
        lobby.app.tell("Oops, Time Ran Out");
        lobby.app.audio("timeout");
        
        lobby.app.game.remove_events();
        setTimeout(function(){
          lobby.app.game.end();
        }, 2000);
        timeoutCalled = 1;
      }
    },
    
    loadQuestion : function(level){
      qid = lobby.app.questions[level]['id'];
      item = lobby.app.questions[level]['content'];
      
      lobby.app.game.question_level = level;
      lobby.app.game.question_id = level + "-" + qid;
      
      this.events();
      lobby.app.audio("bg", true);
      lobby.app.game.startTimer(20);
      
      $(".workspace .clearfix .left .options .option").removeClass("locked").removeClass("correct").removeData("disabled");
      lobby.app.tell(lobby.app.number_in_words[level] + " Question For " + lobby.app.currency + lobby.app.level_money[level], 0, 0);
      
      $(".clearfix .left .question .text").text(item['question']);
      $.each(item['options'], function(i, elem){
        i++;
        $(".clearfix .left .options .option:nth-child("+ i +") .text").text(elem);
      });
      
      $(".clearfix .right .question").removeClass("cur");
      $(".clearfix .right .question:nth-child("+ (16 - level) +")").addClass("cur");
    },
  
    startTimer: function(seconds){
      $(".clearfix .left .show_host .timer").fadeIn(500).data("timer", parseFloat(seconds) + 1);
      $(".clearfix .left .show_host .timer").TimeCircles({
        animation : "ticks",
        start: true,
        count_past_zero: false,
        time: {
          Days: {
            "show": false
          },
          Hours: {
            "show": false
          },
          Minutes: {
            "show": false
          },
          Seconds: {
            "text": "Seconds",
            "color": "#FF9999",
            "show": true
          }
        }
      }).addListener(function(unit, value, total) { 
        if(total == 0){
          lobby.app.game.timeout();
        }
      }).restart();
    },
  },
  
  tell: function(text, time, font){
    time = typeof time == "undefined" ? 0 : time + "000";
    setTimeout(function(){
      $(".show_host .text").html(text);
      if(typeof font == "undefined"){
        $(".show_host .text").css("font-size", "10").animate({"font-size" : 40}, 4000);
      }else{
        $(".show_host .text").css("font-size", "40");
      }
    }, time);
  },
  
  audio: function(file, bg){
    if((typeof audioBG != "undefined" && typeof bg != "undefined") || file == "bgoff"){
      audioBG.pause();
    }
    if(file != "bgoff"){
      audioSrc = lobby.app.src + "/src/Data/Audio/" + file + ".ogg";
      audio = $(".workspace .audios #audio_" + file)[0];
      audio.play();
    }
    
    if(typeof bg != "undefined"){
      audioBG = audio;
      if(typeof audio.loop == "undefined"){
        audioBG.addEventListener('ended', function() {
          this.currentTime = 0;
          this.play();
        }, false);
      }else{
        audioBG.loop = true;
      }
    }
  },
  
  /*test: {
    rules: function(){
      $(".workspace#millionaire .loading").fadeOut(1000, function(){
        $(".clearfix").fadeIn(1000);
      });
      lobby.app.scene.rules();
    },
    
    game: function(){
      $(".workspace#millionaire .loading").fadeOut(1000, function(){
        $(".clearfix").fadeIn(1000);
      });
      $.each(lobby.app.resources, function(i, elem){
        if(i.substr(0, 5) == "audio"){
          $(".workspace .audios").append("<audio id='"+ i +"' src='"+ elem +"'></audio>");
        }
      });
      
      $(".workspace").append("<script async src='"+ lobby.app.resources['chart_bar'] +"'>" + "<" + "/" + "script>");
      lobby.app.scene.game();
    }
  }*/
});
lobby.app.init();
//lobby.app.test.rules();
//lobby.app.test.game();
