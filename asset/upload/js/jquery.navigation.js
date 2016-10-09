/**
 * Xử lý lựa chọn nút đang được active trong một nhóm navigation
 * Nếu tìm được nút đang active sẽ thêm class "active" cho thuộc tính html của nút
 * Require:
 *      asset/upload/js/jquery-1.10.2.min.js (or higher version)
 */

(function($) {
    $.fn.navigation = function(options){
        
        var ini = {
            'defaultIndex' : -1,
            'scanAttr' : 'href',
            'activeClass' : 'active',
        };
        
        // Set giá trị mặc định cho options
        extractOptions();
        
        // Áp dụng script cho từng item nếu đối tượng truyền vào có nhiều item
        var navGroup = this;
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
        
        // Lấy giá trị Options
        function extractOptions(){
            
            if(typeof(options) === 'undefined'){
                return false;
            }
            
            for(var key in ini){
                if(typeof(options[key]) !== 'undefined'){
                    ini[key] = options[key];
                }
            }
        }
        
        // Tìm index của nút có href trùng với 1 phần (toàn bộ) của url trên trình duyệt
        // Nếu có nhiều hơn một nút trùng thì lấy index của nút có href dài hơn (cấp nhỏ hơn)
        function findActiveIndex(buttons){
        
            var current_url = window.location.href;
            var activeIndex = -1;
            for (var i=0; i<buttons.length; i++){
                var href = buttons.eq(i).attr(ini.scanAttr).replace(/(\/*#.*$)|(\/+$)/, ''); //replace: xóa bỏ các phần không cần thiết để có thể đối chiếu chính xác
                if(current_url.indexOf(href) > -1){
                    activeHrefLength = (activeIndex < 0)? -1 : buttons.eq(activeIndex).attr(ini.scanAttr).length;
                    nowHrefLength    = buttons.eq(i).attr(ini.scanAttr).length;
                    activeIndex      = (nowHrefLength > activeHrefLength)? i : activeIndex;
                }
            }
            
            return activeIndex < 0? ini.defaultIndex : activeIndex;
        }
        
        // Set class active cho nút tìm được
        function setActiveButton(buttons, index){
            
            if (index < 0){
                return;
            }
            
            // Dành cho thẻ select
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
            // Dành cho các thẻ khác
            else {
                for (var i=0; i<buttons.length; i++){
                    if (i == index){
                        buttons.eq(i).addClass(ini.activeClass);
                    }
                    else {
                        buttons.eq(i).removeClass(ini.activeClass);
                    }
                }
            }
            
        }
        
        // Chuyển trang đối với navigation bằng nút select
        function changePage(navObj){
            var href = navObj.find('option:selected').attr(ini.scanAttr);
            location.href = decodeURIComponent(href);
        }
    };
})( jQuery );