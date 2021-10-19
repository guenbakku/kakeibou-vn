/**
 * Sortable
 */
 
(function ($) {
    $.fn.sortableWrapper = function() {
        $(this).sortable({
            handle: '.handle',
            items: '.sort',
            axis: 'y',
            start: function(event, ui) {
                // Save start_pos of target item to use in below reorder
                var start_pos = ui.item.index();
                ui.item.data('start_pos', start_pos);
            },
            update: function(event, ui) {
                var stop_pos = ui.item.index();
                var start_pos = ui.item.data('start_pos');
                var table = $(ui.item).parents('table');
                
                // we do not anything if position of target item does not change
                if (start_pos == stop_pos) {
                    return;
                }
                
                // we only update order of items between start_pos and stop_pos
                if (start_pos < stop_pos) {
                    for(var i=stop_pos; i >= start_pos; i--){
                        table.find('tr').eq(i).find('input[data-role=order_no]').val(i);
                    }
                }
                else {
                    for(var i=stop_pos; i <= start_pos; i++){
                        table.find('tr').eq(i).find('input[data-role=order_no]').val(i);
                    }
                }
            },
        });
    }
})(jQuery)