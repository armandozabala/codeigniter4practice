<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment as alignment;
use PhpOffice\PhpSpreadsheet\Style\Border as border;
use PhpOffice\PhpSpreadsheet\Style\Fill as fill; // Instead PHPExcel_Style_Fill

//lamar modelo
use App\Models\ClienteModel;
use App\Models\RutaModel;
use App\Models\OrdenModel;


class Excel extends ResourceController
{ 

 protected $excel;
 protected $cliente;
 protected $ruta;
 protected $orden;

	public function __construct(){

    header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");  
    $this->cliente  = new ClienteModel();
    $this->ruta = new RutaModel();
    $this->orden = new OrdenModel();
    $this->excel = new Spreadsheet();

}




/** $address */
public function geocodeAddress($address) {

   $api_key='AIzaSyDw5Hm1y6CwPFFlDjT3aXXEdai9eTdFdXA';
   $address = urlencode($address.", Colombia");//urlencode($address);

   $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=".$api_key."");

   $obj = json_decode($json);


   if ($obj->status == "OK") {


     return $coord = [
           "lat" => $obj->results[0]->geometry->location->lat,
           "lng" => $obj->results[0]->geometry->location->lng
      ];


   }else{

      return $coord = [
             "lat" => 0,
             "lng" => 0
      ];
   }


}


public function exportarExcel(){

  $obj=json_decode(file_get_contents('php://input'));
  $datos = $obj->row;

  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $filename="nombre.xlsx";
  $writer = new Xlsx($spreadsheet);
  $sheet = $spreadsheet->setActiveSheetIndex(0);

  //set cell A1 content with some text

      $sheet->setCellValue('A1', 'Id');
      $sheet->setCellValue('B1', 'Razon Social');
      $sheet->setCellValue('C1', 'Nombres');
      $sheet->setCellValue('D1', 'Apellidos');
      $sheet->setCellValue('E1', 'Email');
      $sheet->setCellValue('F1', 'Telefono');
      $sheet->setCellValue('G1', 'Direccion');
      $sheet->setCellValue('H1', 'Ruta');
      $sheet->setCellValue('I1', 'Orden');

      //loop
      for($row=2; $row < count($datos) + 2; ++$row){

          $sheet->setCellValue('A'.$row, $datos[$row-2]->id_cliente);

          $sheet->setCellValue('B'.$row, $datos[$row-2]->razon_social);
  
          $sheet->setCellValue('C'.$row, $datos[$row-2]->nombres);
  
          $sheet->setCellValue('D'.$row, $datos[$row-2]->apellidos);
  
          $sheet->setCellValue('E'.$row, $datos[$row-2]->email);
  
          $sheet->setCellValue('F'.$row, $datos[$row-2]->telefono);

          $sheet->setCellValue('G'.$row, $datos[$row-2]->direccion);

          $sheet->setCellValue('H'.$row, $datos[$row-2]->ruta);

          $sheet->setCellValue('I'.$row, $datos[$row-2]->orden);

      }


      $sheet->getColumnDimension('A')->setWidth(10);
      $sheet->getColumnDimension('B')->setWidth(25);
      $sheet->getColumnDimension('C')->setWidth(15);
      $sheet->getColumnDimension('D')->setWidth(15);
      $sheet->getColumnDimension('E')->setWidth(23);
      $sheet->getColumnDimension('F')->setWidth(15);
      $sheet->getColumnDimension('G')->setWidth(30);
      $sheet->getColumnDimension('H')->setWidth(20);
      $sheet->getColumnDimension('I')->setWidth(20);

      //name the worksheet
      $sheet->setTitle('Informe Ordenes');

      $sheet->getStyle('A1:I1')->applyFromArray(

        array(

            'font'    => array(

                'bold'      => true,
                //'color' => array('argb' => 'FFFF0000'),


            ),

            'alignment' => array(

                'horizontal' => alignment::HORIZONTAL_CENTER,

            ),
            
            'borders' => array(

              'top'     => array(

                'borderStyle' => Border::BORDER_THICK,
                'color' => array('argb' => 'FFFF0000'),

              ),

              'bottom'     => array(

                  'borderStyle' => border::BORDER_DASHDOT

              )

          ),
          'fill' => array(
            'fillType' => Fill::FILL_SOLID,
            'startColor' => array('argb' => 'FF4F81BD')
          )


        )

        );

     //save our workbook as this file name
     $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
  
     $writer->save(WRITEPATH.'/uploads/'.$filename);

     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
     header('Content-Disposition: attachment; filename='.$filename);
     ob_end_clean();
     $writer->save("php://output"); 
     exit();
    


}


public function uploadExcel(){

 $archivo = $this->request->getFile('excelfile');

 $archivo->move(WRITEPATH.'/uploads/excel');
 
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
 $arr_agregados = array();

 for ($row = 2; $row <= $highestRow; ++$row) {

    $arr = array();
  

     
    for ($col = 2; $col <= $highestColumnIndex; ++$col) {

           $cell = $worksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();

           array_push($arr, $cell);
   
         
           
    }

    

     $resp = $this->cliente->where('id_cliente',$arr[2])->findAll();

    
     if(count($resp) ==  0){

              $resp_coord = $this->geocodeAddress($arr[6]);

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
                'latitud' => $resp_coord['lat'], //$arr[9],
                'longitud' => $resp_coord['lng'], // $arr[10],
                'hora_desde' => $arr[11],
                'hora_hasta' => $arr[12],
                'id_ciudad' => $arr[13],
                'id_departamento' => $arr[14],
                'ruta' => $arr[15]
               ];

            array_push($arr_final, $data_cell);

     }else{

         $datadd = [
            'id_cliente' => $arr[2]
         ];

         $date = date('Y-m-d', time());

         $checkDate =  $this->orden->getOrdenesDate($date, $arr[2]);

         if(count($checkDate)){
            //echo "si existe ".$date."\n";
         }else{
            //echo "no existe ";
            $this->orden->save($datadd);
         }

         array_push($arr_agregados, $checkDate);

        
        

         
     }
   


  }

  $res = 0;

  if(count($arr_final) > 0)
      $res = $this->cliente->insertBatch($arr_final);

  if( $res == count($arr_final)){
    return $this->respond(['nuevos' => (object) $arr_final, 'registros' => count($arr_agregados)], 200);
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

 public function getOrdenesToday(){


   $this->ordenes->getOrdenesToday();

 }


}