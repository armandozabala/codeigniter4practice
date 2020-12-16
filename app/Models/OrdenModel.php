<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdenModel extends Model {

  protected $table = 'ordenes';
  protected $primaryKey ='id';

  protected $returnType = 'array';


  protected $allowedFields = [ 'fecha_creacion', 'id_cliente'];


  public function getOrdenesDate($fecha, $id_cliente){

   $this->db = \Config\Database::connect();

   /*$query = $this->db->query('SELECT c.razon_social,
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
                                     o.fecha_creacion
                                     r.ruta FROM clientes c 
                                     LEFT JOIN rutas r ON c.id_ruta = r.id_ruta
                                     LEFT JOIN ordenes o ON c.id_cliente = o.id_cliente
                                     WHERE o.fecha_creacion = '.$fecha);*/

    $query = $this->db->query('SELECT o.id_cliente,
                                      c.razon_social
                                      FROM ordenes o
                                      LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
                                      WHERE DATE(o.fecha_creacion) = "'.$fecha.'" AND o.id_cliente = '.$id_cliente);

    $results = $query->getResult();

    return $results;

  }

  public function getOrdenesToday($fecha, $ruta){

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
                                        o.fecha_creacion,
                                        r.ruta 
                                        FROM clientes c 
                                        LEFT JOIN rutas r ON c.id_ruta = r.id_ruta
                                        LEFT JOIN ordenes o ON c.id_cliente = o.id_cliente
                                        WHERE DATE(o.fecha_creacion) = "'.$fecha.'" AND c.orden != 0  AND c.id_ruta != 0 AND r.id_ruta='.$ruta.'  ORDER BY c.orden ASC');
      $results = $query->getResult();

      return $results;

  }

  public function getOrdenesTodayAll($fecha){

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
                                         o.fecha_creacion,
                                         r.ruta 
                                         FROM clientes c 
                                         LEFT JOIN rutas r ON c.id_ruta = r.id_ruta
                                         LEFT JOIN ordenes o ON c.id_cliente = o.id_cliente
                                         WHERE DATE(o.fecha_creacion) = "'.$fecha.'" AND c.orden != 0 AND c.id_ruta != 0 ORDER BY c.orden ASC');
       $results = $query->getResult();
 
       return $results;
 
   }


}
