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
        const props = {};
        props.trigger = this;
        props.targetBackupValueAttrName = 'switcher-target-backup-value';
        props.triggerCurrentValueAttrName = 'switcher-trigger-current-value';

        captureCurrentValueOfTrigger();

        // Setting cho sub switcher
        if (options.subSwitchers.length > 0) {
            for (const i in options.subSwitchers) {
                const subOptions = options.subSwitchers[i];
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
            const previousVal = getPreviousValueOfTrigger();
            const triggerVal = props.trigger.val();
            let isEnable = false;

            if (options.enableValues !== null) {
                isEnable = options.enableValues.indexOf(triggerVal) >= 0
                         ? true : false;
            } else if (options.disableValues !== null) {
                isEnable = options.disableValues.indexOf(triggerVal) >= 0
                         ? false : true;
            }

            // Switch target depend on isEnable value
            if (isEnable) {
                enable(options.targets, triggerVal, previousVal);
                triggerSubSwitchers();
            } else {
                disable(options.targets, triggerVal, previousVal);
                disableAllSubSwitchers();
            }

            captureCurrentValueOfTrigger();
        }).trigger('change');

        return props.trigger;

        //---------------------------------------------------
        // Helper method
        //---------------------------------------------------
        function captureCurrentValueOfTrigger() {
            props.trigger.attr(props.triggerCurrentValueAttrName, props.trigger.val());
        }

        function getPreviousValueOfTrigger() {
            return props.trigger.attr(props.triggerCurrentValueAttrName);
        }

        function backupCurrentValue(selector) {
            $(selector).each(function(i, dom){
                // Skip if this target's value is already backed up
                if ($(dom).is('['+props.targetBackupValueAttrName+']')) {
                    return false;
                }
                const currentVal = $(dom).val();
                $(dom).attr(props.targetBackupValueAttrName, currentVal);
            });
        }

        function restoreCurrentValue(selector) {
            $(selector).each(function(i, dom){
                // Skip if this target's value is not backed up
                if (!$(dom).is('['+props.targetBackupValueAttrName+']')) {
                    return false;
                }

                const currentVal = $(dom).attr(props.targetBackupValueAttrName);

                // Only restore if the currentVal is one of <option> tag's value
                $(dom).children('option').each(function(){
                    if (this.value == currentVal) {
                        $(dom).val(currentVal);
                        return false;
                    }
                });

                $(dom).removeAttr(props.targetBackupValueAttrName);
            });
        }

        function disable(targets, triggerVal, previousVal) {
            const result = options.onBeforeDisable(triggerVal, previousVal);
            if (result) {
                for (const i in targets) {
                    const selector = targets[i];
                    backupCurrentValue(selector);
                    $(selector).each(function(i, dom){
                        $(dom).val(null);
                        $(dom).prop('disabled', true);
                    });
                }
            }
        }

        function enable(targets, triggerVal, previousVal) {
            const result = options.onBeforeEnable(triggerVal, previousVal);
            if (result) {
                for (const i in targets) {
                    const selector = targets[i];
                    restoreCurrentValue(selector);
                    $(selector).each(function(i, dom){
                        $(dom).prop('disabled', false);
                    });
                }
            }
        }

        function triggerSubSwitchers() {
            for (const i in options.subSwitchers) {
                const subOptions = options.subSwitchers[i];
                $(subOptions.selector).trigger('change');
            }
        }

        function disableAllSubSwitchers() {
            for (const i in options.subSwitchers) {
                const subOptions = options.subSwitchers[i];
                disable(subOptions.targets);
            }
        }

        function setDefaultOptions(options) {
            options = typeof options === 'object'
                ? options : {};
            options.targets = typeof options.targets !== 'undefined'
                ? options.targets : null;
            options.enableValues = typeof options.enableValues !== 'undefined'
                ? options.enableValues : null;
            options.disableValues = typeof options.disableValues !== 'undefined'
                ? options.disableValues : null;
            options.subSwitchers = typeof options.subSwitchers === 'object'
                ? options.subSwitchers : [];
            options.selector = typeof options.selector === 'string'
                ? options.selector : null;
            options.onBeforeDisable = typeof options.onBeforeDisable === 'function'
                ? options.onBeforeDisable : () => true;
            options.onBeforeEnable = typeof options.onBeforeEnable === 'function'
                ? options.onBeforeEnable : () => true;

            for (const i in options.subSwitchers) {
                options.subSwitchers[i] = setDefaultOptions(options.subSwitchers[i]);
            }

            return options;
        }
    }
})(jQuery);
