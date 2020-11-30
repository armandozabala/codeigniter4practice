<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		return view('welcome_message');
	}



	//--------------------------------------------------------------------

	public function hello(){
				echo "Nuevo";
	}

	public function saludo(){

		 //para trabajar con peticiones
			$request = \Config\Services::request();

			$saludo = $request->getPost('saludos');

			echo 'Hola '.$saludo;

	}


}
