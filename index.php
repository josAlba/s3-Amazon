<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;


class s3Amazon
{
	/**
	 * Cliente
	 * @access private
	 */
    private $cliente;
    public function __construct()
	{ }
	/**
	 * Establecer la conexion
	 * @param string $key		Clave
	 * @param string $secret	Secret
	 * @return null
	 */
    public function setCliente($key, $secret)
    {
        $this->cliente = new S3Client([
            'region'     => 'eu-west-3',
            'version'     => '2006-03-01',
            'credentials' => [
                'key'    => 'YOUR KEY',
                'secret' => 'YOUR KEY',
            ],
        ]);
	}
	/**
	 * Listar los buckets
	 * @return array
	 */
    public function listarBuckets()
    {
        return $this->cliente->listBuckets();
	}
	/**
	 * Subir un archivo
	 * @param string $bucket	Buket donde se almacenara el fichero.
	 * @param string $file	Archivo que se quiere subir
	 * @return null
	 */
    public function sendFile($buket, $file)
    {
        $info = $this->cliente->doesObjectExist($buket, $file);
        if ($info) {
            echo 'Existe el archivo';
            return;
        }
        echo "\n " . $file . ' >> ' . $buket;
        $name = basename($file);
        $uploader = new MultipartUploader($this->cliente, $file, [
            'bucket' => $buket,
            'key' => $name,
        ]);
        try {
            $result = $uploader->upload();
            echo "Subida completada: {$result['ObjectURL']}\n";
        } catch (MultipartUploadException $e) {
            echo $e->getMessage() . "\n";
        }
	}
	/**
	 * Descarga un fichero
	 * @param string $bucket Buket donde esta almacenado el fichero.
	 * @param string $file 	Archivo que se quiere recuperar.
	 * @return string		Archivo.
	 */
    public function downloadFile($buket, $file)
    {
        try {
            $result = $this->cliente->getObject([
                'Bucket' => $buket,
                'Key'    => $file
            ]);
            header("Content-Type: {$result['ContentType']}");
            echo $result['Body'];
        } catch (Exception $e) {
            echo 'Acceso no permitido';
        }
    }
}

?>
