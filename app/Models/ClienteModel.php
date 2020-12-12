<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model {

  protected $table = 'clientes';
  protected $primaryKey ='id';

  protected $returnType = 'array';
  protected $db;
  protected $allowedFields = [ 'razon_social', 'nit','id_cliente','nombres','apellidos','cedula','direccion','telefono','email','latitud','longitud','hora_desde','hora_hasta','id_ciudad','id_departamento','orden','ruta'];



  public function getClientes(){

   $this->db = \Config\Database::connect();

      $query = $this->db->query('SELECT c.razon_social,
                                        c.nit,
                                        c.id_cliente,
                                        c.nombres,
                                        c.apellidos,
                                        c.cedula,
                                        c.direccion,
                                        c.telefono,
                                        c.email,
                                        c.latitud,
                                        c.longitud,
                                        c.hora_desde,
                                        c.hora_hasta,
                                        c.orden,
                                        c.ruta,
                                        c.id_ruta,
                                        c.id_ciudad,
                                        c.id_departamento,
                                        r.ruta FROM clientes c LEFT JOIN rutas r ON c.id_ruta = r.id_ruta');
      $results = $query->getResult();

      return $results;

  }


  public function getClientesRutas($id_ruta){

    $this->db = \Config\Database::connect();
 
       $query = $this->db->query('SELECT c.razon_social,
                                         c.nit,
                                         c.id_cliente,
                                         c.nombres,
                                         c.apellidos,
                                         c.cedula,
                                         c.direccion,
                                         c.telefono,
                                         c.email,
                                         c.latitud,
                                         c.longitud,
                                         c.hora_desde,
                                         c.hora_hasta,
                                         c.orden,
                                         c.ruta,
                                         c.id_ruta,
                                         c.id_ciudad,
                                         c.id_departamento,
                                         r.ruta FROM clientes c LEFT JOIN rutas r ON c.id_ruta = r.id_ruta
                                         WHERE c.id_ruta = '.$id_ruta);
       $results = $query->getResult();
 
       return $results;
 
   }
}
