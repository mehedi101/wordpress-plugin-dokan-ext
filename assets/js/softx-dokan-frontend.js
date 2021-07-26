;(function($){
    // alert("I am working");
   
    $("#_is_public_product_checkbox").on("change",function(){
     
     var val= $(this).val();
     var ppp = $("#public_product_price");
     console.log(ppp.val());
     console.log(val);
     if( this.checked){
       ppp.slideDown()
     }
     if( ! this.checked){
      var price = setPublicPriceOnUnchecked();
         ppp.slideUp();
       console.log(price);
     }
   
    });
   
    function setPublicPriceOnUnchecked(){
     return $("#public_product_price").val('');
   
    }
   
   
   
    $("#prices").on("change", function(){
     
     var val= $(this).text();
     var selectedOptionPrice = parseInt( $(this).find("option:selected").text() );
     var regular_price = $("#_regular_price");
     //console.log(ppp);
     console.log(selectedOptionPrice);
    
     console.log(regular_price);
     if( selectedOptionPrice > 1){
     regular_price.val(selectedOptionPrice);
     }
    });
   
   
   })(jQuery);
   