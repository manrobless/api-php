<?php 
class Certificados {
  public function contenidoCertificado($pathCertificado) {
    $archivo = file_get_contents($pathCertificado);
    $cert = "-----BEGIN CERTIFICATE-----\n" . chunk_split(base64_encode($archivo), 64, "\n") . "-----END CERTIFICATE-----";
    return $cert;
  }

  public function contenidoLlave($pathLlave) {
    $archivo   = file_get_contents($pathLlave);
    return $archivo;
  }

  public function passwordLlave($pathPassword) {
    $password = file_get_contents($pathPassword);
    return $password;
  }
}
?>