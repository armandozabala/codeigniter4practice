<?php 

namespace App\Controllers;

//lamar modelo
use App\Models\TareaModel;
use App\Models\ClienteModel;



class Home extends Auth
{


	protected $model;
	protected $cliente;


	public function __construct(){
					$this->model  = new TareaModel();
					$this->cliente  = new ClienteModel();
					
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

			return $this->respond(['data' => 'creado'], 200);


			

		}

		public function getClientes(){

			
						return $this->respond($this->cliente->findAll());


		}


		public function getTareasEliminadas(){


			$token =  $this->request->getHeader('Authorization') != null ?  $this->request->getHeader('Authorization')->getValue() : "";

			if($this->validateToken($token) == true){

						return $this->respond($this->model->onlyDeleted()->findAll());

			}
			else{

						return $this->respond(['message' => 'Token invalido'], 401);
			}

	}


		public function eliminar(){

			$token =  $this->request->getHeader('Authorization') != null ?  $this->request->getHeader('Authorization')->getValue() : "";

			if($this->validateToken($token) == true){

					$id = $this->request->getPost('id');

					$res = $this->model->delete($id);

					//echo json_encode(["msg" => "borrado ".$res]);

					return $this->respond(['message' => 'Borrado'], 200);

			}else{
			
			  	return $this->respond(['message' => 'Token invalido'], 401);
				   
			}

		}

		public function editar(){


			$token =  $this->request->getHeader('Authorization') != null ?  $this->request->getHeader('Authorization')->getValue() : "";

			if($this->validateToken($token) == true){

					$id = $this->request->getPost('id');

					$data = [
						'titulo' => $this->request->getPost('titulo'),
						'descripcion' =>  $this->request->getPost('descripcion')
					];

					$res = $this->model->update($id, $data);

					//echo json_encode(["msg" => "Editado ".$res]);

					return $this->respond(['message' => 'Editado'], 200);
				}
				else{

			  		return $this->respond(['message' => 'Token invalido'], 401);
				}

		}


		public function buscar(){

			
			$token =  $this->request->getHeader('Authorization') != null ?  $this->request->getHeader('Authorization')->getValue() : "";

			if($this->validateToken($token) == true){


							$id = $this->request->getPost('id');

							//echo json_encode($this->model->find($id));

							return $this->respond(['data' => $this->model->find($id)], 200);

			}else{

						return $this->respond(['message' => 'Token invalido'], 401);
			}
		}


		public function tareasTitulo(){
					$db = \Config\Database::connect();
					$builder = $db->table('tareas');
					$builder->select("titulo");
					$query = $builder->get();

					echo json_encode($query->getResult());

		}


		public function subida()
		{

								$img = $this->request->getFile('filesito');
								
								
								$img->move(WRITEPATH.'/uploads');


									return $this->respond(['message' =>  ['name'=> $img->getName(), 'size' => $img->getSize(), 'ext'=> $img->getExtension() ]], 200);
										/*$validacion = $this->validate([
											   'tienda_logo' => [
																				'uploaded[tienda_logo]',
																				'mime_in[tienda_logo,image/png]',
																				'max_size[tienda_log,4096]'
														]
										]);

										if($validacion){
											$img = $this->request->getFile('userfile');
											$img->move('./uploads','name.png');
												return $this->respond(['message' => $img], 200);
										}
										else{
													return $this->respond(['message' => 'error img invalido'], 401);
										}*/


		}





}
