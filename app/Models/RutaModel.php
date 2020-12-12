<?php

namespace App\Models;

use CodeIgniter\Model;

class RutaModel extends Model {

  protected $table = 'rutas';
  protected $primaryKey ='id_ruta';

  protected $returnType = 'array';


  protected $allowedFields = [ 'ruta', 'color', 'id_operacion'];


}
