<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contact extends Render_Controller
{

    public function index()
    {
        // Page Settings
        $this->title = 'Contact';
        $this->navigation = ['Contact'];
        $this->plugins = ['datatables'];

        // Breadcrumb setting
        $this->breadcrumb_1 = 'Home';
        $this->breadcrumb_1_url = base_url() . 'admin/home';
        $this->breadcrumb_3 = 'Home';
        $this->breadcrumb_3_url = '#';
        $this->breadcrumb_4 = 'Kelebihan';
        $this->breadcrumb_4_url = base_url() . 'home/contact';

        // content
        $this->content      = 'admin/home/contact';
        $this->data['maps'] = $this->key_get($this->key_contact_maps);

        // Send data to view
        $this->render();
    }
    public function update_maps()
    {
        // get post
        $value = $this->input->post("value", false);
        // update
        $result = $this->key_set($this->key_contact_maps, $value, null);

        $this->output_json($result);
    }

    public function ajax_data()
    {
        $order = ['order' => $this->input->post('order'), 'columns' => $this->input->post('columns')];
        $start = $this->input->post('start');
        $draw = $this->input->post('draw');
        $draw = $draw == null ? 1 : $draw;
        $length = $this->input->post('length');
        $cari = $this->input->post('search');

        if (isset($cari['value'])) {
            $_cari = $cari['value'];
        } else {
            $_cari = null;
        }

        $data = $this->model->getAllData($draw, $length, $start, $_cari, $order)->result_array();
        $count = $this->model->getAllData(null, null, null, $_cari, $order, null)->num_rows();

        $this->output_json(['recordsTotal' => $count, 'recordsFiltered' => $count, 'draw' => $draw, 'search' => $_cari, 'data' => $data]);
    }

    public function getList()
    {
        $result = $this->model->getList();
        $code = $result ? 200 : 500;
        $this->output_json($result, $code);
    }

    public function insert()
    {
        $this->db->trans_start();
        $icon = $this->input->post("icon");
        $title = $this->input->post("title");
        $column = $this->input->post("column");
        $status = $this->input->post("status");
        $description = $this->input->post("description");
        $user_id = $this->id;
        $result = $this->model->insert($user_id, $title, $icon, $column,  $description, $status);

        $this->db->trans_complete();
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    public function update()
    {
        $id = $this->input->post("id");
        $icon = $this->input->post("icon");
        $title = $this->input->post("title");
        $column = $this->input->post("column");
        $status = $this->input->post("status");
        $description = $this->input->post("description");
        $user_id = $this->id;
        $result = $this->model->update($id, $user_id, $title, $icon, $column,  $description, $status);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    public function delete()
    {
        $id = $this->input->post("id");
        $result = $this->model->delete($this->id, $id);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    public function cari()
    {
        $key = $this->input->post('q');
        // jika inputan ada
        if ($key) {
            $this->output_json([
                "results" => $this->model->cari($key)
            ]);
        } else {
            $this->output_json([
                "results" => []
            ]);
        }
    }

    function __construct()
    {
        parent::__construct();
        $this->sesion->cek_session();
        $akses = ['Super Admin'];
        $get_lv = $this->session->userdata('data')['level'];
        if (!in_array($get_lv, $akses)) {
            redirect('my404', 'refresh');
        }
        $this->id = $this->session->userdata('data')['id'];
        $this->photo_path = './files/home/excess/';
        $this->load->model("admin/home/ContactModel", 'model');
        $this->default_template = 'templates/dashboard';
        $this->load->library('plugin');
        $this->load->helper('url');
    }
}