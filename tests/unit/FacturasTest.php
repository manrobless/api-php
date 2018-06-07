<?php
  use PHPUnit\Framework\TestCase;
  require "lib/Certificados.php";
  require "lib/DocDigitales.php";

  class FacturasTest extends TestCase {
    public function setUp() {
      date_default_timezone_set('America/Tijuana');
    }

    /** @test */
    public function generacionFactura() {
      $api  = new DocDigitales();
      $cert = new Certificados();
      $json = '{"meta":{"empresa_uid":"asd123asd","empresa_api_key":"123123123","ambiente":"S","objeto":"factura"},"data":[{"datos_fiscales":{"certificado_pem":"","llave_pem":"","llave_password":""},"cfdi":{"cfdi__comprobante":{"folio":"123","fecha":"2018-03-25T12:12:12","tipo_comprobante":"I","lugar_expedicion":"21100","forma_pago":"01","metodo_pago":"PUE","moneda":"MXN","tipo_cambio":"1","subtotal":"99.00","total":"99.00","cfdi__emisor":{"rfc":"DDM090629R13","nombre":"Emisor Test","regimen_fiscal":"601"},"cfdi__receptor":{"rfc":"XEXX010101000","nombre":"Receptor Test","uso_cfdi":"G01"},"cfdi__conceptos":{"cfdi__concepto":[{"clave_producto_servicio":"01010101","clave_unidad":"KGM","cantidad":"1","descripcion":"descripcion test","valor_unitario":"99.00","importe":"99.00","unidad":"unidad","no_identificacion":"KGM123","cfdi__impuestos":{"cfdi__traslados":{"cfdi__traslado":[{"base":"99.00","impuesto":"002","tipo_factor":"Exento"}]}}}]}}}}]}';
      $factura = json_decode($json, true);
      
      # Llenar los datos fiscales y ponerle fecha presente al comprobante
      $factura["data"][0]["datos_fiscales"]["certificado_pem"]  = $cert->contenidoCertificado("vendor/certificados/certificado.cer");
      $factura["data"][0]["datos_fiscales"]["llave_pem"]        = $cert->contenidoLlave("vendor/certificados/llave.pem");
      $factura["data"][0]["datos_fiscales"]["llave_password"]   = $cert->passwordLlave("vendor/certificados/password.txt");
      $factura["data"][0]["cfdi"]["cfdi__comprobante"]["fecha"] = date('Y-m-d\TH:i:s', time());
      
      # Generar
      $facturaGenerada = $api->generacionFactura($factura);
      # Validar que el uuid venga en la respuesta
      $uuid = $facturaGenerada["data"][0]["cfdi_complemento"]["uuid"];
      $this->assertNotNull($uuid);
    }

    /** @test */
    public function cancelacionFactura() {
      $api  = new DocDigitales();
      $cert = new Certificados();
      $json = '{"meta":{"empresa_uid":"asd123asd","empresa_api_key":"123123123","ambiente":"S","objeto":"factura"},"data":[{"rfc":"","uuid":[""],"datos_fiscales":{"certificado_pem":"","llave_pem":"","password_llave":""},"acuse": false}]}';
      $uuidCancelar = "C39C7784-B41E-40D6-89E7-46683205ED6C";
      $cancelacion = json_decode($json, true);

      # Llenar los datos fiscales y la informacion de la cancelacion
      $cancelacion["data"][0]["rfc"] = "DDM090629R13";
      $cancelacion["data"][0]["uuid"][0] = $uuidCancelar;
      $cancelacion["data"][0]["datos_fiscales"]["certificado_pem"]  = $cert->contenidoCertificado("vendor/certificados/certificado.cer");
      $cancelacion["data"][0]["datos_fiscales"]["llave_pem"]        = $cert->contenidoLlave("vendor/certificados/llave.pem");
      $cancelacion["data"][0]["datos_fiscales"]["llave_password"]   = $cert->passwordLlave("vendor/certificados/password.txt");

      # Cancelar
      $facturaCancelar = $api->cancelacionFactura($cancelacion);
      $this->assertEquals("Cancelado Exitosamente", $facturaCancelar["data"][0]["descripcion"]);
    }

    /** @test */
    public function envioFactura() {
      $api  = new DocDigitales();
      $json = '{"meta":{"empresa_uid":"asd123asd","empresa_api_key":"123123123","ambiente":"S","objeto":"factura"},"data":[{"uuid":[""],"destinatarios":[{"correo":"sandbox@docdigitales.com"}],"titulo":"Envio de Factura: 123","texto":"Envio de Factura con folio 123, para su revision.","pdf":"true"}]}';
      $uuid = "ACF6B8DB-AA7C-4FBC-A0A2-D8FE04220E2B";
      $envio = json_decode($json, true);
      # Llenar la informacion del Envio
      $envio["data"][0]["uuid"][0] = $uuid;
      # Generar Envio
      $facturaEnviada = $api->envioFactura($envio);
      $this->assertNotNull($facturaEnviada);
    }

    /** @test */
    public function descargaFactura() {
      $api  = new DocDigitales();
      $json = '{"meta":{"empresa_uid":"asd123asd","empresa_api_key":"123123123","ambiente":"S","objeto":"factura"},"data":[{"uuid":[""],"destinatarios":[{"correo":"sandbox@docdigitales.com"}],"titulo":"Descargar factura","texto":"Adjunto factura generada","pdf":"true"}]}';
      $uuid = "ACF6B8DB-AA7C-4FBC-A0A2-D8FE04220E2B";
      $descarga = json_decode($json, true);
      # Llenar la informacion de la descargar
      $descarga["data"][0]["uuid"][0] = $uuid;
      # Generar Descarga
      $facturaDescargada = $api->descargaFactura($descarga);
      $this->assertNotNull($facturaDescargada["data"][0]["link"]);
    }
  }
?>