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

    

     $resp = $this->cliente->where('id_cliente',$arr[0])->findAll();

    
     if(count($resp) ==  0){

              //$resp_coord = $this->geocodeAddress($arr[6]);

              $var =  $arr[17];
              
              $date = str_replace('/', '-', $var);

              $date_end = date('Y-m-d', strtotime($date));

              $this->saveRuta($arr[5]);

              $id_ruta = $this->getRuta($arr[5]);

              $data_cell =[
                'id_cliente' => $arr[0],
                'nit' => $arr[1],
                'cedula' => $arr[2],
                'razon_social' => $arr[3],
                'establecimiento' => $arr[4],
                'ruta' => $arr[5],
                'direccion' => $arr[6],
                'direccion_estandar' => $arr[7],
                'barrio' => $arr[9],
                'localidad' => $arr[11],
                'ciudad' => $arr[13],
                'departamento' => $arr[14],
                'latitud' =>  $arr[15],
                'longitud' => $arr[16],
                'fecha_ultima_compra' => $date_end,
                'id_ruta' => $id_ruta
               ];

           array_push($arr_final, $data_cell);



     }else{

         $datadd = [
            'id_cliente' => $arr[0]
         ];

         $date = date('Y-m-d', time());

         $checkDate =  $this->orden->getOrdenesDate($date, $arr[0]);

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