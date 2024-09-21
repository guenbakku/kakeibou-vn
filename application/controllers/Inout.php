<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inout extends MY_Controller
{
    public function add(string $type)
    {
        if (!$cashFlowName = $this->inout_model->get_cash_flow_name($type)) {
            show_error(settings('err_bad_request'));
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');

                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }

                $insert_ids = $this->inout_model->add($type, $this->input->post());
                $first_insert_id = reset($insert_ids);

                $this->flash->success(sprintf(
                    settings('succ_add_inout_record'),
                    $this->inout_model->get_cash_flow_name($type),
                    $this->base_url(['edit', $first_insert_id])
                ));

                // Xét xem có nhập tiếp hay không
                if ((bool) $this->input->get('continue') === false) {
                    return redirect(base_url());
                }
                $this->form_validation->reset_field_data(['amount', 'memo']);
            } catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }

        $view_data['type'] = $type;
        $view_data['title'] = $cashFlowName;
        $view_data['inout_type_sign'] = $this->inout_model->get_inout_type_sign($type);
        $view_data['select'] = [
            'accounts' => $this->account_model->get_select_tag_data(),
            'players' => $this->user_model->get_select_tag_data(),
            'categories' => $this->category_model->get_select_tag_data($this->inout_model->get_inout_type_id($type)),
            'transfer' => $this->inout_model->get_select_tag_data_for_transfer(),
        ];
        $view_data['url'] = [
            'form' => $this->base_url([__FUNCTION__, $type]),
            'back' => base_url(),
        ];

        $this->template->write_view('MAIN', 'inout/form', $view_data);
        $this->template->render();
    }

    public function edit(int $id)
    {
        if (!is_numeric($id)) {
            show_error(settings('err_bad_request'));
        }

        $ioRecord = $this->inout_model->get($id);

        if (empty($ioRecord)) {
            show_error(settings('err_not_found'));
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // Chuyển sang xử lý xóa record nếu lựa chọn xóa
            if ((bool) $this->input->get('delete') === true) {
                return $this->del($id);
            }

            try {
                $this->load->library('form_validation');

                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }

                $this->inout_model->edit($id, $this->input->post());
                $this->flash->success(sprintf(
                    settings('succ_edit_inout_record'),
                    $this->base_url(['edit', $id])
                ));

                return redirect($this->referer->getSession());
            } catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        } else {
            // Lưu referer của page access đến form
            $this->referer->saveSession();
        }

        if ($ioRecord['cash_flow'] == 'internal') {
            $transfer = $this->inout_model->get_transfer_code($ioRecord);
            $ioRecord['transfer_from'] = $transfer['from'];
            $ioRecord['transfer_to'] = $transfer['to'];
        }

        $ioRecord['amount'] = abs($ioRecord['amount']);
        $_POST = $ioRecord;
        $type = $ioRecord['cash_flow'];
        $view_data = $ioRecord;
        $view_data['type'] = $type;
        $view_data['title'] = 'Chỉnh sửa';
        $view_data['inout_type_sign'] = $this->inout_model->get_inout_type_sign($ioRecord['inout_type_id']);
        $view_data['select'] = [
            'accounts' => $this->account_model->get_select_tag_data(),
            'players' => $this->user_model->get_select_tag_data(),
            'categories' => $this->category_model->get_select_tag_data($this->inout_model->get_inout_type_id($type)),
            'transfer' => $this->inout_model->get_select_tag_data_for_transfer(),
        ];
        $view_data['url'] = [
            'form' => $this->base_url([__FUNCTION__, $id]),
            'back' => base_url(),
        ];

        $this->template->write_view('MAIN', 'inout/form', $view_data);
        $this->template->render();
    }

    public function del(int $id)
    {
        if (!is_numeric($id)) {
            show_error(settings('err_bad_request'));
        }

        $this->inout_model->del($id);
        $this->flash->success(settings('succ_delete_inout_record'));

        return redirect($this->referer->getSession());
    }

    /**
     * API trả về kết quả search memo.
     */
    public function search_memo()
    {
        $keyword = $this->input->get('keyword');
        $cash_flow = $this->input->get('cash_flow');
        $result = $this->inout_model->search_memo($keyword, $cash_flow);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result))
        ;
    }
}
