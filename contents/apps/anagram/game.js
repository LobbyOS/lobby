var parent = ".workspace#anagram "
$(parent +"#newGame").live("click", function(){
	$(parent +".letters, "+ parent +".input").html('');
	lobby.ajaxRequest("load.php", {length: 2}, function(r){
		$(parent +".letters").html(r);
	});
});
$(parent +".letters .letter").live("click", function(e){
	e.preventDefault();
	l = $(this).text();
	$(parent +".input").append("<a href='' class='letter'>"+ l +"</a>");
	$(this).remove();
});
$(parent +".input .letter").live("click", function(e){
	e.preventDefault();
	l = $(this).text();
	$(parent +".letters").append("<a href='' class='letter'>"+ l +"</a>");
	$(this).remove();
});