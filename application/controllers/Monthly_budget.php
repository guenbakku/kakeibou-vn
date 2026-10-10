<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Monthly_budget extends MY_Controller
{
    protected $ctrl_base_url = 'setting';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('monthly_budget_model');
    }

    public function index()
    {
        $inout_type_id = array_flip($this->inout_model::$INOUT_TYPE)['Chi'];
        $categories = $this->category_model->get(null, ['inout_type_id' => $inout_type_id]);
        $budget_data = $this->monthly_budget_model->build_budget_data($categories);

        $view_data['categories'] = $budget_data['categories'];
        $view_data['summary'] = $budget_data['summary'];
        $view_data['title'] = 'Dự định chi tháng này';
        $view_data['url'] = [
            'edit' => $this->base_url(['edit']),
            'back' => base_url('setting'),
        ];
        $this->template->write_view('MAIN', 'monthly_budget/index', $view_data);
        $this->template->render();
    }

    public function edit()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');
                $data = $this->input->post('categories');

                foreach ($data as $i => $category) {
                    $this->form_validation->set_rules(
                        sprintf('categories[%d][month_estimated_amount]', $i),
                        sprintf('Dự định chi tháng này của %s', $category['name']),
                        'required|trim|greater_than_equal_to[0]'
                    );
                    $data[$i]['is_month_fixed_money'] = isset($category['is_month_fixed_money']) ? (int) $category['is_month_fixed_money'] : 0;
                }
                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }

                $this->category_model->edit_batch($data, 'id');
                $this->flash->success(settings('succ_edit_month_estimated_outgo'));

                return redirect($this->base_url());
            } catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }

        $inout_type_id = array_flip($this->inout_model::$INOUT_TYPE)['Chi'];
        $view_data['categories'] = $this->category_model->get(null, ['inout_type_id' => $inout_type_id]);
        $view_data['title'] = 'Dự định chi tháng này';
        $view_data['url'] = [
            'form' => $this->base_url(['edit']),
            'back' => $this->base_url(),
        ];
        $_POST['categories'] = $view_data['categories'];
        $this->template->write_view('MAIN', 'monthly_budget/form', $view_data);
        $this->template->render();
    }
}
