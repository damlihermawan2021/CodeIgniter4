<?php

namespace App\Controllers;

use App\Models\KomikModel;
use CodeIgniter\Database\Config;
use Config\Validation;

class Komik extends BaseController
{
    protected $KomikModel;
    public function __construct()
    {
        $this->KomikModel = new KomikModel(); 
    }
    public function index()
    {
         $data = [
        
        'title' => 'daftar komik',
        'komik' => $this->KomikModel->getKomik()
    ];
        
       
    
        return view('komik/index', $data);
    }
    public function detail($slug)
    {   
        
        $data = [     
        'title' => 'Detail Komik',
        'komik' => $this->KomikModel->getKomik($slug)
        ];
        // jika komik tidak ada ditable
        if(empty($data['komik'])){
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul komik ' . $slug . ' Tidak Ditemukan.');
            }
        return view('komik/detail', $data);
    }

    public function create()
    {
        session();
        $data = [
            'title' => 'Form Input Data Komik',
            'validation' => \Config\Services::validation()
        ];
        return view('komik/create',$data);

    }
    public function save()
    {

        //validasi form input
        if(!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => '{field} Judul Harus Di isi!',
                    'is_unique' => '{field} Judul sudah terdaftar'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'yang anda pilih bukan gambar',
                    'mime_in' => 'yang anda pilih bukan gambar' 
                ]
            ]
        ])){
            //$validation = \Config\Services::validation();        
            //return redirect()->to('/komik/create')->withInput()->with('validation',$validation);
            return redirect()->to('/komik/create')->withInput();
        }
    
    //ambil gambar
    $filesampul = $this->request->getFile('sampul');
    //pindahkan ke file img
    if($filesampul->getError() == 4) {
        $namaSampul = 'default.jpeg';
    }else{
    //nama randomfile img
    $namaSampul = $filesampul->getRandomName();
    $filesampul->move('img', $namaSampul);
    //ambilnamafile
    // $namaSampul = $filesampul->getName();
}


       $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->KomikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);   
        session()->setFlashdata('pesan' , 'data berhasil ditambahkan');
        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        $this->KomikModel->delete($id);
        session()->setFlashdata('pesan' , 'data berhasil dihapus');
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        session();
        $data = [
            'title' => 'Form Edit Data Komik',
            'validation' => \Config\Services::validation(),
            'komik'=> $this->KomikModel->getKomik($slug)
        ];
        return view('komik/edit',$data);

    }

    public function update($id)
    {

        //cek judul
            $komikLama = $this->KomikModel->getKomik($this->request->getVar('slug'));
                if($komikLama['judul'] == $this->request->getVar('judul')){
                    $rule_judul = 'required';
                }else{
                    $rule_judul = 'required|is_unique[komik.judul]';
                }                

        //validasi form update
        if(!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} Judul Harus Di isi!',
                    'is_unique' => '{field} Judul sudah terdaftar'
                ]
                ]
        ])){
            $validation = \Config\Services::validation();        
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput()->with('validation',$validation);
                 
        }

        
        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->KomikModel->save([
            'id'=> $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);   
        session()->setFlashdata('pesan' , 'data berhasil diubah');
        return redirect()->to('/komik');

    }
}