jQuery(document).ready(function() {
    jQuery(".optional").css("display", "none");
    var request_val = jQuery("#send_api_request_field").val();
    var request_order_id = jQuery("#request_order_id").val();
    var admin_ajax_url = jQuery("#admin_ajax_url").val();
    
    if(request_val == 1) {
        jQuery("#send_api_request").attr({
            checked:"checked", 
            disabled:"disabled"
        });
    }

    jQuery("#send_api_request").click(function(){
        if(request_val == 0) {
            jQuery.ajax({
                type: "post",
                url: admin_ajax_url,
                data : {
                    action : 'api_request_ajax_function',
                    order_id : request_order_id
                },
                success : function( response ) {
                    jQuery("#send_api_request_field").val(1);
                    jQuery("#send_api_request").attr({
                        checked:"checked", 
                        disabled:"disabled"
                    });
                }
            });
          
           
        }

    });
    
   
});