/**
 * Utilities
 */


$(function(){
    /*
     * Xóa class 'active' của những item không được click trong cùng 1 button group
     */
    $('.btn-group .btn').click(function(){ 
        $(this).addClass('active').siblings().removeClass('active');
    }); 
    
    /*
     * Submit form khi input có class .submit-on-change thay đổi giá trị
     */
    $('.submit-on-change').change(function(){
        $(this).parents('form').submit();
    });
})