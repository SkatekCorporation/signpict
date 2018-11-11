<?php

namespace App;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class App
{
    /**
     * @var Folder
     */
    private static $source;

    /**
     * @var Folder
     */
    private static $destination;

    private static $tmpDir;

    private static $files = [];

    private static $text = "0896358335";

    public function __construct(Folder $source = null, Folder $destination = null, $tmp = null){
        self::setSource($source);
        self::setDestination($destination);
        self::setTemp($tmp);
    }

    /**
     * @return self
     */
    public static function setDestination(Folder $dir = null){
        self::$destination = $dir;
        return self::class;
    }

    /**
     * @return self
     */
    public static function setTemp($tmp = null){
        if($tmp instanceof Folder){
            self::$tmpDir = $tmp;
        } else {
            self::$tmpDir = new Folder($tmp, true);
        }
        return self::class;
    }

    /**
     * 
     */
    public static function setSource(Folder $dir = null){
        self::$source = $dir;
        return self::class;
    }

    /**
     * Demarrer l'operation
     */
    public static function run(){
        var_dump(self::$tmpDir);
    }

    private static function setFiles(){
        self::$files = self::$source->read();
        return self::class;
    }

    public static function setText($text = null){
        if(is_string($text)){
            self::$text = $text;
        }
        return self::class;
    }

    public static function getText(){
        return self::$text;
    }

    /**
     * @return File|null
     */
    public static function getLogo()
    {
        $files = new Folder(DEFAULT_LOGO);
        if(count($files->read()[1])){
            return new File($files->path . $files->read()[1][0]);
        }
        return null;
    }

    /**
     * Redimensionner une image
     * @param string L'image a redimentionner
     * @param int $width La largeur du redimesion
     * @param int $height La hauteur du redimension 
     * @return Image
     */
    public static function resize($image = null, $width = 200, $height = 150){
        $source = imagecreatefromjpeg($image); // La photo est la source
        $destination = imagecreatetruecolor($width, $height); // On crée la miniature vide
        // Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);
        $largeur_destination = imagesx($destination);
        $hauteur_destination = imagesy($destination);

        // On crée la miniature
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
        // On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
        // imagejpeg($destination, "mini_couchersoleil.jpg");
        return $destination;
    }

}
