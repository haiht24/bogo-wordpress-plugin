jQuery(document).ready(function ($) {
    console.log('Watcher enabled');
    var allowSubmit = false;
    // console.log(ajaxurl);
    var form = $('form#edittag');
    form.submit(function (e) {
        if(!allowSubmit)
            e.preventDefault();
    });
    // edit store
    var inputAffUrl = $('#_wpc_store_aff_url');
    if(inputAffUrl.length > 0){
        var firstAffUrl = inputAffUrl.val();
        var storeName = $('#name').val();
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
                        type: 'store',
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
    });

    /* Do not allow change aff url of store */
    var _currentUserEmail = $('#_currentUserEmail').val();
    var arrAllowEditStoreUrl = ['toanty.kt@gmail.com'];
    // var arrAllowEditStoreUrl = ['abc@gm.com'];
    var inputStoreUrl = $('#_wpc_store_url');
    if(arrAllowEditStoreUrl.indexOf(_currentUserEmail) > -1){
        console.log('allow edit home url');
    }else{
        console.log('Do not allow edit home url');
        inputStoreUrl.prop('readOnly', true);
    }

});