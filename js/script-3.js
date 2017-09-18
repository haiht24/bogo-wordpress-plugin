jQuery(document).ready(function ($) {
    $('#submit').click(function () {
        // empty add store field after click add success: empty 2 tiny mce editor + remove chosen image before
        // excute after click 1 seconds
        setTimeout(
            function()
            {
                $('#html-tag-description_ifr').contents().find('body').text('');
                $('#_wpc_extra_info_ifr').contents().find('body').text('');
                $('#_wpc_store_image').val('');
                $('#_wpc_store_image_id').val(0);
                $('#_wpc_store_image_id-status').empty();
            }, 1000);
    });

    /* Check store exist */
    $('#_wpc_store_url').on('change', function () {
        var input = $(this).val();
        jQuery.ajax({
            url : ajaxurl,
            type : 'post',
            data : {
                action : 'check_store_exist',
                params: {
                    url: input,
                    editUrl: window.location.href
                }
            },
            success : function( response ) {

                response = JSON.parse(response);
                console.log(response);
                if(response.length > 0){
                    for(var i = 0; i < response.length; i++){
                        var el = response[i];
                        // console.log(el);
                        $('#_wpc_store_url').parent().append(el);
                    }
                }
            }
        });
    })
});