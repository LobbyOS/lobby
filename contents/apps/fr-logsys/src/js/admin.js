$.extend(lobby.app, {

  init: function(){
    $('.workspace').ajaxify({
      previewoff: true,
      inline: true,
      prefetch: false,
      selector : ".workspace a"
    });
    
    $(window).on("pronto.request", function(){
      $('.rightpane .contentLoading').show();
    });
    $(window).on("pronto.render", function(){
      lobby.load_init();
    });
    
    $(".workspace .dialog").live("click", function(){
      $.fancybox.defaults.minHeight = 100;
      $.fancybox.defaults.minWidth = 600;
      lobby.app.ajax($(this).data("dialog"), $(this).data("params"), function(r){
        $.fancybox.open(r);
      });
    });
    
    $(".workspace .removeColumn").live("click", function(){
      col = $(this).data('column');
      if(confirm("Are you sure you want to delete the column '"+ col +"' ?")){
        index = $(this).parents("th").index();
        lobby.app.ajax("remove_col.php", {column: col}, function(){
          $(".workspace .contentLoader table thead").find("th:eq("+ index +")").fadeOut(1000);
          $(".workspace .contentLoader table tr").find("td:eq("+ index +")").fadeOut(1000);
        });
      }
    });
  },

  stats: function(monthChart, dayChart, dayChartDays){
    if(document.getElementById("monthChart") != null){
      var ctx = document.getElementById("monthChart").getContext("2d");
      var myNewChart = new Chart(ctx).Radar({
        labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
        datasets: [{
          label: "Month Dataset",
          strokeColor: "#1E90FF",
          pointColor: "#1A1A1A",
          pointStrokeColor: "#fff",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
          data: monthChart
        }]
      });
  
      var ctx = document.getElementById("dayChart").getContext("2d");
      var myNewChart = new Chart(ctx).Line({
        labels: dayChartDays,
        datasets: [{
          label: "Day Dataset",
          strokeColor: "#1E90FF",
          pointColor: "#1A1A1A",
          pointStrokeColor: "#fff",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
          data: dayChart
        }]
      });
    }
  }
});

$(function(){
  lobby.app.init();
});
