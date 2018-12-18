<?php

namespace App;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Utility\Text;

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

    private static $logoW = 300;
    private static $logoH = 300;

    private static $files = [];

    private static $sucess = [];
    private static $errors = [];

    private static $text = "0896358335";

    private static $font;

    const TRANSPARENCE = 90;

    const PADDING = 50;

    public function __construct(Folder $source = null, Folder $destination = null, $tmp = null, $options = []){
        self::setSource($source);
        self::setDestination($destination);
        self::setTemp($tmp);
        self::setLogo();
        self::$font = 4;

        if (isset($options['clean']) && in_array($options['clean'], ['dest', 'd', 'destination'])) {

            exec("rm -fr " . Folder::slashTerm(self::$destination->path) . DS . '*');
            echo "Clean complete" . PHP_EOL;
            exit(0);
        }
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
    public static function run($logoW = null, $logoH = null){
        $path = Folder::slashTerm(self::$destination->path);
        // $logo = self::getLogo();
        $logo = new File(self::resize(self::getLogo(), $logoW ?? self::$logoW, $logoH ?? self::$logoH));

        $groupe = date('dmY_His'); $writter = 0;

        foreach(self::$files[1] as $file_n){
            $file = new File(Folder::slashTerm(self::$source->path) . $file_n);
            $skatek = $path . $groupe . DS . $file->name;
            $newFile = new File($skatek, true);

            imagejpeg(self::pasteLogo($file, $logo), $skatek);
            $writter += 1;

        }

        echo ("{$writter} fichiers écris dans le dossier `{$path}{$groupe}`.\n\nMerci....\nhttp://www.skatek.net\n\n\n");
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
    public static function resize(File $image = null, $width = 200, $height = 200){
        //return self::scaleImageFileToBlob($image->path, $width, $height);

        if(strtolower($image->ext()) == 'png'){
            $source = imagecreatefrompng($image->path);
        } else {
            $source = imagecreatefromjpeg($image->path); // La photo est la source
        }

        $destination = imagecreatetruecolor($width, $height); // On crée la miniature vide
        // Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);

        // On crée la miniature
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $largeur_source, $hauteur_source);

        $link = Folder::slashTerm(TMP) . Text::uuid() . '.' . $image->ext();

        if ($image->ext() == 'png') {
            imagepng($destination, $link);
        } else {
            imagejpeg($destination, $link);
        }


        return $link;
    }

    /**
     * Coler le logo sur l'image
     * @param File $image L'image en question
     */
    public static function pasteLogo(File $image = null, File $logo = null)
    {
        if(self::getLogo() == null){
            self::setError('logo', 'No logo found.')->printErrors();
        }

        if(! $image->exists()){
            self::setError('source', "No source image.")->printErrors();
        }

        list($largeur_source, $hauteur_source, $source_type) = getimagesize($logo->path);
        // list($largeur_source, $hauteur_source, $source_type) = getimagesize(self::getLogo()->path);

        list($largeur_destination, $hauteur_destination, $destination_type) = getimagesize($image->path);

        switch ($source_type)
        {
            case 1: $source = imagecreatefromgif($logo->path); break;
            case 2: $source = imagecreatefromjpeg($logo->path);  break;
            case 3: $source = imagecreatefrompng($logo->path); break;
            default: self::setError('source', "Source image type undefined.")->printErrors(); break;
        }

        switch ($destination_type)
        {
            case 1: $destination = imagecreatefromgif($image->path); break;
            case 2: $destination = imagecreatefromjpeg($image->path);  break;
            case 3: $destination = imagecreatefrompng($image->path); break;
            default: self::setError('destination', "Destination image type undefined.")->printErrors(); break;
        }

        // On doit determiner l'orientation de l'image, puis l'appliquer
        if(function_exists("exif_read_data")){
            $exif_data = exif_read_data($image->path);
            $exif_orientation = $exif_data['Orientation'];

            switch ($exif_orientation) {
                case 3: $rotated_image = imagerotate($destination, 180, 0); break;
                case 6: $rotated_image = imagerotate($destination, -90, 0); break;
                case 8: $rotated_image = imagerotate($destination, 90, 0); break;
            }
        }

        if (isset($rotated_image)) {
            $destination = $rotated_image;
        }


        // On veut placer le logo en bas à droite, on calcule les coordonnées où on doit placer le logo sur la photo
        $destination_x = ( (isset($rotated_image) ? imagesx($destination) : $largeur_destination ) - $largeur_source) - self::PADDING;
        $destination_y = ( (isset($rotated_image) ? imagesy($destination) : $hauteur_destination) - $hauteur_source) - self::PADDING;
        // On met le logo (source) dans l'image de destination (la photo)
        imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source, self::TRANSPARENCE);
        // \imagestringup($destination, $font_size, $text_x, $text_y, self::getText(), $noir);
        // On affiche l'image de destination qui a été fusionnée avec le logo
        // imagejpeg($destination);
        return $destination;
    }

    public static function setError($key, $value = null)
    {
        self::$errors[$key] = $value;
        return new self();
    }

    public static function printErrors(){
        print_r("Errors");
        return 1;
    }

    public static function textImage($text = null){
        $tn_width = 600;
        $tn_height = 200;

        $image = imagecreate(200,50);
        $orange = imagecolorallocate($image, 255, 128, 0); // Le fond est  orange (car c'est la première couleur)
        imagestring($image, 4, 35, 15, $text ?? self::getText(), $noir);
        imagecolortransparent($image, $orange); // On rend le fond orange transparent

        return imagepng($image);
    }

    /**
    * resize 2
    */
    public static function scaleImageFileToBlob($file, $max_width = 200, $max_height = 200) {

        $source_pic = $file;

        list($width, $height, $image_type) = getimagesize($file);

        switch ($image_type)
        {
            case 1: $src = imagecreatefromgif($file); break;
            case 2: $src = imagecreatefromjpeg($file);  break;
            case 3: $src = imagecreatefrompng($file); break;
            default: return '';  break;
        }

        $x_ratio = $max_width / $width;
        $y_ratio = $max_height / $height;

        if( ($width <= $max_width) && ($height <= $max_height) ){
            $tn_width = $width;
            $tn_height = $height;
            }elseif (($x_ratio * $height) < $max_height){
                $tn_height = ceil($x_ratio * $height);
                $tn_width = $max_width;
            }else{
                $tn_width = ceil($y_ratio * $width);
                $tn_height = $max_height;
        }

        $tmp = imagecreatetruecolor($tn_width,$tn_height);

        /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($image_type == 1) OR ($image_type==3))
        {
            imagealphablending($tmp, false);
            imagesavealpha($tmp,true);
            $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
            imagefilledrectangle($tmp, 0, 0, $tn_width, $tn_height, $transparent);
        }
        imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);

        /*
         * imageXXX() only has two options, save as a file, or send to the browser.
         * It does not provide you the oppurtunity to manipulate the final GIF/JPG/PNG file stream
         * So I start the output buffering, use imageXXX() to output the data stream to the browser,
         * get the contents of the stream, and use clean to silently discard the buffered contents.
         */
        ob_start();

        switch ($image_type)
        {
            case 1: imagegif($tmp); break;
            case 2: imagejpeg($tmp, NULL, 100);  break; // best quality
            case 3: imagepng($tmp, NULL, 0); break; // no compression
            default: echo ''; break;
        }

        $final_image = ob_get_contents();

        ob_end_clean();

        return $final_image;
    }

}
