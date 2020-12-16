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

 }