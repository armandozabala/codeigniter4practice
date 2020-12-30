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
      $sheet->setCellValue('C1', 'Establecimiento');
      $sheet->setCellValue('D1', 'Direccion');
      $sheet->setCellValue('E1', 'Ruta');
      $sheet->setCellValue('F1', 'Orden');
      $sheet->setCellValue('G1', 'Fecha');
      /*$sheet->setCellValue('H1', 'Ruta');
      $sheet->setCellValue('I1', 'Orden');*/

      //loop
      for($row=2; $row < count($datos) + 2; ++$row){

          $sheet->setCellValue('A'.$row, $datos[$row-2]->id_cliente);

          $sheet->setCellValue('B'.$row, $datos[$row-2]->razon_social);
  
          $sheet->setCellValue('C'.$row, $datos[$row-2]->establecimiento);

          $sheet->setCellValue('D'.$row, $datos[$row-2]->direccion);

          $sheet->setCellValue('E'.$row, $datos[$row-2]->ruta);

          $sheet->setCellValue('F'.$row, $datos[$row-2]->orden);
          
          $sheet->setCellValue('G'.$row, $datos[$row-2]->fecha_creacion);

      }


      $sheet->getColumnDimension('A')->setWidth(10);
      $sheet->getColumnDimension('B')->setWidth(40);
      $sheet->getColumnDimension('C')->setWidth(40);
      $sheet->getColumnDimension('D')->setWidth(30);
      $sheet->getColumnDimension('E')->setWidth(23);
      $sheet->getColumnDimension('F')->setWidth(15);
      $sheet->getColumnDimension('G')->setWidth(30);
      /*$sheet->getColumnDimension('H')->setWidth(20);
      $sheet->getColumnDimension('I')->setWidth(20);*/

      //name the worksheet
      $sheet->setTitle('Informe Ordenes');

      $sheet->getStyle('A1:G1')->applyFromArray(

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


public function uploadExcelCliente(){


  $archivo = $this->request->getFile('excelfile');

  $archivo->move(WRITEPATH.'/uploads/excel');
  
 
 $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(WRITEPATH.'/uploads/excel/'.$archivo->getName()); // $reader->load(WRITEPATH.'/uploads/excel/'.$archivo->getName());
 
 $worksheet = $spreadsheet->getActiveSheet();
 
 

 //foreach ( $spreadsheet->getWorksheetIterator() as $worksheet) {
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
   
 
      
     for ($col = 1; $col <= $highestColumnIndex; ++$col) {
 
            $cell = $worksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
 
            array_push($arr, $cell);
    
          
            
     }
 


    
     print_r($arr);
     //return $this->respond(['message' => 'Error in batch invalido'.$arr], 401);
    

     //$resp = $this->cliente->where('id_cliente',$arr[1])->findAll();




    /*  if(count($resp) ==  0){
 
               //$resp_coord = $this->geocodeAddress($arr[6]);
 
               //$var =  $arr[18];
               
               //$date = str_replace('/', '-', $var);
 
               //$date_end = date('Y-m-d', strtotime($date));
 
               $this->saveRuta($arr[6]);
 
               $id_ruta = $this->getRuta($arr[6]);
 
               $data_cell =[
                 'id_cliente' => $arr[1],
                 'nit' => $arr[2], 
                 'cedula' => $arr[3], 
                 'razon_social' => $arr[4],
                 'establecimiento' => $arr[5],
                 'ruta' => $arr[6],
                 'direccion' => $arr[7],
                 'direccion_estandar' =>  $arr[7],
                 'barrio' => $arr[10],
                 'localidad' => '',
                 'orden' => 0,
                 'ciudad' => $arr[14],
                 'departamento' =>  $arr[15],
                 'latitud' =>  $arr[16],
                 'longitud' => $arr[17],
                 'id_ruta' => $id_ruta
                ];
 
               array_push($arr_final, $data_cell);
                 
 
    
              
 
      }else{

        $datadd = [
           'id_cliente' => $arr[1]
        ];

        $date = date('Y-m-d', time());

        $checkDate =  $this->orden->getOrdenesDate($date, $arr[1]);

        if(count($checkDate)){
           //echo "si existe ".$date."\n";
        }else{

           array_push($arr_agregados, $datadd);
        }

       
    
        
    }*/
  

   
 
 
   }
 
 


   /*$res = 0;
 
   if(count($arr_final) > 0)
       $res = $this->cliente->insertBatch($arr_final);

    if(count($arr_agregados) > 0)
      $res = $this->orden->insertBatch($arr_agregados);

    if( $res == count($arr_final) || $res == count($arr_agregados)){
    return $this->respond(['nuevos' =>  count($arr_final), 'clientes' => $arr_final, 'ordenes' => count($arr_agregados)], 200);
    }else{
    return $this->respond(['message' => 'Error in batch invalido'], 401);
    }*/
   


}


public function uploadExcel(){

 $archivo = $this->request->getFile('excelfile');

 $archivo->move(WRITEPATH.'/uploads/excel');
 
/*$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');

$reader->setReadDataOnly(false);*/

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(WRITEPATH.'/uploads/excel/'.$archivo->getName()); // $reader->load(WRITEPATH.'/uploads/excel/'.$archivo->getName());

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
  

     
    for ($col = 1; $col <= $highestColumnIndex; ++$col) {

           $cell = $worksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();

           array_push($arr, $cell);
   
         
           
    }

    

     $resp = $this->cliente->where('id_cliente',$arr[16])->findAll();


   
     if(count($resp) ==  0){

              //$resp_coord = $this->geocodeAddress($arr[6]);

              $var =  $arr[13];
              
              $date = str_replace('/', '-', $var);

              $date_end = date('Y-m-d', strtotime($date));

              $this->saveRuta($arr[14]);

              $id_ruta = $this->getRuta($arr[14]);

              $data_cell =[
                'id_cliente' => $arr[16],
                'nit' => 0, //$arr[1],
                'cedula' => 0, //$arr[2],
                'razon_social' => $arr[0],
                'establecimiento' => $arr[10],
                'ruta' => $arr[14],
                'direccion' => $arr[1],
                'direccion_estandar' => $arr[1],
                'barrio' => '',
                'telefono' => $arr[11],
                'localidad' => '',
                'hora_desde' => $arr[3],
                'hora_hasta' => $arr[4],
                'ciudad' => '', //$arr[13],
                'departamento' => '', // $arr[14],
                'latitud' =>  $arr[7],
                'longitud' => $arr[8],
                'fecha_ultima_compra' => $date_end,
                'id_ruta' => $id_ruta
               ];

              array_push($arr_final, $data_cell);
                


     }else{

         $datadd = [
            'id_cliente' => $arr[16]
         ];

         $date = date('Y-m-d', time());

         $checkDate =  $this->orden->getOrdenesDate($date, $arr[16]);

         if(count($checkDate)){
            //echo "si existe ".$date."\n";
         }else{
 
            array_push($arr_agregados, $datadd);
         }

        
      
        
        

         
     }
   


  }


  $res = 0;

  if(count($arr_final) > 0)
      $res = $this->cliente->insertBatch($arr_final);

  if(count($arr_agregados) > 0)
      $res = $this->orden->insertBatch($arr_agregados);

  if( $res == count($arr_final) || $res == count($arr_agregados)){
    return $this->respond(['nuevos' =>  count($arr_final), 'clientes' => $arr_final, 'ordenes' => count($arr_agregados)], 200);
  }else{
    return $this->respond(['message' => 'Error in batch invalido'], 401);
  }
  

 }


 }

 public function getOrdenesToday(){


   $this->ordenes->getOrdenesToday();

 }

 public function getRuta($ruta){

      $resp = $this->ruta->where('ruta', $ruta)->find();

      return $resp[0]['id_ruta'];

 }

 public function saveRuta($ruta){


      $resp = $this->ruta->where('ruta', $ruta)->findAll();

      if(count($resp) ==  0){

          $data_ruta =[
                'ruta' => $ruta
          ];

          $this->ruta->save($data_ruta);

      }

 }

}