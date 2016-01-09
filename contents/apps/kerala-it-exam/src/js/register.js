$(document).ready(function(){
  $("#student_register select, #student_register input").live("change", function(e){
    /**
     * Class is a reserved word
     */
    school_class = $("#student_register select[name=class]").val();
    div = $("#student_register select[name=div]").val().toUpperCase();
    roll = $("#student_register input[name=roll]").val();
    
    if(!roll.match(/^\d+$/)){
      $("#student_register input[name=roll]").val("").focus();
      alert("Enter valid Roll Number");
    }else{
      $("#register_number").html(school_class + div + roll);
    }
  });
});
