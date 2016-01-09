$.extend(lobby.app, {
  /**
   * The words
   */
  words: {},
  
  init: function(){
    this.events();
    $(".workspace .boxes").niceScroll({
      horizrailenabled : true,
      verticalrailenabled : false
    });
  },
  
  /**
   * Arrange the letters in the white box
   */
  arrangeLetters: function(){
    space = ($(".workspace .letters").width() / 20) / 2;
    $(".workspace .letter").css({
      paddingLeft: space,
      paddingRight: space
    });
  },
  
  updateStatus: function(){
    words_length = Object.keys(lobby.app["words"]).length;
    found = words_length - $(".workspace .contents .boxes .box:contains('')").length;
    $(".workspace .contents .status").html(found + " / " + words_length + " words");
  },
  
  load: function(){
    lobby.app.ajax("load.php", {length: 12}, function(response){
      response = JSON.parse(response);
      $(".workspace .letters").html(response.str);
      lobby.app.words = response.words;
      
      /**
       * Make whiteboxes for letters in all words as a row
       */
      id = 1;
      maxHeight = Math.round(($(window).height() - $(".workspace .boxes .boxes-inner .section#sec1").offset().top) / 27);
      $.each(lobby.app.words, function(word){
        h = "<div class='box'>";
        for(i=0;i < word.length;i++){
          h += "<div class='sub'></div>";
        }
        if($(".workspace .contents .boxes .boxes-inner .section#sec"+ id +" .box").length >= maxHeight){
          id++;
          $(".workspace .boxes .boxes-inner").append("<div class='section' id='sec"+ id +"'></div>");
        }
        $(h + "</div>").attr("data-word", word).appendTo($(".workspace .boxes .boxes-inner .section#sec" + id));
      });
      
      /**
       * Set height, width
       */
      width = 20;
      $(".workspace .boxes .boxes-inner .section").each(function(){width += parseInt($(this).width() + 21);});
      if($(".workspace .boxes .boxes-inner").width() < width){
        $(".workspace .boxes .boxes-inner").width(width);
      }
      $(".workspace .boxes").height(Math.round(($(window).height() - $(".workspace .boxes .boxes-inner .section#sec1").offset().top)) - 10);
      
      /**
       * New letters was added, so arrange it
       */
      lobby.app.arrangeLetters();
      lobby.app.updateStatus();
      $(".workspace .contents .loading").fadeOut(500);
    });
  },
  
  /**
   * Event Listeners on the letters and buttons
   */
  events: function(){
    $(".workspace #newGame").live("click", function(){
      $(".workspace .letters, .workspace .input, .workspace .boxes .boxes-inner").html('');
      $(".workspace .boxes .boxes-inner").append("<div class='section' id='sec1'></div>");
      
      $(".workspace .contents .loading").fadeIn(500);
      lobby.app.load();
    });
    
    $(".workspace .letters .letter").live("click", function(e){
      e.preventDefault();
      if(!$(this).hasClass("hidden")){
        l = $(this).text();
        $(".workspace .input").append("<a href='' class='letter'>"+ l +"</a>");
        
        $(".workspace .contents audio#select")[0].play();
        $(this).addClass("hidden");
        lobby.app.arrangeLetters();
      }
    });
    
    $(".workspace .input .letter").live("click", function(e){
      e.preventDefault();
      l = $(this).text();
      $(".workspace .letters .letter.hidden:first").replaceWith("<a href='' class='letter'>"+ l +"</a>");
      $(this).remove();
      lobby.app.arrangeLetters();
    });
    
    /**
     * When a word is submitted, check and scroll to the box containing the word
     */
    $(".workspace .submit .button").live("click", function(){
      word = $(".workspace .input").text();
      if(typeof lobby.app.words[word] != "undefined" && $(".workspace .contents .boxes").find(".box[data-word='"+ word +"']").text() == ""){
        p = $(".workspace .contents .boxes").find(".box[data-word='"+ word +"']");
        for (var i = 0, len = word.length; i < len; i++) {
          p.find(".sub").eq(i).text(word[i]);
          $(".workspace .contents .boxes").scrollTo(p, {duration:'slow'});
        }
        $(".workspace .contents audio#correct")[0].play();
      }else{
        $(".workspace .contents audio#wrong")[0].play();
      }
      lobby.app.updateStatus();
    });
    
    /**
     * Solve automatically
     */
    $(".workspace .contents .controls #solveGame").live("click", function(){
      solveInterval = setInterval(function(){
        t = $(".workspace .contents .boxes .box .sub:empty").first().parents(".box");
        word = t.data("word");
        if(typeof word == "undefined"){
          clearInterval(solveInterval);
        }else{
          for (var i = 0, len = word.length; i < len; i++) {
            t.find(".sub").eq(i).text(word[i]);
            $(".workspace .contents .boxes").scrollTo(t);
          }
          $(".workspace .contents audio#select")[0].play();
        }
      }, 100);
    });
  }
});
lobby.load(function(){
  lobby.app.init();
});
