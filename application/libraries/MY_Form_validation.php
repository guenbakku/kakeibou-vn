<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    
    /**
     * Reset giá trị của key trong property $_field_data.
     * Sử dụng khi muốn show lại form đã qua validate đồng thời reset lại
     * field trong form đó.
     *
     * @param   mixed: danh sách key muốn reset. 
     *                 Nếu null sẽ reset lại toàn bộ field.
     * @return  object: class object.
     */
    public function reset_field_data($keys = null) 
    {
        if ($keys === null) {
            foreach($this->_field_data as &$key) {
                $key['postdata'] = null;
            }
        } else {
            if (!is_array($keys)) {
                $keys = array($keys);
            }
            foreach ($keys as $key) {
                if (isset($this->_field_data[$key])) {
                    $this->_field_data[$key]['postdata'] = null;
                }
            }
        }
        
        return $this;
    }
    
    /**
     * OVERRIDED METHOD.
     * Bỏ cách xử lý đưa callback rule lên ưu tiên cao nhất của xử lý mặc định. 
     * Trừ rule required, tất cả đều được xử lý theo thứ tự nhập vào.
     * 
	 * Prepare rules
	 *
	 * Re-orders the provided rules in order of importance, so that
	 * they can easily be executed later without weird checks ...
	 *
	 * "Callbacks" are given the highest priority (always called),
	 * followed by 'required' (called if callbacks didn't fail),
	 * and then every next rule depends on the previous one passing.
	 *
	 * @param	array	$rules
	 * @return	array
	 */
	protected function _prepare_rules($rules)
	{
		$new_rules = array();

		foreach ($rules as &$rule)
		{
			// Let 'required' always be the first (non-callback) rule
			if ($rule === 'required')
			{
				array_unshift($new_rules, 'required');
			}
			// 'isset' is a kind of a weird alias for 'required' ...
			elseif ($rule === 'isset' && (empty($new_rules) OR $new_rules[0] !== 'required'))
			{
				array_unshift($new_rules, 'isset');
			}
			// Everything else goes at the end of the queue
			else
			{
				$new_rules[] = $rule;
			}
		}

		return $new_rules;
	}
}
