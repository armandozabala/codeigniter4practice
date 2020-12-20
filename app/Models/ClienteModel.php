<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model {

  protected $table = 'clientes';
  protected $primaryKey ='id';

  protected $returnType = 'array';
  protected $db;
  protected $allowedFields = [ 'razon_social', 'establecimiento', 'nit','id_cliente', 'departamento', 'ciudad', 'nombres','apellidos','cedula','direccion', 'direccion_estandar', 'estrato', 'barrio', 'localidad', 'telefono','email','latitud','longitud','hora_desde','hora_hasta','orden','ruta','id_ruta','fecha_ultima_compra'];



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
                                        c.ciudad,
                                        c.departamento,
                                        r.ruta 
                                        FROM clientes c 
                                        LEFT JOIN rutas r ON c.id_ruta = r.id_ruta');
      $results = $query->getResult();

      return $results;

  }


  public function getClientesRutasAll(){

   $this->db = \Config\Database::connect();
 
   $query = $this->db->query('SELECT c.razon_social,
                                     c.establecimiento,
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
                                     c.ciudad,
                                     c.departamento,
                                     r.ruta FROM clientes c LEFT JOIN rutas r ON c.id_ruta = r.id_ruta 
                                     ORDER BY c.orden, c.id_cliente ASC'); //WHERE c.orden != 0 AND c.id_ruta != 0
   $results = $query->getResult();

   return $results;
  }


  public function getClientesRutas($id_ruta){

    $this->db = \Config\Database::connect();
 
       $query = $this->db->query('SELECT c.razon_social,
                                         c.establecimiento,
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
                                         c.ciudad,
                                         c.departamento,
                                         r.ruta 
                                         FROM clientes c 
                                         LEFT JOIN rutas r ON c.id_ruta = r.id_ruta
                                         WHERE c.id_ruta = '.$id_ruta.'  ORDER BY c.orden, c.id_cliente ASC');
       $results = $query->getResult();
 
       return $results;
 
   }


   public function getClientesNoRutas(){

      $this->db = \Config\Database::connect();
   
         $query = $this->db->query('SELECT c.razon_social,
                                           c.establecimiento,
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
                                           c.ciudad,
                                           c.departamento,
                                           r.ruta FROM clientes c 
                                           LEFT JOIN rutas r ON c.id_ruta = r.id_ruta
                                           WHERE c.id_ruta = 0 OR c.orden=0');
         $results = $query->getResult();
   
         return $results;
   
     }
}
