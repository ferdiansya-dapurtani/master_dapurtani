<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

  function __construct()
{
    parent::__construct();
    $this->load->library(array('template', 'cart'));
    $this->load->model('app');
}

	public function index()
	{
    $this->load->view('user/cart');
	}

  public function add()
  {
    if (is_numeric($this->uri->segment(3)))
    {
      $id = $this->uri->segment(3);
      $get = $this->app->get_where('t_sayur', array('id_sayur' => $id))->row();

      $data = array(
        'id' => $get->id_sayur,
        'name' => $get->nama_sayur,
        'price' => $get->harga,
        'promo' => $get->promo,
        'weight' => $get->satuan,
        'picture' => $get->gambar,
        'stock' => $get->stock,
        'qty' => 1
      );

      $this->cart->insert($data);

      echo '<script type="text/javascript">window.history.go(-1);</script>';

    } else {
      redirect('home');
    }
  }

  public function update()
  {
     if ($this->uri->segment(3))
     {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('qty', 'Jumlah Pesan', 'required|numeric');

        if ($this->form_validation->run() == TRUE)
        {
           $data = array(
              'qty' => $this->input->post('qty', TRUE),
              'rowid' => $this->uri->segment(3)
           );

           $this->cart->update($data);

        redirect('home');
        } else {
          $this->template->user('user/index');
        }

     } else {
        redirect('home');
     }

  }

  public function delete()
  {
     if ($this->uri->segment(3))
     {

        $rowid = $this->uri->segment(3);

        $this->cart->remove($rowid);

        redirect('home');
     } else {
        redirect('home');
     }
  }
}
