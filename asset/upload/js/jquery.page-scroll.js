/**
 * Tự động cuộn trang (theo trục tung) đến vị trí của item được chỉ định
 */
function pageScroll(target, offset, anchorName){
    $(function(){
        
        if (target.length == 0){
            return false;
        }
        
        offset = typeof offset !== 'undefined'? offset : 0;
        anchorName = typeof anchorName !== 'undefined'? anchorName : 'page-scroll';
        
        var targetDOM = $('['+anchorName+'='+target+']');
        if (targetDOM.length > 0){
            console.log($(document).scrollTop());
            var y_coord = targetDOM.offset().top + offset;
            $('body').animate({ scrollTop: y_coord }, "fast");
        }
        
        return false;
    })
}