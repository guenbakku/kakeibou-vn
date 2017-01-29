/**
 * Cashbook's javascript main factory
 */

var Cashbook = {};
 
(function(Cashbook){
    /*
     * Quyết định mode cho form
     */
    Cashbook.submitbutton = function(btn, type) {
        var form = $(btn).parents('form');
        var action = form.attr('action');
        
        switch (type) {
            case 'submit':
            case 'add':
            case 'edit':
                action = Cashbook.insertParam(action, 'continue', '0');
                break;
            case 'continue':
                action = Cashbook.insertParam(action, 'continue', '1');
                break;
            case 'delete':
                if (!confirm('Chắc chắn muốn xóa?')) {
                    return false;
                }
                action = Cashbook.insertParam(action, 'delete', '1');
                break;
        }
        
        form.attr('action', action);
        form.submit();
    }
    
    /*
     * Thêm param vào phần query của một url
     */
    Cashbook.insertParam = function (url, key, value) {
        key = encodeURI(key); value = encodeURI(value);
                
        var splited_url = url.split('?');
        var domain = splited_url[0];
        var kvp = typeof splited_url[1] == 'undefined'? [] : splited_url[1].split('&');
        
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