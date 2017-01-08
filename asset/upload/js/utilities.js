/**
 * Utilities
 */

/*
 * Xóa class 'active' của những item không được click trong cùng 1 button group
 */
$(function(){
    $('.btn-group .btn').click(function(){ 
        $(this).addClass('active').siblings().removeClass('active');
    }); 
})