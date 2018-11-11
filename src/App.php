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

    private static $logo = null;

    private static $files = [];

    private static $sucess = [];
    private static $errors = [];

    private static $text = "0896358335";

    const TRANSPARENCE = 50;

    const PADDING = 10;

    public function __construct(Folder $source = null, Folder $destination = null, $tmp = null){
        self::setSource($source);
        self::setDestination($destination);
        self::setTemp($tmp);
        self::setLogo();
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
     * @return self
     */
    public static function setSource(Folder $dir = null){
        self::$source = $dir;
        self::setFiles();
        return self::class;
    }

    /**
     * Demarrer l'operation
     */
    public static function run(){
        $path = Folder::slashTerm(self::$destination->path);
        $logo = self::resize(self::getLogo(), 50, 20);
        $groupe = date('d-M-Y His'); $writter = 0;
        
        foreach(self::$files[1] as $file_n){
            $file = new File(Folder::slashTerm(self::$source->path) . $file_n);
            $skatek = $path . $groupe . DS . $file->name;
            $newFile = new File($skatek, true);
            // $newFile->create();
            // $newFile->write(self::pasteLogo($file), 'wb');
            // if(touch($skatek)){
                imagejpeg(self::pasteLogo($file), $skatek);
                $writter += 1;

            // }
        }

        echo ("{$writter} fichiers écris dans le dossier `{$path}`.\n\nMerci....\nhttp://www.skatek.net\n\n\n");
        return 0;
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
    public static function setLogo($image = null)
    {
        if($image == null){
            $files = new Folder(DEFAULT_LOGO);
            if(count($files->read()[1])){
                self::$logo = new File($files->path . $files->read()[1][0]);
            }
        } else {
            self::$logo = (new File(TMP . date('Ymd_His_') . rand(1, 1000) . '.jpg', true))->write($image);
        }
        return self::class;
    }

    public static function getLogo(){
        return self::$logo;
    }

    /**
     * Redimensionner une image
     * @param string L'image a redimentionner
     * @param int $width La largeur du redimesion
     * @param int $height La hauteur du redimension 
     * @return Image
     */
    public static function resize(File $image = null, $width = 200, $height = 150){
        
        if(strtolower($image->ext()) == 'png'){
            $source = imagecreatefrompng($image->path);
        } else {
            $source = imagecreatefromjpeg($image->path); // La photo est la source
        }

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

    /**
     * Coler le logo sur l'image
     * @param File $image L'image en question
     */
    public static function pasteLogo(File $image = null)
    {
        if(self::getLogo() == null){
            self::setError('logo', 'No logo found.')->printErrors();
        }

        if(! $image->exists()){
            self::setError('source', "No source image.")->printErrors();
        }

        // $destination = new File(Folder::slashTerm(self::$destination->path) . date('Ymd_His_') . $image->name, true);

        $source = imagecreatefrompng(self::getLogo()->path); // Le logo est la source
        $destination = imagecreatefromjpeg($image->path); // La photo est la destination
        // Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);
        $largeur_destination = imagesx($destination);
        $hauteur_destination = imagesy($destination);
        // On veut placer le logo en bas à droite, on calcule les coordonnées où on doit placer le logo sur la photo
        $destination_x = ($largeur_destination - $largeur_source) - self::PADDING;
        $destination_y = ($hauteur_destination - $hauteur_source) - self::PADDING;
        // On met le logo (source) dans l'image de destination (la photo)
        imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source, self::TRANSPARENCE);

        // On affiche l'image de destination qui a été fusionnée avec le logo
        // imagejpeg($destination);
        return $destination;
    }

    public static function setError($key, $value = null)
    {
        self::$errors[$key] = $value;
        return self::class;
    }

    public static function printErrors(){
        print_r("Errors");
        return 1;
    }

}
