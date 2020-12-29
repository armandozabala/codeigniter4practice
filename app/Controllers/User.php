<?php 

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;

use App\Models\UsuarioModel;
use App\Models\ClienteModel;


class User extends ResourceController
{
	protected $format = 'json';
	protected $usuario;
	protected $cliente;

	public function __construct(){
			
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");  
			$this->usuario = new UsuarioModel();
			$this->cliente  = new ClienteModel();
	
	}

	public function updateOrden(){

				
				$id_cliente = $this->request->getPost('id_cliente');
				$orden = $this->request->getPost('orden');

				$this->cliente->set('orden', $orden);
				$this->cliente->where('id_cliente', $id_cliente);
				$this->cliente->update();

				return $this->respond(['users' => 'update'], 200);
	}


		public function updateRuta(){

					
			$id_cliente = $this->request->getPost('id_cliente');
			$ruta = $this->request->getPost('id_ruta');

			$this->cliente->set('id_ruta', $ruta);
			$this->cliente->where('id_cliente', $id_cliente);
			$this->cliente->update();

			return $this->respond(['users' => 'update'], 200);
	}


	public function login(){

					 $email = $this->request->getPost('email');
						$password = $this->request->getPost('password');


						$datosUsuario = $this->usuario->where('email',$email)->first();

						if(	$datosUsuario != null){

											if(password_verify($password, $datosUsuario['password'])){

													 $datosSesion = [
																		'id_usuario' => $datosUsuario['id'],
																		'nombres' => $datosUsuario['nombres'],
																		'apellidos' => $datosUsuario['apellidos'],
																		'email' => $datosUsuario['email']
														];

														
														return $this->respond(['users' => (object) $datosSesion], 200);

									
											}
											else{
												return $this->respond(['message' => 'Error el usuario o password invalido'], 401);
											}
						}
						else{

						
						    	return $this->respond(['message' => 'Error el usuario no existe'], 401);
						}
	}

	public function insertar(){

					$password = $this->request->getPost('password');

					$img = $this->request->getFile('photo');
								
					$img->move(WRITEPATH.'/users');

					$hash = password_hash($password, PASSWORD_DEFAULT);
					
					$res = $this->usuario->save([
						   'ip_address' => '::1',
									'email' => $this->request->getPost('email'),
									'password' => $hash,
									'nombres' => $this->request->getPost('nombres'),
									'apellidos' => $this->request->getPost('apellidos'),
									'cedula' => $this->request->getPost('cedula'),
									'telefono' =>  $this->request->getPost('telefono'),
									'direccion' => $this->request->getPost('direccion'),
									'foto' => $img->getName()
					]);

					if($res){

				   		return $this->respond(['message' => 'Guardado'], 200);
					}
					else{

							  return $this->respond(['message' => 'Error'], 401);
					
					}

	}

	public function getClientes(){

 		 return $this->respond($this->cliente->getClientes());
  
	}

	public function getClientesNoOrden(){

		$id_ruta = $this->request->getPost('id_ruta');

	
		if($id_ruta == 0){

			return $this->respond($this->cliente->getClientesNoOrdenAll());

		}else if($id_ruta != 0){

			return $this->respond($this->cliente->getClientesNoOrden($id_ruta));


		}

}

public function getClientesNoRuta(){


	return $this->respond($this->cliente->getClientesNoRuta());

}

	
	public function getClientesRutas(){

		$id_ruta = $this->request->getPost('id_ruta');

		if($id_ruta == 0){

			/* $resp = [
							"rows" => $this->cliente->getClientesRutasAll(),
							"total" => $this->cliente->countAll(),
				];*/

			return $this->respond($this->cliente->getClientesRutasAll());

		}else if($id_ruta != 0){

			return $this->respond($this->cliente->getClientesRutas($id_ruta));

		}

}


public function deleteCustomer(){

			$id_cliente = $this->request->getPost('id_cliente');

			$res = $this->cliente->where('id_cliente', $id_cliente)->delete();

			return $this->respond(['message' => 'Borrado'], 200);

}


public function updateCustomers(){

	$obj=json_decode(file_get_contents('php://input'));
	$datos = $obj->row;


	for($row=0; $row < count($datos); ++$row){
		$this->cliente->where('id_cliente', $datos[$row]->id_cliente);
		$this->cliente->delete();
		$this->cliente->replace((array)$datos[$row]);
	}

	///return $this->respond(['message' =>  'update'.(object) $datos[0]], 200);
/*	$res = $this->cliente->updateBatch($datos, 'orden');
*/
	return $this->respond(['message' => 'Actualizado'], 200);

}





}