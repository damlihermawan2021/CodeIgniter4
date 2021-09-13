<?php

namespace App\Controllers;

class Pages extends BaseController
{
	public function index()
	{
		$data = [
			'title' => 'Home | Webprograming damli',
			'tes'  => ['satu','dua','tiga']
		];
		
		return view('pages/home', $data);
		
	}

	public function about()
	{
		$data = [
			'title' => 'About | Webprograming damli'
		];
		
		return view('pages/about', $data);

	}
	
	public function contact()
	{
		$data = [
			'title' => 'Contact | Webprograming damli',
			'alamat' => [
			[
					'tipe' => 'Rumah',
					'alamat' => 'jl.vilanusaindah no.9',
					'kota' => 'bekasi'
				],
				[
					'tipe' => 'Kantor',
					'alamat' => 'jl dipatiukur no.2',
					'kota' => 'bandung'
				]
			]
		];
		
		return view('pages/contact',$data);
		
	}
}
