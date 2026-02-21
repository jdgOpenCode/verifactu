<?php

namespace jdgOpenCode\verifactu;

class VeriFactuValidarNIF
{
    /**
     * Valida un NIF/CIF/NIE contra el servicio REST de la AEAT
     * Requiere certificado digital válido
     *
     * @param string $nif        NIF/CIF/NIE a comprobar
     * @param string $nombre     Nombre/Razón social esperado (puede ir vacío)
     * @param string $certPath   Ruta al certificado (.p12 o .pem)
     * @param string $certPass   Contraseña del certificado
     * @param bool   $pruebas    true = entorno de pruebas, false = producción
     * @return array|null        Respuesta de la AEAT en array o null si error
     * 
     * // ----------------
     * // Ejemplo de uso
     * // ----------------
     * $result = validarNifAEAT(
     *     "B12345678",          // NIF de prueba
     *     "EMPRESA DEMO SL",    // Nombre esperado (puede ir vacío "")
     *     "/ruta/certificado.p12", // Certificado digital
     *     "password",              // Contraseña del certificado
     *     true                     // true = pruebas
     * );
     * // Respuesta:
     * {
     *  "respuesta": {
     *    "codigoError": "00",
     *    "descripcion": "Contribuyente identificado",
     *    "nombre": "EMPRESA DEMO SL",
     *    "nif": "B12345678"
     *  } 
     * }
     */
    public static function validarNifAEAT($nif, $nombre, $certPath, $certPass, $pruebas = true): array|null
    {
        return null;
        
        $url = $pruebas
            ? "https://www.agenciatributaria.gob.es/wlpl/BUCV-JDIT/ws/ValNifV2Rest"
            : "https://www.agenciatributaria.gob.es/wlpl/BUCV-JDIT/ws/ValNifV2Rest";
        $payload = json_encode([
            "contribuyente" => [
                "nif"    => strtoupper(trim($nif)),
                "nombre" => $nombre
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json"
        ]);

        // Autenticación con certificado digital
        curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $certPass);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "cURL error: " . curl_error($ch);
            return null;
        }
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }
        return json_decode($response, true);
    }
}
