<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;


use App\Models\UsuarioModel;

class Auth extends ResourceController
{

	protected $format = 'json';
	protected $usuario;


	public function __construct(){
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		 $this->usuario = new UsuarioModel();
	
	}




	public function create()
	{
		/**
		 * JWT claim types
		 * https://auth0.com/docs/tokens/concepts/jwt-claims#reserved-claims
		 */
		$email = $this->request->getPost('email');
		$password = $this->request->getPost('password');

		
		$datosUsuario = $this->usuario->where('email',$email)->first();

		if(	$datosUsuario != null){


												// add code to fetch through db and check they are valid
									// sending no email and password also works here because both are empty
									if(password_verify($password, $datosUsuario['password'])){

										$time = time();
										$key = Services::getSecretKey();
										$payload = [
														'iat' => $time,
														'exp' => $time + 60*10, //segundos
														'data' => [
																				'email' => $datosUsuario['email'],
																				'nombres' =>  $datosUsuario['nombres'],
																				'apellidos' => $datosUsuario['apellidos'],
																				'id_usuario' => $datosUsuario['id']
														]
										];

										/**'aud' => 'http://example.com',
											'iat' => 1356999524,
											'nbf' => 1357000000, */
										/**
											* IMPORTANT:
											* You must specify supported algorithms for your application. See
											* https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
											* for a list of spec-compliant algorithms.
											*/
										$jwt = JWT::encode($payload, $key);
										return $this->respond(['token' => $jwt], 200);
									

										
										//return $this->respond(['users' => (object) $datosSesion], 200);

					
							}
							else{
								return $this->respond(['message' => 'Error el usuario o password invalido'], 401);
							}
		}
		else{

							return $this->respond(['message' => 'Error el usuario no existe'], 401);
		}


		return $this->respond(['message' => 'Invalid login details'], 401);
	}


	protected function validateToken($token){
			  try{

								$key = Services::getSecretKey();
								return JWT::decode($token, $key, array('HS256'));

					}catch(\Exception $e){
						  return false;
					}
	}

	public function verifyToken(){
				$key = Services::getSecretKey();
				$token = $this->request->getPost("token");

				if($this->validateToken($token) == false){
					   return $this->respond(['message' => 'Token invalido'], 401);
				}
				else{
							$data = JWT::decode($token, $key, array('HS256'));
							return $this->respond(['data' => $data], 200);
				}
	}
}