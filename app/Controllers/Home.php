<?php 

namespace App\Controllers;

//lamar modelo
use App\Models\TareaModel;


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

	public function sumar(){

				 //para trabajar con peticiones
					$request = \Config\Services::request();

					//from body
				/*	$obj = $request->getBody();

					$ok = json_decode($obj);

					echo $ok;*/
					
					$n1 = $request->getPost('n1');
					$n2 = $request->getPost('n2');
					
					if(is_numeric($n1) && is_numeric($n2)){
									$suma = intval($n1) + intval($n2);
									$arr = ['msg' => $suma];
					}else{
						   $arr = ['msg' => 'have a problem with number'];
					}

					echo json_encode($arr);

		}


		//INSERT
		public function insertar(){

					$model  = new TareaModel();

					$request = \Config\Services::request();

					$data = [
							'titulo' => $request->getPost('titulo'),
							'descripcion' =>  $request->getPost('descripcion')
					];

					$model->insert($data);

					echo json_encode(["msg" => "creado"]);

		}

		public function getTareas(){

				$model  = new TareaModel();

				echo json_encode($model->findAll());

		}


		public function getTareasEliminadas(){

			$model  = new TareaModel();

			echo json_encode($model->onlyDeleted()->findAll());

	}


		public function eliminar(){

					$model  = new TareaModel();

					$request = \Config\Services::request();

					$id = $request->getPost('id');

					$res = $model->delete($id);

					echo json_encode(["msg" => "borrado ".$res]);

		}



}
