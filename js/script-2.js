jQuery(document).ready(function ($) {
    console.log('Watcher enabled');
    var allowSubmit = false;
    // edit coupon
    var form = $('form#post');
    form.submit(function (e) {
        if(!allowSubmit)
            e.preventDefault();
    });
    var inputAffUrl = $('#_wpc_destination_url');
    if(inputAffUrl.length > 0){
        var firstAffUrl = inputAffUrl.val();
        var storeName = $('#title').val();
    }
    $('input[type=submit]').click(function () {
        $(this).prop('disabled', true);
        var lastAffUrl = inputAffUrl.val();
        if(firstAffUrl !== lastAffUrl) {
            jQuery.ajax({
                url : ajaxurl,
                type : 'post',
                data : {
                    action : 'alert_email',
                    params: {
                        first: firstAffUrl,
                        last: lastAffUrl,
                        storeName: storeName,
                        type: 'coupon',
                        editUrl: window.location.href
                    }
                },
                success : function( response ) {
                    console.log(response);
                    $(this).prop('disabled', false);
                    allowSubmit = true;
                    form.submit();
                }
            });
        }else{
            $(this).prop('disabled', false);
            allowSubmit = true;
            form.submit();
        }
    })
});