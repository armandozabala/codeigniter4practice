<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//lamar modelo
use App\Models\ClienteModel;


class Excel extends ResourceController
{ 

 protected $excel;
 protected $model;

	public function __construct(){

    $this->model  = new ClienteModel();
    $this->excel = new Spreadsheet();

}


public function getCoord(){

     echo "Ok";

}

public function getCoordinatesFromApi($address) {

   $key='AIzaSyDw5Hm1y6CwPFFlDjT3aXXEdai9eTdFdXA';
   $address = urlencode($address);

   $url = $http.get('https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$key);

   $resp_json = file_get_contents($url);
   $resp = json_decode($resp_json, true);

   if ($resp['status'] == 'OK') {
    // get the important data
    $lati  = $resp['results'][0]['geometry']['location']['lat'];
    $longi = $resp['results'][0]['geometry']['location']['lng'];
    
    print_r($lati);
    //echo $longi;

  } else {
    return false;
  }

}


public function exportarExcel(){

  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $filename="nombrssse.xlsx";
  $writer = new Xlsx($spreadsheet);
  $sheet = $spreadsheet->getActiveSheet();
  //name the worksheet
  $sheet->setTitle('Informe');
  //set cell A1 content with some text
  $sheet->setCellValue('A1','Celda1');

  
     //save our workbook as this file name

     $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
  
   
     $writer->save(WRITEPATH.'/uploads/'.$filename);

     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
     header('Content-Disposition: attachment; filename='.$filename);
     ob_end_clean(); $writer->save("php://output"); exit();
     
     /*if(file_exists($filename)){
      echo json_encode(array('error'=>false, 'export_path'=> WRITEPATH.'/uploads/' . $filename)); //my angular project is at D:\wamp64\www\angular6-app\client\
     }*/

}


public function uploadExcel(){

 $archivo = $this->request->getFile('excelfile');

 $archivo->move(WRITEPATH.'/uploads/excel');
 
//$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile(WRITEPATH.'/uploads/excel/'.$archivo->getName());


$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');

$reader->setReadDataOnly(false);

$spreadsheet = $reader->load(WRITEPATH.'/uploads/excel/'.$archivo->getName());

$worksheet = $spreadsheet->getActiveSheet();

 

foreach ( $spreadsheet->getWorksheetIterator() as $worksheet) {
 $worksheetTitle     = $worksheet->getTitle();
 $highestRow         = $worksheet->getHighestRow(); // e.g. 10
 $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'

 $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);


 $ordenes = "";

 $nrColumns = ord($highestColumn) - 64;

 $arr_final = array();

 for ($row = 2; $row <= $highestRow; ++$row) {

    $arr = array();
  

     
    for ($col = 2; $col <= $highestColumnIndex; ++$col) {

           $cell = $worksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
           //$val = $cell->getCalculatedValue();
           array_push($arr, $cell);
   
         
           
    }

      $data_cell =[
                  'razon_social' => $arr[0],
                  'nit' => $arr[1],
                  'id_cliente' => $arr[2],
                  'nombres' => $arr[3],
                  'apellidos' => $arr[4],
                  'cedula' => $arr[5],
                  'direccion' => $arr[6],
                  'telefono' => $arr[7],
                  'email' => $arr[8],
                  'latitud' => $arr[9],
                  'longitud' => $arr[10],
                  'hora_desde' => $arr[11],
                  'hora_hasta' => $arr[12],
                  'id_ciudad' => $arr[13],
                  'id_departamento' => $arr[14],
                  'ruta' => $arr[15],
                  'orden' => $arr[16]
      ];

      array_push($arr_final, $data_cell);
 
     

  }


  $res = $this->model->insertBatch($arr_final);

  if( $res == count($arr_final)){
    return $this->respond(['data' => 'creado batch cliente '.count($arr_final)." clientes registrados "], 200);
  }else{
    return $this->respond(['message' => 'Error in batch invalido'], 401);
  }
  

}


 /*$this->excel->setActiveSheetIndex(0);
  //name the worksheet
  $this->excel->getActiveSheet()->setTitle('Informe');
  //set cell A1 content with some text
  $this->excel->getActiveSheet()->setCellValue('A1','Celda1');
  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
  $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(10);
  
  $filename="nombre.xls"; //save our workbook as this file name
  header('Content-Type: application/vnd.ms-excel'); //mime type
  header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
  header('Cache-Control: max-age=0'); //no cache*/

 }



}