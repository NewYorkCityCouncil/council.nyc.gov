jQuery(document).ready(function($) {
    $(document).on('click','.accordions-import-json',function(){
        json_file = $('.json_file').val();



        if(json_file){
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            $.ajax(
                {
                    type: 'POST',
                    context: this,
                    url:accordions_ajax.accordions_ajaxurl,
                    data: {
                        "action" 	: "accordions_ajax_import_json",
                        "json_file" : json_file,
                        "nonce" : accordions_ajax.nonce,
                    },
                    success: function( response ) {
                        var data = JSON.parse( response );
                        console.log(data);
                        $(this).html('Import done');
                        $('.json_file').val('');
                    } });
        }
        else{
            alert('Please put file url');
        }
    })
});