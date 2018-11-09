<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Inventario extends REST_Controller {


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
    
    $insertar = array('Fecha' => $data['fecha'], 'NoCrib' => $data['num_producto'], 'DesCrib' =>$data['descripcion'], 'InventarioActual' =>$data['inventario'], 'Minimo' =>$data['minimo'], 'Maximo' => $data['maximo'], 'Vendedor' => $data['vendedor'], 'Planta' => $data['planta'] );
    $this->db->insert( 'inventario', $insertar );

    $respuesta = array(
      'error' => FALSE,
      'mensaje'=> "Invetario registrado con éxito."
    );
    $this->response( $respuesta );
  }

  //Funcion borrar tecnico
  public function borrar_inventario_delete( $id = "" ){
    
    if(  $id == ""  ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> ""
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }
    
    $condiciones = array('ID' => $id);
    $this->db->where( $condiciones );
    $query = $this->db->get('inventario');

    $existe = $query->row();

    if( !$existe ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Este registro no existe"
                  );
      $this->response( $respuesta );
      return;
    }
   

    $condiciones = array('ID' => $id );
    $this->db->delete( 'inventario', $condiciones );


    $respuesta = array(
              'error' => FALSE,
              'mensaje'=>'Se ha eliminado el registro correctamente'
          );

    $this->response( $respuesta );

  }

  public function actualizar_inventario_put(){
    $data = $this->put();
    if(  $data['id']== "" || $data['dato'] == "0" ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> ""
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }

    $condiciones = array('ID' => $data['id']);
    $this->db->where( $condiciones );
    $query = $this->db->get('inventario');

    $existe = $query->row();

    if( !$existe ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Este registro no existe"
                  );
      $this->response( $respuesta );
      return;
    }
    
    $this->db->set('InventarioActual', $data['dato']);
    $this->db->where('ID', $data['id']);
    $this->db->update('inventario');

    $respuesta = array(
      'error' => FALSE,
      'mensaje'=> "El registro se ha modificado."
    );
    $this->response( $respuesta );
    
  }


  public function obtener_todos_get( ){

    $query = $this->db->query('SELECT * FROM `inventario`');

    $respuesta = array(
            'error' => FALSE,
            'inventario' => $query->result_array()
          );

    $this->response( $respuesta );

  }
  public function obtener_piezas_get( ){

    $query = $this->db->query('SELECT * FROM `piezas`');

    $respuesta = array(
            'error' => FALSE,
            'piezas' => $query->result_array()
          );

    $this->response( $respuesta );

  }

  public function buscar_get( $termino = "no especifico" ){
    
    $query = $this->db->query('SELECT * FROM `inventario` where `DesCrib` LIKE "%'. $termino .'%"');

    $respuesta = array(
        'error' => FALSE,
        'inventario' => $query->result_array()
      );

    $this->response( $respuesta );
  }
  
}