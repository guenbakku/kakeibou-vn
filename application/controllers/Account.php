<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account extends MY_Controller
{
    protected $ctrl_base_url = 'setting';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('account_model');
        $this->load->model('category_model');
    }

    public function index()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data = $this->input->post('categories');
            $this->account_model->edit_batch($data);
            $this->flash->success(settings('succ_edit_account_order'));

            return redirect($this->referer->get());
        }

        $view_data['title'] = 'Quản lý tài khoản';
        $view_data['accounts'] = $this->account_model->get();
        $view_data['url'] = [
            'form' => $this->base_url(),
            'add' => $this->base_url(['add']),
            'edit' => $this->base_url(['edit', '%s']),
            'back' => base_url('setting'),
        ];
        $this->template->write_view('MAIN', 'account/home', $view_data);
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

                $this->account_model->add($this->input->post());
                $this->flash->success(settings('succ_add_account'));

                return redirect($this->referer->getSession());
            } catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        } else {
            // Lưu referer của page access đến form
            $this->referer->saveSession();
        }

        $view_data['title'] = 'Thêm tài khoản';
        $view_data['url'] = [
            'form' => $this->base_url(__FUNCTION__),
            'back' => $this->referer->getSession(null, false),
        ];

        $this->template->write_view('MAIN', 'account/form', $view_data);
        $this->template->render();
    }

    public function edit(int $id)
    {
        if (!is_numeric($id)) {
            show_error(settings('err_bad_request'));
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // Chuyển sang xử lý xóa category nếu lựa chọn xóa
            if ((bool) $this->input->get('delete') === true) {
                return $this->del($id);
            }

            try {
                $this->load->library('form_validation');

                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }

                $this->account_model->edit($id, $this->input->post());
                $this->flash->success(settings('succ_edit_account'));

                return redirect($this->referer->getSession());
            } catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        } else {
            $account_data = $this->account_model->get($id);

            if (empty($account_data)) {
                show_error(settings('err_not_found'));
            }
            $_POST = $account_data;

            // Lưu referer của page access đến form
            $this->referer->saveSession();
        }

        $view_data['title'] = 'Sửa tài khoản';
        $view_data['url'] = [
            'form' => $this->base_url([__FUNCTION__, $id]),
            'del' => $this->base_url(['del', $id]),
            'back' => $this->referer->getSession(null, false),
        ];

        $this->template->write_view('MAIN', 'account/form', $view_data);
        $this->template->render();
    }

    public function del(int $id)
    {
        if (!is_numeric($id)) {
            show_error(settings('err_bad_request'));
        }

        try {
            $this->account_model->del($id);
            $this->flash->success(settings('succ_del_account'));
        } catch (AppException $ex) {
            $this->flash->error($ex->getMessage());
        }

        return redirect($this->referer->getSession());
    }
}
