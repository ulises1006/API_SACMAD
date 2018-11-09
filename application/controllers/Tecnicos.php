<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Tecnicos extends REST_Controller {


  public function __construct(){

    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");

    parent::__construct();
    $this->load->database();

  }

  // Insertar tecnico

  public function registrar_post(){
    $data = $this->post();
    if( !isset($data['Technician']) ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Rellena todos los datos."
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }
    $insertar = array('Laborcode' => $data['Laborcode'], 'Technician' => $data['Technician'], 'Fecha_Ingreso' =>$data['Fecha_Ingreso'], 'CrewID' =>$data['CrewID'], 'Employee' =>$data['Employee'], 'Depto' => $data['Depto'], 'Entrenamientos' => $data['Entrenamientos'] );
    $this->db->insert( 'tecnicos', $insertar );

    $this->db->reset_query();

    $insertarLogin = array('usuario' => $data['Laborcode'], 'contrasena' => $data['Laborcode']);
    $this->db->insert( 'login', $insertarLogin );

    $respuesta = array(
      'error' => FALSE,
      'mensaje'=> "Técnico registrado con éxito."
    );
    $this->response( $respuesta );
  }

  //Funcion borrar tecnico
  public function borrar_tecnico_delete( $Laborcode = "" ){
    
    if(  $Laborcode == ""  ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> ""
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }
    
    $condiciones = array('Laborcode' => $Laborcode);
    $this->db->where($condiciones);
    $query = $this->db->get('tecnicos');
    $existe = $query->row();

    if( !$existe ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "No existe técnico con este número de identificación",
                    );
      $this->response( $respuesta );
      return;
    }
  
      $condiciones = array('Laborcode' => $Laborcode);
      $this->db->delete( 'tecnicos', $condiciones);

  
        $respuesta = array(
                  'error' => FALSE,
                  'mensaje'=>'Se ha eliminado el técnico correctamente'
              );
  
    
       $this->response( $respuesta );
    
    
  }

  public function obtener_tecnicos_get( ){

    $query = $this->db->query('SELECT * FROM `tecnicos`');

    $respuesta = array(
            'error' => FALSE,
            'tecnico' => $query->result_array()
          );

    $this->response( $respuesta );

  }

}