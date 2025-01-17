/**
 * Tự động cuộn trang (theo trục tung) đến vị trí của item được chỉ định
 */
function pageScroll(target, offset, anchorName){
        
    $(function(){      
        offset = typeof(offset) !== 'undefined'? offset : 0;
        anchorName = typeof(anchorName) !== 'undefined'? anchorName : 'page-scroll';
        
        var targetDOM = $('['+anchorName+'='+target+']');
        
        if (targetDOM.length > 0){
            var y_coord = targetDOM.offset().top + offset;
            $('html, body').animate({scrollTop: y_coord}, {
                'duration': 50,
                'complete': blink(targetDOM),
            });
        }

        return false;
    })
    
    function blink(targetDOM){
        orgColor = targetDOM.css('background-color');
        blinkColor = '#ffff7a';
        targetDOM.css('background-color', blinkColor).stop().animate({'backgroundColor' : orgColor}, {
            'duration' : 1200,
        });
        setTimeout(function(){targetDOM.css('background-color', '')}, 1300);
    }
}