<?php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;

class DocDigitales {
  public function generacionFactura($factura) {
    try {
      $uriGeneracion = "http://api.docdigitales.com/v1/facturas/generar";
      $generada = $this->getPostResponse($uriGeneracion, $factura);
      return $generada;
    } catch (Exception $e) {
      echo $e;
      return null;
    }
  }

  public function cancelacionFactura($cancelacion) {
    try {
      $uriCancelacion = "http://api.docdigitales.com/v1/facturas/cancelar";
      $cancelada = $this->getPostResponse($uriCancelacion, $cancelacion);
      return $cancelada;
    } catch (Exception $e) {
      echo $e;
      return null;
    }
  }

  public function envioFactura($envio) {
    try {
      $uriEnvio = "http://api.docdigitales.com/v1/facturas/enviar";
      $enviada = $this->getPostResponse($uriEnvio, $envio);
      return $enviada;
    } catch (Exception $e) {
      echo $e;
      return null;
    }
  }

  public function descargaFactura($descarga) {
    try {
      $uriDescarga = "http://api.docdigitales.com/v1/facturas/descargar";
      $descargada = $this->getPostResponse($uriDescarga, $descarga);
      return $descargada;
    } catch (Exception $e) {
      echo $e;
      return null;
    }
  }

  private function getPostResponse($uri, $data) {
    $client = new GuzzleHttp\Client(['base_uri' => $uri]);
    $options = ['headers' => [
      'Authorization' => "Token token=123123123",
      'Accept'        => 'application/json',
      'Content-Type'  => 'application/json',
      'Access-Control-Allow-Origin' => '*'      
    ],'body' => json_encode($data)];
    
    $response = $client->request('POST', $uri, $options);
    $stream = Psr7\stream_for($response->getBody());
    return json_decode($stream, true);
  }
}
?>