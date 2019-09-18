<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->library(array('template', 'cart', 'form_validation', 'email', 'session'));
    $this->load->model(array('app', 'admin'));
    $this->load->helper(['form','security']);
  }

  public function index()
  {
    $data['data'] = $this->app->get_limit();
    $this->template->user('user/home', $data);
  }

  public function hubungi()
  {
    $this->load->view('user/hubungi_kami');
  }

  public function tentang()
  {
    $this->load->view('user/tentang_kami');
  }

  public function resep()
  {
    $this->load->view('user/resep');
  }

  public function transaksi()
  {
    if (!$this->session->userdata('user_id')) {
      redirect('home');
    }

    $data['data'] = $this->app->get_where('t_order', ['id_user' => $this->session->userdata('user_id')]);

    $this->template->user('user/nav', $data);
  }

  public function daftar()
  {
    if (!$this->session->userdata('user_id') || !$this->cart->contents())
    {
      redirect('home');
    }
    $this->load->view('user/daftar_promo');
  }

  public function daftar_paket()
  {

    $this->load->library('form_validation');
    $this->form_validation->set_rules('bintang', 'bintang1', "required");
    $this->form_validation->set_rules('bintang', 'bintang2', "required");
    $this->form_validation->set_rules('bintang', 'bintang3', "required");
    $this->form_validation->set_rules('bintang', 'bintang4', "required");
    $this->form_validation->set_rules('bintang', 'bintang5', "required");
    $this->form_validation->set_rules('komentar', "required");

    if ($this->form_validation->run() == TRUE)
    {
      $data = array(
      'nama' => $this->input->post('nama', TRUE),
      'bintang' => $this->input->post('bintang', TRUE),
      'email' => $this->input->post('email', TRUE),
      'telp' => $this->input->post('telp', TRUE),
      'komentar' => $this->input->post('komentar', TRUE),
      );

      $this->app->insert('t_s_pemesanan', $data);
      echo '<script type="text/javascript">alert("Pendaftaran Berhasil");window.location.replace("'.base_url('home').'")</script>';
    } else {
      echo '<script type="text/javascript">alert("Gagal");</script>';
    }
  }

  public function registrasi()
  {
    if($this->input->post('submit', TRUE) == 'Submit')
    {
      $this->load->library('form_validation');

      $this->form_validation->set_rules('nama1', "required|min_length[3]|regex_match[/^[a-zA-Z'.]+$/]");
      $this->form_validation->set_rules('ktg_pelanggan', 'Kategori Pelanggan', "required");
      $this->form_validation->set_rules('user',  "required|min_length[5]|regex_match[/^[a-zA-Z0-9]+$/]");
      $this->form_validation->set_rules('email', "required|valid_email");
      $this->form_validation->set_rules('pass1', "required|min_length[5]");
      $this->form_validation->set_rules('pass2', "required|matches[pass1]");
      $this->form_validation->set_rules('jk', 'Jenis Kelamin', "required");
      $this->form_validation->set_rules('telp', "required|min_length[8]|numeric");
      $this->form_validation->set_rules('alamat', "required|min_length[10]");
      $this->form_validation->set_rules('promo', "required");

      if ($this->form_validation->run() == TRUE)
      {
        $data = array(
        'username' => $this->input->post('user', TRUE),
        'fullname' => $this->input->post('nama1', TRUE),
        'ktg_pelanggan' => $this->input->post('ktg_pelanggan', TRUE),
        'email' => $this->input->post('email', TRUE),
        'password' => password_hash($this->input->post('pass1', TRUE), PASSWORD_DEFAULT, ['cost' => 10]),
        'jk' => $this->input->post('jk', TRUE),
        'telp' => $this->input->post('telp', TRUE),
        'alamat' => $this->input->post('alamat', TRUE),
        'promo' => $this->input->post('promo'),
        'status' => 1
        );

        if ($this->app->insert('t_users', $data))
        {
          echo '<script type="text/javascript">alert("Pendaftaran Berhasil");window.location.replace("'.base_url('home').'")</script>';
        } else {
          echo '<script type="text/javascript">alert("Username / Email tidak tersedia");</script>';
        }
      }
    }

    $data = array(
    'user' => $this->input->post('user', TRUE),
    'ktg_pelanggan' => $this->input->post('ktg_pelanggan', TRUE),
    'nama1' => $this->input->post('nama1', TRUE),
    'email' => $this->input->post('email', TRUE),
    'jk' => $this->input->post('jk', TRUE),
    'telp' => $this->input->post('telp', TRUE),
    'alamat' => $this->input->post('alamat', TRUE),
    'promo' => $this->input->post('promo', TRUE),
    );

    $this->load->view('user/register', $data);
  }

  public function login()
  {
    if ($this->input->post('submit') == 'Masuk')
    {
      $user = $this->input->post('email', TRUE);
      $pass = $this->input->post('password', TRUE);

      // echo $user;
      // echo $pass;



      $cek  = $this->app->get_where('t_users', "username = '$user' && status = 1 || email = '$user' && status = 1" );

      if ($cek->num_rows() > 0) {
        $data = $cek->row();

        if (password_verify($pass, $data->password))
        {

          $datuser = array(
          'user_id' => $data->id_user,
          'name' => $data->fullname,
          'email' => $data->email,
          'alamat' => $data->alamat,
          'telp' => $data->telp,
          's_pemesanan' => $data->s_pemesanan,
          'promo' => $data->promo,
          'user_login' => TRUE
          );
          $this->session->set_userdata($datuser);
          redirect('home');

        }
        else {
          echo '<script type="text/javascript">alert("Password ditolak");</script>';
        }

      } else {
        echo '<script type="text/javascript">alert("Username tidak dikenali");</script>';
      }

    }

    if ($this->session->userdata('user_login') == TRUE)
    {
      redirect('home');
    }

    $tampil['data'] = $this->app->get_limit();
    $this->template->user('user/home', $tampil);

  }

  public function list_sayur()
  {
    $data['data'] = $this->app->get_all('t_sayur');
    $this->load->view('user/sayur', $data);
  }

  public function s_daun()
  {
    $data['data'] = $this->app->get_where('t_sayur', ['status' => 1]);
    $this->load->view('user/sayur', $data);
  }

  public function s_buah()
  {
    $data['data'] = $this->app->get_where('t_sayur', ['status' => 2]);
    $this->load->view('user/sayur', $data);
  }

  public function umbi_umbian()
  {
    $data['data'] = $this->app->get_where('t_sayur', ['status' => 3]);
    $this->load->view('user/sayur', $data);
  }

  public function buah()
  {
    $data['data'] = $this->app->get_where('t_sayur', ['status' => 4]);
    $this->load->view('user/sayur', $data);
  }

  public function search(){
    $data['keyword'] = $this->input->post("keyword");

    $data['sayur']=$this->app->cari($data['keyword'])->result();
    $data['jumlah']=count($data['sayur']);
    $this->load->view('sayur',$data);
  }

  public function logout()
  {
    $this->session->sess_destroy();

    redirect('home');

  }


  public function add_email()
  {
    $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|is_unique[t_email.email]|min_length[11]|max_length[40]|valid_email');

    if ($this->form_validation->run() == FALSE ) {
      $this->load->view('user/index');
    } else {
      $this->load->library(['encryption']);
      $this->load->helper(['string']);


      $id_email = time();
      $email = $this->input->post('email', TRUE);
      $data = array (
      'id_email' => $id_email,
      'email' => $email
      );

      $this->app->insert('t_email', $data);

      $this->load->library('email');
      $config = array();
      $config['charset'] = 'utf-8';
      $config['useragent'] = 'Dapurtani';
      $config['protocol']= "smtp";
      $config['mailtype']= "html";
      $config['smtp_host']= "ssl://smtp.gmail.com";//pengaturan smtp
      $config['smtp_port']= "465";
      $config['smtp_timeout']= "400";
      $config['smtp_user']= "infodapurtani@gmail.com"; // isi dengan email kamu
      $config['smtp_pass']= "dapurtani.com2018"; // isi dengan password kamu
      $config['crlf']="\r\n";
      $config['newline']="\r\n";
      $config['wordwrap'] = TRUE;
      //memanggil library email dan set konfigurasi untuk pengiriman email

      $this->email->initialize($config);
      $id_email = time();
      $email = $this->input->post('email', TRUE);
      //konfigurasi pengiriman

      $this->email->from($config['smtp_user']);
      $this->email->to($email);
      $this->email->subject("Dapurtani");
      $this->email->message(
      'Terima kasih telah mengikuti kami. Temukan pengalaman baru berbelanja sayuran segar secara online dan tentunya dapat diantarkan ketempt kalian.
      <br><br>
      Best regards<br>
      Admin
      '
      );

      $this->email->send();
      echo '<script type="text/javascript">alert("Email Berhasil Terkirim");window.location.replace("'.base_url('home').'")</script>';

    }
    echo '<script type="text/javascript">alert("Email Sudah Ada");window.location.replace("'.base_url('home').'")</script>';
  }
}
