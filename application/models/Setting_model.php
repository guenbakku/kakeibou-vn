<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting_model extends App_Model
{
    public const TABLE = 'settings';

    /**
     * Lấy setting.
     *
     * @param  string/array : item setting muốn lấy
     * @param  string       : column muốn lấy
     * @param null|mixed $items
     *
     * @return array : (key => val)
     */
    public function get($items = null, ?string $col = null)
    {
        if (is_string($items)) {
            $items = [$items];
        }

        if (is_array($items)) {
            foreach ($items as $key) {
                $this->db->where('item', $key);
            }
        }

        $list = $this->db->get(self::TABLE)->result_array();

        // Json Decode Value
        if (isset($list[0]['value'])) {
            foreach ($list as $item => $val) {
                $val['value'] = json_decode($val['value'], true);
                $list[$item] = $val;
            }
        }

        return array_gen_key($list, 'item', $col);
    }

    /*
     * Lưu setting
     *
     * @param   array
     * @return  void
     */
    public function edit(array $data)
    {
        foreach ($data as $k => $v) {
            $this->db->where('item', $k)
                ->update(self::TABLE, ['value' => json_encode($v)])
            ;
        }
    }
}
