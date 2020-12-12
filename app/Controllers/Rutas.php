<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;


//lamar modelo
use App\Models\ClienteModel;
use App\Models\RutaModel;


class Rutas extends ResourceController
{ 

 protected $excel;
 protected $ruta;

	public function __construct(){

        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE"); 
         $this->ruta = new RutaModel();

    }

 public function getRutas(){
    
    return $this->respond($this->ruta->findAll());

 }

 }