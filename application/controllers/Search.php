<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {
        
	public function index()
    {   
        $result = null;
        if (!empty($this->input->get()))
        {
            try {
                $_POST = $this->input->get();
                
                // Điều kiện tìm kiếm => bắt buộc hay không
                // Khi tìm kiếm, ít nhất một trong những điều kiện 
                // bắt buộc được nhập mới thực hiện tìm kiếm 
                $condition_keys = array(
                    'memo_or_amount'    => true,
                    'player'            => true, 
                    'inout_type'        => true, 
                    'from'              => true, 
                    'to'                => true,
                    'hide_pair_inout'   => false,
                );
                $can_excute_search = false;
                
                foreach ($condition_keys as $key => $is_required){
                    $val = trim($this->input->get($key));
                    if (!empty($val)){
                        if ($is_required){
                            $can_excute_search = true;
                        }
                        
                        if ($key === 'memo_or_amount'){
                            if (is_numeric($val)){
                                $this->search_model->amount = $val;
                            }
                            else {
                                $this->search_model->memo = $val;
                            }
                        }
                        else {
                            $this->search_model->$key = $val;
                        }
                    }
                    else {
                        $this->search_model->$key = null;
                    }
                }
                
                if (!$can_excute_search){
                    throw new Exception('Chưa nhập điều kiện tìm kiếm');
                }
                
                $result = $this->search_model->search();
            }
            catch (Exception $e) {
                $this->flash->error($e->getMessage());
            }
        }
        
        $view_data['list']        = $result;
        $view_data['total_items'] = count($view_data['list']);
        $view_data['form_url']    = base_url().strtolower(__CLASS__);
        $view_data['title']       = 'Tìm kiếm chi tiêu';
        $view_data['show_form']   = $result===null? true : false;
        $view_data['select']      = array(
            'players'     => array(0=> 'Tất cả') + $this->app_model->getSelectTagData('user_id'),
            'inout_types' => array(0=> 'Tất cả') + $this->app_model->getSelectTagData('inout_type_id'),
        );
		$this->template->write_view('MAIN', 'search/search', $view_data);
        $this->template->render();
	}
    
}
