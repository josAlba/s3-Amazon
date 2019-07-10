<?php

    require 'vendor/autoload.php';

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;
    use Aws\S3\MultipartUploader;
    use Aws\Exception\MultipartUploadException;


class s3Amazon{

    private $cliente;

    public function __construct(){
    }

    public function setCliente($key,$secret){

        $this->cliente = new S3Client([
            'region' 	=> 'eu-west-3', 
            'version' 	=> '2006-03-01',
            'credentials' => [
                'key'    => 'YOUR KEY',
                'secret' => 'YOUR KEY',
            ],
        ]);
    }

    public function listarBuckets(){

        return $this->cliente->listBuckets();

    }

    public function sendFile($buket,$file){

        $info = $this->cliente->doesObjectExist($buket, $file);

        if ($info){
            echo 'Existe el archivo';
            return;
        }

        echo "\n ".$file.' >> '.$buket;

        $name = basename($file);
        
        $uploader = new MultipartUploader($this->cliente, $file, [
            'bucket' => $buket,
            'key' => $name,
        ]);
        try{
            $result = $uploader->upload();
            echo "Subida completada: {$result['ObjectURL']}\n";
        }catch (MultipartUploadException $e) {
            echo $e->getMessage() . "\n";
        }
        
    }


}