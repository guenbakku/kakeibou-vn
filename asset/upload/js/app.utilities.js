/**
 * Utilities.
 * This contains the js snippets that are automatically applied to all screens
 */
$(function(){
    /**
     * Xóa class 'active' của những item không được click trong cùng 1 button group
     */
    $('.btn-group .btn').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
    });

    /**
     * Submit form khi input có class .submit-on-change thay đổi giá trị
     */
    $('.submit-on-change').change(function(){
        $(this).parents('form').submit();
    });

    /**
     * Workaround to make the Reset button of Datepicker on iOS can clear the inputted value to blank.
     * Many thanks to: https://stackoverflow.com/a/64886383
     */
    $(window).on("load", function(){
        const dateFields = document.querySelectorAll('input[type="date"]');
        for (const dateField of dateFields) {
            const value = dateField.value;
            dateField.defaultValue = '';
            dateField.value = value;
        }
    });

    /**
     * Auto focus to specific input field
     */
    $(window).on("load", function(){
        $('.autofocus').focus();
    });
})
