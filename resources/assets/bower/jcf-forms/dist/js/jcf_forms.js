function determineFloat() {
   $("input").each(function() {
      if($(this).val().length === 0) {
         $(this).removeClass('active');
      }      
      else {
         $(this).addClass('active');
      }
   });
}

$(document).on('keypress change', 'input', function() {
   determineFloat();
   
   $(this).addClass("visited");
});

$(document).on('keypress', function(e) {
   if (e.keyCode === 9) {
      determineFloat(); 
   }
});