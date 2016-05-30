/**
 * Hiển thị PopUp
 * Require: jquery.js Plugin
 * Cách dùng: 
 *      obj = new creatPopup("ID html của div Popup") -> Khởi tạo Popup
 *      obj.display("Title", "Content", "Width") -> Hiển thị popup với tiêu đề, nội dung, độ rộng (px) của nội dung
 *      obj.changeContent("New Content")         -> Thay đổi nội dung của popup obj
 *      obj.changeTitle("New Title")             -> Thay đổi tiêu đề của popup obj
 */
function createPopup(name) {
    
    name = typeof (name) !== 'undefined'? name : "popup";
    
    //Tắt hết các popUp đang bật
    $('.popupBlackScreen').each(function(){
        $(this).css('display', 'none');
    });
    
    //kiểm tra name đã tồn tại chưa, nếu đã tồn tại thì tăng index
    var index = 0;
    var popupName = name;
    while($("[popup-name=" + popupName + "]").length>0){
        index++
        popupName = name + "-" + index;
    }
    
    //kiểm tra ID đã tồn tại chưa, nếu đã tồn tại thì tăng index
    var index = 0;
    var popupID = "popup";
    while($("#" + popupID).length>0){
        index++
        popupID = "popup-" + index;
    }
    
    //Tạo div Popup
    var popupHTML = "";
    popupHTML += '<div class="popupBlackScreen" id=' + popupID + ' popup-name="' + popupName + '">';
    popupHTML += '  <div class="popupBox">';
    popupHTML += '      <div class="popupTitle" style="height:35px">';
    popupHTML += '          <span></span>';
    popupHTML += '          <div class="popupClose" title="Close"></div>';
    popupHTML += '      </div>';
    popupHTML += '      <div class="popupContent"></div>';
    popupHTML += '  </div>';
    popupHTML += '</div>';
    $("body").append(popupHTML);
    
    var popupObj = $("[popup-name=" + popupName + "]");
    
    //Tat Popup khi bam nut Close
    popupObj.find(".popupClose").bind("click", function(){
        popupObj.fadeOut(300);
    });
        
    //Tat Popup khi bam ra ngoai vung ResponseBox
    popupObj.bind("click", function(event){
        var id = event.target.id;
        if (id.match("popup")) popupObj.fadeOut(300);
    });
    
    this.display = function(title, content, width){
        
        //Kick thuoc blackScreen
        //var popupBS_H = $("#top_bg").outerHeight() + $("#middle_bg_1").outerHeight() + $("#middle_bg_hr12").outerHeight() + $("#middle_bg_2").outerHeight() + $("#footer_bg").outerHeight();
        var popupBS_H = $(window).height();
        var popupBS_W = $(window).width();
        //Kich thuoc content box
        var popupContent_W = (typeof(width) !== 'undefined' && width !== null)? width : (popupBS_W > 640)? 600 : popupBS_W*0.7;
        var popupBox_W     = popupContent_W + 20;     
        //Vi tri content box
        var popupBox_T = 50;
        var popupBox_L = $(window).scrollLeft() + ($(window).width()-popupBox_W )/2;
        
        
        popupObj.css({
            "width"  : popupBS_W + "px",
            "height" : popupBS_H + "px",
        });
        popupObj.find(".popupBox").css({
            "width"  : popupBox_W + "px",
            "top"    : popupBox_T + "px",
            "left"   : popupBox_L + "px",
        });
        popupObj.find(".popupTitle, .popupContent").css({
            "width"  : popupContent_W + "px",
        });
        
        this.changeContent(content);
        this.changeTitle(title);
        
        popupObj.stop(true).hide().fadeIn(300);

    }  
    
    this.changeContent = function(content){
        popupObj.find(".popupContent").html(content);
    }
    
    this.changeTitle = function(title){
        popupObj.find(".popupTitle").find("span").html(title);
    }

}

/*
 *---------------------------------------------------------------------------------------------------
 * Hiển thị PopUp tự động.
 * Sử dụng Session Cookie
 *      ->Nếu client browser có bật cookie: chỉ hiện Popup 1 lần, lúc session được tạo
 *      ->Nếu client browser không bật cookie: hiện Popup mỗi khi request  
 * Require: jquery.js Plugin & jquery.cookie.js Plugin
 * 
 * @param   string : tiêu đề của Popup
 * @param   string : url tới file chứa nội dung html của Popup
 * @param   mixed  : các tùy chọn khác
 *                   width      -> chiều rộng popup
 *                   delay      -> thời gian chờ từ lúc load page xong đến khi show popup (ms)
 *                   name       -> tên của cookie để điều khiển hiển thị popup
 *                   expires    -> thời gian chờ giữa 2 lần hiển thị cookie (ngày).
 *                                 nếu nhập 0, popup sẽ bật mỗi khi load page.
 *                                 nếu nhập null hoặc không nhập, popup sẽ bật mỗi khi bắt đầu một session mới.
 *---------------------------------------------------------------------------------------------------                    
 */
function popupAuto(title, file, option){
    
    var ini = {
        width    : null,
        delay    : 0,
        name     : file,
        expires  : null,
        path     : '/',
    };
    
    for (var k in option){
        if (ini.hasOwnProperty(k))
            ini[k] = option[k];
    }
    
    if (ini.expires !== 0){
        // Kiểm tra xem popUp đã bật chưa?
        var resTime = parseFloat($.cookie(ini.name));
        if(resTime) return false;
    }
    
    // Bật Popup
    setTimeout(function(){
        $.get(file,function(data){
            popup = new createPopup("popupAuto"); 
            popup.display(title, data, ini.width)
        })
    }, ini.delay);
    
    if (ini.expires !== 0){
        // Lưu cookie điều khiển
        c_props = {
            expires : ini.expires,
            path    : ini.path,
        };
        $.cookie(ini.name, 1, c_props);
    }
}