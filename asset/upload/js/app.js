/**
 * Cashbook's javascript main factory
 */

var Cashbook = {};

(function(Cashbook){
    /*
     * Quyết định mode cho form
     */
    Cashbook.submitButton = function(btn, type) {
        var form = $(btn).parents('form');
        var action = form.attr('action');

        switch (type) {
            case 'continue':
                action = Cashbook.insertParam(action, 'continue', '1');
                break;
            case 'delete':
                if (!confirm('Chắc chắn muốn xóa?')) {
                    return false;
                }
                action = Cashbook.insertParam(action, 'delete', '1');
                break;
            default:
                break;
        }

        $(btn).attr('disabled', true);
        form.attr('action', action);
        form.submit();
    }

    /*
     * Thêm param vào phần query của một url
     */
    Cashbook.insertParam = function (url, key, value) {
        key = encodeURI(key); value = encodeURI(value);

        var splitted_url = url.split('?');
        var domain = splitted_url[0];
        var kvp = typeof splitted_url[1] == 'undefined'? [] : splitted_url[1].split('&');

        var i=kvp.length; var x; while(i--) {
            x = kvp[i].split('=');

            if (x[0]==key)
            {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if(i<0) {kvp[kvp.length] = [key,value].join('=');}

        return domain + '?' + kvp.join('&');
    }
}(Cashbook));
