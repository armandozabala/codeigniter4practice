<?php 

namespace App\Controllers;

//lamar modelo
use App\Models\TareaModel;


class Home extends BaseController
{

	protected $request;
	protected $model;

	public function __construct(){

					$this->request = \Config\Services::request();
					$this->model  = new TareaModel();
	}

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

			$saludo = $this->request->getPost('saludos');

			echo 'Hola '.$saludo;

	}

	public function sumar(){

				 //para trabajar con peticiones

					//from body
				/*	$obj = $this->request->getBody();

					$ok = json_decode($obj);

					echo $ok;*/
					
					$n1 = $this->request->getPost('n1');
					$n2 = $this->request->getPost('n2');
					
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

			

					$data = [
							'titulo' => $this->request->getPost('titulo'),
							'descripcion' =>  $this->request->getPost('descripcion')
					];

					$this->model->insert($data);

					echo json_encode(["msg" => "creado"]);

		}

		public function getTareas(){


				echo json_encode($this->model->findAll());

		}


		public function getTareasEliminadas(){


			echo json_encode($this->model->onlyDeleted()->findAll());

	}


		public function eliminar(){


					$id = $this->request->getPost('id');

					$res = $this->model->delete($id);

					echo json_encode(["msg" => "borrado ".$res]);

		}

		public function editar(){


					$id = $this->request->getPost('id');

					$data = [
						'titulo' => $this->request->getPost('titulo'),
						'descripcion' =>  $this->request->getPost('descripcion')
					];

					$res = $this->model->update($id, $data);

					echo json_encode(["msg" => "Editado ".$res]);


		}


		public function buscar(){


			$id = $this->request->getPost('id');

			echo json_encode($this->model->find($id));


		}


		public function tareasTitulo(){
					$db = \Config\Database::connect();
					$builder = $db->table('tareas');
					$builder->select("titulo");
					$query = $builder->get();

					echo json_encode($query->getResult());

		}



}
