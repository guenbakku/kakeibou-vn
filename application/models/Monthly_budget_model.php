<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Monthly_budget_model extends App_model
{
    public function get_table(): string
    {
        return 'categories';
    }

    /**
     * Lấy số tiền đã chi thực tế trong tháng hiện tại theo từng category.
     *
     * @return array [category_id => actual_amount]
     */
    public function get_month_actual_outgo_by_category(): array
    {
        $results = $this->db->select('category_id')
            ->select('SUM(ABS(amount)) as actual_amount', false)
            ->where('cash_flow', 'outgo')
            ->where('skip_month_estimated', 0)
            ->where('YEAR(date)', date('Y'), false)
            ->where('MONTH(date)', date('n'), false)
            ->group_by('category_id')
            ->get('inout_records')
            ->result_array()
        ;

        return array_column($results, 'actual_amount', 'category_id');
    }

    /**
     * Gắn actual_amount và percent vào từng category,
     * đồng thời tính summary tổng hợp.
     *
     * @param array $categories danh sách category từ Category_model::get()
     *
     * @return array ['categories' => [...], 'summary' => [...]]
     */
    public function build_budget_data(array $categories): array
    {
        $actual_by_category = $this->get_month_actual_outgo_by_category();

        foreach ($categories as &$cat) {
            $estimated = (int) $cat['month_estimated_amount'];
            $actual = (int) ($actual_by_category[$cat['id']] ?? 0);
            $cat['actual_amount'] = $actual;
            $cat['percent'] = $estimated > 0
                ? max(0, (int) round(($estimated - $actual) / $estimated * 100))
                : 0;
        }
        unset($cat);

        $total_estimated = array_sum(array_column($categories, 'month_estimated_amount'));
        $total_actual = array_sum(array_column($categories, 'actual_amount'));

        $summary = [
            'month_estimated_amount' => $total_estimated,
            'actual_amount' => $total_actual,
            'percent' => $total_estimated > 0
                ? max(0, (int) round(($total_estimated - $total_actual) / $total_estimated * 100))
                : 0,
        ];

        return ['categories' => $categories, 'summary' => $summary];
    }
}
