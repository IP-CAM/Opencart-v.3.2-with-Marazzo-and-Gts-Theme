$(document).on('click','.table-responsive .parent .item__toggle',function(){
    $(this).parent().parent().toggleClass('active');
});