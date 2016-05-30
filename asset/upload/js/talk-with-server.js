/**
 * Thực hiện giao tiếp giữa client & server thông qua Ajax
 * Require:
 *      asset/upload/js/jquery-1.10.2.min.js (or higher version)
 *      asset/upload/js/popup.js
 */

function talk_with_server(url, send_data, u_ini){
    
    //Default Setting
    var ini = {
        'show_popup'        : false,
        'send_type'         : 'post',
        'get_type'          : 'json',
        'message'           : 'Loading <img src="http://vimirai.com/images/loading.gif">',
        'title'             : 'Message',
        'async'             : true,
        'contentType'       : 'application/x-www-form-urlencoded; charset=UTF-8',
        'processData'       : true,
        'callback'          : '',
        'callback_param'    : '',
    }
    
    //Ghi đè user setting sang default setting
    for (var key in u_ini){
        if (typeof(ini[key] !== 'undefined'))
            ini[key] = u_ini[key];
    }
    
    var popup = new createPopup;
    
    if (ini.show_popup === true)
        popup.display(ini.title, ini.message);
    
    //console.log(ini);
    
    $(document).ajaxError(function(XMLHttpRequest, textStatus, errorThrown){
        alert('Có lỗi xảy ra trong quá trình truyền dữ liệu [talk_with_server()] - ' + errorThrown.message)
    });
    
    $.ajax({
        url         : url,
        async       : ini.async,
        type        : ini.send_type,
        dataType    : ini.get_type,
        contentType : ini.contentType,
        processData : ini.processData,
        data        : send_data,
        success     : function (respone) {
            if (ini.show_popup === true){
                popup.display(respone.title, respone.message);
            }
            else if (respone.success == false){
                popup.display(respone.title, respone.message);
            }
            
            if (typeof(ini.callback) === 'function')
                ini.callback(respone, ini.callback_param);
        },
    });

}