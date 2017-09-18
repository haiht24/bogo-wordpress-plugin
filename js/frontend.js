jQuery(document).ready(function ($) {
    // console.log(bogoAjaxVar.ajaxurl);
    var ajaxurl = bogoAjaxVar.ajaxurl;
    $('.coupon-title').prepend("<a class='quick-edit' style='cursor: pointer;color: blue'>[ edit ]</a>");
    $('.coupon-title .coupon-link').click(function (e) {
        return false;
    });
    $('.quick-edit').on('click', function () {
        $(this).hide();
        var title = $(this).closest('.coupon-item').find('.coupon-link').text();
        var btnSave = "<button class='btn_primary button ui quick-update-post' style='margin-top: 10px' >Update</button>";
        var btnCancel = "<button class='button ui btn-cancel' style='margin-top: 10px' >Cancel</button>";
        var inputEdit = "<input class='newTitle' style='cursor: default; width:100%;' type='text' value='" + title + "'>";
        $(this).closest('.coupon-item').prepend("<div class='clear div-quick-edit'>" + inputEdit + btnSave + btnCancel + "</div><p></p>");
    });

    $(document.body).on('click', '.btn-cancel', function() {
        $(this).closest('.coupon-item').find('.div-quick-edit').hide();
        $(this).closest('.coupon-item').find('.quick-edit').fadeIn(500);
    });

    $(document.body).on('click', '.quick-update-post', function() {
        var t = $(this);
        var boxCouponItem = t.closest('.coupon-item');
        var postId = boxCouponItem.find('.coupon-link').data('coupon-id');
        var newTitle = boxCouponItem.find('.newTitle').val();
        var $params = {
            id: postId,
            title: newTitle
        };

        jQuery.ajax({
            url : ajaxurl,
            type : 'post',
            data : {
                action : 'quick_update_post',
                params: $params
            },
            success : function( response ) {
                console.log(response);
                if(response === 'success'){
                    boxCouponItem.find('.coupon-link').text(newTitle);
                    boxCouponItem.find('.coupon-link').attr('title', newTitle);
                    boxCouponItem.find('.div-quick-edit').fadeOut(500);
                    boxCouponItem.find('.quick-edit').fadeIn(500);
                }
            }
        });
    });

});