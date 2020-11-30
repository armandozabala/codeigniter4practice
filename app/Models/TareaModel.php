<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaModel extends Model {

  protected $table = 'tareas';
  protected $primaryKey ='id';

  protected $returnType = 'array';
  protected $useSoftDeletes = true;

  protected $allowedFields = [ 'titulo', 'descripcion'];

}
