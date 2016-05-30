/**
 * Xử lý lựa chọn nút đang được active trong một nhóm navigation
 * Nếu tìm được nút đang active sẽ thêm class "active" cho thuộc tính html của nút
 * Require:
 *      asset/upload/js/jquery-1.10.2.min.js (or higher version)
 */

function navigation(selector, defaultIndex){
    
    // Index của nút sẽ bật mặc định nếu không tìm được nút nào phù hợp
    defaultIndex = (typeof(defaultIndex) !== 'undefined')? defaultIndex : -1;
    
    $(document).ready(function(){
        var navGroup = $(selector);
        for (var i=0; i < navGroup.length; i++){
            var navObj  = navGroup.eq(i);
            var buttons = navObj.children(); 
            setActiveButton(buttons, findActiveIndex(buttons));
            
            // Listen change event để chuyển trang đối với navigation là select button
            if (navObj.is('select')){
                navObj.change(function(evt){
                    changePage($(this));
                });
            }
        }
    })
    
    // Tìm index của nút có href trùng với 1 phần (toàn bộ) của url trên trình duyệt
    // Nếu có nhiều hơn một nút trùng thì lấy index của nút có href dài hơn (cấp nhỏ hơn)
    function findActiveIndex(buttons){
    
        var current_url = window.location.href;
        var activeIndex = -1;
        for (var i=0; i<buttons.length; i++){
            var href = buttons.eq(i).attr('href').replace(/(\/*#.*$)|(\/+$)/, ''); //replace: xóa bỏ các phần không cần thiết để có thể đối chiếu chính xác
            if(current_url.indexOf(href) > -1){
                activeHrefLength = (activeIndex < 0)? -1 : buttons.eq(activeIndex).attr('href').length;
                nowHrefLength    = buttons.eq(i).attr('href').length;
                activeIndex      = (nowHrefLength > activeHrefLength)? i : activeIndex;
            }
        }
        
        return activeIndex;
        
    }
    
    // Set class active cho nút tìm được
    function setActiveButton(buttons, index){
    
        index = (index < 0)? defaultIndex : index;
        
        if (index >= 0){
            if (buttons.eq(0).is('option')){
                for (var i=0; i<buttons.length; i++){
                    if (i == index){
                        buttons.eq(i).prop('selected', true);
                    }
                    else {
                        buttons.eq(i).removeProp('selected');
                    }
                }
            }
            else {
                for (var i=0; i<buttons.length; i++){
                    if (i == index){
                        buttons.eq(i).addClass('active');
                    }
                    else {
                        buttons.eq(i).removeClass('active');
                    }
                }
            }
        }
    }
    
    // Chuyển trang đối với navigation bằng nút select
    function changePage(navObj){
        var href = navObj.find('option:selected').attr('href');
        location.href = decodeURIComponent(href);
    }

}