/**
 * Disable/Enable input dựa vào giá trị hiện tại của một input khác
 * Hiện tại chỉ sử dụng được đối với những loại target sau: 
 *      input text, textarea, select.
 * Require:
 *      asset/upload/js/jquery-1.10.2.min.js (or higher version)
 */

(function($){
    $.fn.switcher = function(options) {
        options = setDefaultOptions(options);

        // Define global properties
        var props = {};
        props.trigger     = this;
        props.tmpAttrName = 'switcher-tmp';
        
        // Setting cho sub switcher
        if (options.subSwitchers.length > 0) {
            for (var i in options.subSwitchers) {
                var subOptions = options.subSwitchers[i];
                $(subOptions.selector).switcher(subOptions);
            }
        }
        
        // Array-lize options values
        if (options.targets !== null && typeof options.targets !== 'object') {
            options.target = [options.targets];
        }
        if (options.enableValues !== null && typeof options.enableValues !== 'object') {
            options.enableValues = [options.enableValues];
        }
        if (options.disableValues !== null && typeof options.disableValues !== 'object') {
            options.disableValues = [options.disableValues];
        }
        
        // Here we go ...
        props.trigger.on('change', function(evt){
            if (options.targets === null) {
                console.error('targets must be defined');
                return;
            }
            
            if (options.enableValues === null 
                && options.disableValues === null)
            {
                console.error('enableValues or disableValues must be defined');
                return;
            }
            
            // Check is enable or not
            var triggerVal = props.trigger.val();
            var isEnable = false;
            if (options.enableValues !== null) {
                isEnable = options.enableValues.indexOf(triggerVal) >= 0
                         ? true : false;
            } else if (options.disableValues !== null) {
                isEnable = options.disableValues.indexOf(triggerVal) >= 0
                         ? false : true;
            }
            
            // Switch target depend on isEnable value
            if (isEnable) {
                enable(options.targets);
                triggerSubSwitchers();
            } else {
                disable(options.targets);
                disableAllSubSwitchers();
            }
        }).trigger('change');
        
        return this;
        
        //---------------------------------------------------
        // Helper method
        //---------------------------------------------------
        
        function backupCurrentValue(selector) {
            $(selector).each(function(i, dom){
                // Skip if this target's value is already backed up
                if ($(dom).is('['+props.tmpAttrName+']')) {
                    return false;
                }
                var currentVal = $(dom).val();
                $(dom).attr(props.tmpAttrName, currentVal);
            });
        }
        
        function restoreCurrentValue(selector) {
            $(selector).each(function(i, dom){
                // Skip if this target's value is not backed up
                if (!$(dom).is('['+props.tmpAttrName+']')) {
                    return false;
                }
                var currentVal = $(dom).attr(props.tmpAttrName);
                $(dom).val(currentVal);
                $(dom).removeAttr(props.tmpAttrName);
            });
        }
        
        function disable(targets) {
            for (var i in targets) {
                var selector = targets[i];
                backupCurrentValue(selector);
                $(selector).each(function(i, dom){
                    $(dom).val(null);
                    $(dom).prop('disabled', true);
                });
            }
        }
        
        function enable(targets) {
            for (var i in targets) {
                var selector = targets[i];
                restoreCurrentValue(selector);
                $(selector).each(function(i, dom){
                    $(dom).prop('disabled', false);
                });
            }
        }
        
        function triggerSubSwitchers() {
            for (var i in options.subSwitchers) {
                var subOptions = options.subSwitchers[i];
                $(subOptions.selector).trigger('change');
            }
        }
        
        function disableAllSubSwitchers() {
            for (var i in options.subSwitchers) {
                var subOptions = options.subSwitchers[i];
                disable(subOptions.targets);
            }
        }
        
        function setDefaultOptions(options) {
            options = typeof options === 'object'
                      ? options : {};
            options.targets       = typeof options.targets !== 'undefined'
                                    ? options.targets : null;
            options.enableValues  = typeof options.enableValues !== 'undefined'
                                    ? options.enableValues : null;
            options.disableValues = typeof options.disableValues !== 'undefined'
                                    ? options.disableValues : null;
            options.subSwitchers  = typeof options.subSwitchers === 'object'
                                    ? options.subSwitchers : [];
            options.selector      = typeof options.selector === 'string'
                                    ? options.selector : null;
            
            for (var i in options.subSwitchers) {
                options.subSwitchers[i] = setDefaultOptions(options.subSwitchers[i]);
            }
            
            return options;
        }
    }
})(jQuery);