<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model {

  protected $table = 'cliente';
  protected $primaryKey ='id';

  protected $returnType = 'array';
  protected $useSoftDeletes = true;

  protected $allowedFields = [ 'razon_social', 'nit','id_cliente','nombres','apellidos','cedula','telefono','email','latitud','longitud','hora_desde','hora_hasta','id_ciudad','id_departamento','orden','ruta'];

}
