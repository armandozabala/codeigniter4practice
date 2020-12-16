<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model {

  protected $table = 'users';
  protected $primaryKey ='id';

  protected $returnType = 'array';
  protected $db;
  protected $allowedFields = [ 'ip_address', 'username', 'password','salt','email','created_on','last_login','active','nombres','apellidos','tipo_documento','cedula','fecha_nacimiento','telefono','direccion','id_departamento','id_ciudad', 'fecha_licencia','foto','id_tipous'];


  protected $useTimestamps = true;
  protected $createdField = 'created_on';
  protected $updatedField = 'updated_at';
  protected $deletedField = 'deleted_at';
}