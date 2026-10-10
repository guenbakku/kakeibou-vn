<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category extends MY_Controller
{
    protected $ctrl_base_url = 'setting';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
    }

    public function index()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data = $this->input->post('categories');
            $this->category_model->edit_batch($data);
            $this->flash->success(settings('succ_edit_category_order'));

            return redirect($this->referer->get());
        }

        $inout_type_id = (int) $this->input->get('inout_type_id');
        if (!in_array($inout_type_id, [1, 2])) {
            $inout_type_id = 1;
        }

        $view_data['title'] = 'Quản lý danh mục';
        $view_data['categories'] = $this->category_model->get(null, ['inout_type_id' => $inout_type_id]);
        $view_data['inout_type_id'] = $inout_type_id;
        $view_data['url'] = [
            'form' => $this->base_url(),
            'subNav' => [
                $this->base_url().'?inout_type_id=1',
                $this->base_url().'?inout_type_id=2',
            ],
            'add' => $this->base_url(['add', '?inout_type_id=']).$inout_type_id,
            'edit' => $this->base_url(['edit', '%s']),
            'back' => base_url('setting'),
        ];
        $this->template->write_view('MAIN', 'category/home', $view_data);
        $this->template->render();
    }

    public function add()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');

                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }

                $this->category_model->add($this->input->post());
                $this->flash->success(settings('succ_add_category'));

                return redirect($this->referer->getSession());
            } catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        } else {
            // Lưu referer của page access đến form
            $this->referer->saveSession();
            $_POST['inout_type_id'] = $this->input->get('inout_type_id');
        }

        $view_data['title'] = 'Thêm danh mục';
        $view_data['select'] = [
            'inout_types' => $this->inout_type_model->get_select_tag_data(),
        ];
        $view_data['url'] = [
            'form' => $this->base_url(__FUNCTION__),
            'back' => $this->referer->getSession(null, false),
        ];

        $this->template->write_view('MAIN', 'category/form', $view_data);
        $this->template->render();
    }

    public function edit(int $id)
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');

                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }

                $this->category_model->edit($id, $this->input->post());
                $this->flash->success(settings('succ_edit_category'));

                $inout_type_id = $this->category_model->get($id)['inout_type_id'] ?? 1;

                return redirect($this->base_url().'?inout_type_id='.$inout_type_id);
            } catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        } else {
            $category_data = $this->category_model->get($id);

            if (empty($category_data)) {
                show_error(settings('err_not_found'));
            }
            $_POST = $category_data;
        }

        $inout_type_id = $_POST['inout_type_id'] ?? 1;

        $view_data['title'] = 'Sửa danh mục';
        $view_data['select'] = [
            'inout_types' => $this->inout_type_model->get_select_tag_data(),
        ];
        $view_data['url'] = [
            'form' => $this->base_url([__FUNCTION__, $id]),
            'del' => $this->base_url(['del_confirm', $id]),
            'back' => $this->base_url().'?inout_type_id='.$inout_type_id,
        ];

        $this->template->write_view('MAIN', 'category/form', $view_data);
        $this->template->render();
    }

    public function del_confirm(int $id)
    {
        if (!is_numeric($id)) {
            show_error(settings('err_bad_request'));
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            return $this->del($id);
        }

        $category_data = $this->category_model->get($id);
        if (empty($category_data)) {
            show_error(settings('err_not_found'));
        }

        $is_category_empty = $this->category_model->is_empty($id);
        $target_categories = ['' => ''] + array_filter(
            $this->category_model->get_select_tag_data($category_data['inout_type_id']),
            fn ($category_id) => $category_id !== $id,
            ARRAY_FILTER_USE_KEY,
        );

        $view_data['title'] = 'Xác nhận xóa danh mục';
        $view_data['url'] = [
            'form' => $this->base_url(['del_confirm', $id]),
            'back' => $this->base_url(['edit', $id]),
        ];
        $view_data['category'] = $category_data;
        $view_data['is_category_empty'] = $is_category_empty;
        $view_data['select'] = [
            'target_categories' => $target_categories,
        ];

        $this->template->write_view('MAIN', 'category/del_confirm', $view_data);
        $this->template->render();
    }

    public function del(int $id)
    {
        if (!is_numeric($id)) {
            show_error(settings('err_bad_request'));
        }

        try {
            if (!$this->category_model->is_empty($id)) {
                $this->load->library('form_validation');

                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }

                $target_category_id = $this->input->post('target_category_id');
                $this->category_model->move_records_and_delete($id, (int) $target_category_id);
            } else {
                $this->category_model->del($id);
            }

            $this->flash->success(settings('succ_del_category'));

            $inout_type_id = $this->category_model->get($id)['inout_type_id'] ?? 1;

            return redirect($this->base_url().'?inout_type_id='.$inout_type_id);
        } catch (AppException $ex) {
            $this->flash->error($ex->getMessage());

            return redirect($this->base_url(['del_confirm', $id]));
        }
    }

    /**
     * API kiểm tra category có phải là loại thu chi cố định hàng tháng hay không.
     */
    public function is_month_fixed_money()
    {
        $category_id = $this->input->get('id');
        if (!ctype_digit($category_id)) {
            show_error(settings('err_bad_request'));
        }

        $result = $this->category_model->is_month_fixed_money($category_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result))
        ;
    }
}
