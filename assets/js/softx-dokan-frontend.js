;(function($){
    
  /*********************************************
   *  seller dashboard front end customization *
   * *******************************************/

    $(document).ready(function() {

    /** if vendor click on show product on public shop
     * it will slideDown a field to set the price for public shop */

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

    /********************
     * my account page  *
    *********************/
   
   /* only seller radio button active when load the page*/ 
    $(".user-role").children("label").eq(1).find("input").trigger("click");
  
    /** by default seller related form field disabled.
     * this funcation will set disabled = false afer 2s to enter value
     */
    window.setTimeout(function(){

        $(".show_if_seller").find("input").prop("disabled",false);
        
        },2000);    

      }); /** documnet ready close */
   })(jQuery);

