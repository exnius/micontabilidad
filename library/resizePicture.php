<?php
Class resizePicture
{

    // *** Class variables
    private $image;
    private $width;
    private $height;
    private $imageResized;

    function __construct($fileName) {
        //Open file image
        $this->image = $this->openImage($fileName);

        //Get width and height
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    private function openImage($file)
    {
        // *** Get extension
        $extension = strtolower(strrchr($file, '.'));

        switch ($extension) {
            case '.jpg':
                $image = @imagecreatefromjpeg($file);
                break;
            case '.gif':
                $image = @imagecreatefromgif($file);
                break;
            case '.png':
                $image = @imagecreatefrompng($file);
                break;
            default:
                $image = false;
                break;
        }
        return $image;
    }


    public function resizeImage($newWidth, $newHeight)
    {
        $optionArray = $this->getDimensions($newWidth, $newHeight);
        $optimalWidth = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];

        $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);
    }


    private function getDimensions($newWidth, $newHeight) 
    {
        $optionArray = $this->getSize($newWidth, $newHeight);
        $optimalWidth = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];
        
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }


    private function getSizeByFixedHeight($newHeight)
    {
        $ratio = $this->width / $this->height;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }

    private function getSizeByFixedWidth($newWidth) {
        $ratio = $this->height / $this->width;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }
    
    private function getSize($newWidth, $newHeight)
    {
        if ($this->height < $this->width) {
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);
        } elseif ($this->height > $this->width) {
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;
        } else {
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
            } elseif ($newHeight > $newWidth) {
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;
            } else {
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
            }
        }

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }


    public function saveImage($savePath, $imageQuality = "100") {
        // *** Get extension
        $extension = strrchr($savePath, '.');
        $extension = strtolower($extension);

        switch ($extension) {
            case '.jpg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->imageResized, $savePath, $imageQuality);
                }
                break;

            case '.gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->imageResized, $savePath);
                }
                break;

            case '.png':
                // *** Scale quality from 0-100 to 0-9
                $scaleQuality = round(($imageQuality / 100) * 9);

                // *** Invert quality setting as 0 is best, not 9
                $invertScaleQuality = 9 - $scaleQuality;

                if (imagetypes() & IMG_PNG) {
                    imagepng($this->imageResized, $savePath, $invertScaleQuality);
                }
                break;
        }

        imagedestroy($this->imageResized);
    }

}
?>
