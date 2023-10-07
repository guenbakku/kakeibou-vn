<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('search_model');
    }

    public function index()
    {
        if (!empty($this->input->get())) {
            try {
                $_POST = $this->input->get();

                // Điều kiện tìm kiếm => bắt buộc hay không
                // Khi tìm kiếm, ít nhất một trong những điều kiện
                // bắt buộc được nhập mới thực hiện tìm kiếm
                $condition_keys = [
                    'memo_or_amount'    => true,
                    'player'            => true,
                    'inout_type'        => true,
                    'inout_from'        => true,
                    'inout_to'          => true,
                    'modified_from'     => true,
                    'modified_to'       => true,
                    'show_pair_inout'   => false,
                    'only_temp'         => false,
                    'offset'            => false,
                ];
                $can_excute_search = false;

                foreach ($condition_keys as $key => $is_required) {
                    $val = trim($this->input->get($key));
                    if (!empty($val)) {
                        if ($is_required) {
                            $can_excute_search = true;
                        }

                        if ($key === 'memo_or_amount') {
                            if (is_numeric($val)) {
                                $this->search_model->amount = $val;
                            }
                            else {
                                $this->search_model->memo = $val;
                            }
                        }
                        elseif ($key == 'offset') {
                            $this->search_model->$key = (int)$val;
                        }
                        else {
                            $this->search_model->$key = $val;
                        }
                    }
                    else {
                        $this->search_model->$key = null;
                    }
                }

                if (!$can_excute_search) {
                    throw new AppException('Chưa nhập điều kiện tìm kiếm');
                }

                $this->search_model->search();
            }
            catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }

        $view_data['result']    = $this->search_model->result;
        $view_data['current_num'] = is_array($this->search_model->result) ? count($this->search_model->result) : 0;
        $view_data['num_of_results'] = $this->search_model->num_of_results;
        $view_data['results_sum'] = $this->search_model->results_sum;
        $view_data['title'] = 'Tìm kiếm chi tiêu';
        $view_data['select'] = [
            'players'     => [0 => 'Tất cả'] + $this->user_model->get_select_tag_data(),
            'inout_types' => [0 => 'Tất cả'] + $this->inout_type_model->get_select_tag_data(),
        ];
        $view_data['url'] = [
            'form'      => $this->base_url(),
            'edit'      => base_url(['inout', 'edit', '%s']),
            'back'      => base_url(),
            'next_page' => $this->search_model->next_page_url(),
        ];

        $this->template->write_view('MAIN', 'search/home', $view_data);
        $this->template->render();
    }
}
