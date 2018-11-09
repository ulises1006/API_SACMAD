<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once (APPPATH . '/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Prueba extends REST_Controller {

    public function index()
	{
		echo "Hola pompudita";
    }
    
    public function obtener_arreglo_get( $index = 0 ){
		$arreglo = array("Manzana", "Pera", "Naranja");
        //echo json_encode($arreglo[$index]);
        $this->response($arreglo[$index]);
    }
    
    public function obtener_producto_get( $codigo ){
        $this->load->database();
        $query = $this->db->query("SELECT * FROM `productos` WHERE codigo = '".$codigo."'");

        //echo json_encode($query->result());
        $this->response($query->result());
    }
}