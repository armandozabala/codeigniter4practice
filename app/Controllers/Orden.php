<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;


//lamar modelo
use App\Models\OrdenModel;


class Orden extends ResourceController
{ 

 protected $orden;

	public function __construct(){

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE"); 
        $this->orden = new OrdenModel();

 }

 public function getOrdenesToday(){

   
    $ruta = $this->request->getPost('id_ruta');
    $date = date('Y-m-d', time());
    
    if($ruta != 0){
     return $this->respond($this->orden->getOrdenesToday($date, $ruta));
    }
    else{
     return $this->respond($this->orden->getOrdenesTodayAll($date));
    }

 }

 public function deleteOrden(){

   $id_orden = $this->request->getPost('id_orden');

   $res = $this->orden->where('id', $id_orden)->delete();

   return $this->respond(['message' => 'Borrado'], 200);

}


public function deleteAllOrden(){

	$obj=json_decode(file_get_contents('php://input'));
	$datos = $obj->row;


	for($row=0; $row < count($datos); ++$row){
		$this->orden->where('id', $datos[$row]->id);
		$this->orden->delete();
	}

	///return $this->respond(['message' =>  'update'.(object) $datos[0]], 200);
/*	$res = $this->cliente->updateBatch($datos, 'orden');
*/
	return $this->respond(['message' => 'Borrados'], 200);

}


 }