<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Estimated_month_outgo extends MY_Controller
{
    protected $ctrl_base_url = 'setting';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
    }

    public function index()
    {
        $inout_type_id = array_flip($this->inout_model::$INOUT_TYPE)['Chi'];
        $categories = $this->category_model->get(null, ['inout_type_id' => $inout_type_id]);
        $actual_by_category = $this->category_model->get_month_actual_outgo_by_category();

        foreach ($categories as &$cat) {
            $estimated = (int) $cat['month_estimated_amount'];
            $actual = (int) ($actual_by_category[$cat['id']] ?? 0);
            $cat['actual_amount'] = $actual;
            $cat['percent'] = $estimated > 0 ? max(0, (int) round(($estimated - $actual) / $estimated * 100)) : 0;
        }
        unset($cat);

        $view_data['categories'] = $categories;
        $view_data['title'] = 'Dự định chi tháng này';
        $view_data['url'] = [
            'edit' => $this->base_url(['edit']),
            'back' => base_url('setting'),
        ];
        $this->template->write_view('MAIN', 'estimated_month_outgo/index', $view_data);
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
        $this->template->write_view('MAIN', 'estimated_month_outgo/form', $view_data);
        $this->template->render();
    }
}
