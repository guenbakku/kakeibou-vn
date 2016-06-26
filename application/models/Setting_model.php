<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends App_Model {
    
    const TABLE        = 'settings';
    
    /*
     *--------------------------------------------------------------------
     * Lấy setting
     *
     * @param  string/array : item muốn lấy
     * @return array        : (key => val)
     *--------------------------------------------------------------------
     */
    public function get($arr = null, $col = null){
        
        if (is_string($arr)){
            $arr =array($arr);
        }
        
        if (is_array($arr)){
            foreach ($arr as $key){
                $this->db->where('item', $key);
            }
        }
        
        $list = $this->db->get(self::TABLE)->result_array();
        
        // Json Decode Value
        if (isset($list[0]['value'])){
            foreach ($list as $item => $val){
                $val['value'] = json_decode($val['value'], true);
                $list[$item] = $val;
            }
        }
        
        return array_gen_key($list, 'item', $col);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lưu setting
     *
     * @param   array:
     * @return  void
     *--------------------------------------------------------------------
     */
    public function edit($data)
    {
        foreach ($data as $k => $v){
            $this->db->where('item', $k)
                     ->update(self::TABLE, array('value'=>json_encode($v)));
        }
    }
}