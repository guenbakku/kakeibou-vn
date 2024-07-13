<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting_model extends App_Model
{
    public function get_table(): string
    {
        return 'settings';
    }

    /**
     * Lấy setting.
     *
     * @param string|string[] $items item setting muốn lấy
     * @param string          $col   column muốn lấy
     *
     * @return array [key => val]
     */
    public function get(array|string $items, ?string $col = null): array
    {
        if (is_string($items)) {
            $items = [$items];
        }

        if (is_array($items)) {
            foreach ($items as $key) {
                $this->db->where('item', $key);
            }
        }

        $list = $this->db->get($this->get_table())->result_array();

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

     */
    public function edit(array $data)
    {
        foreach ($data as $k => $v) {
            $this->db->where('item', $k)
                ->update($this->get_table(), ['value' => json_encode($v)])
            ;
        }
    }
}
