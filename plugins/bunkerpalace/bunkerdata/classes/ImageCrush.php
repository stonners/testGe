<?php namespace BunkerPalace\BunkerData\Classes;

use October\Rain\Database\Attach\File;
use ToughDeveloper\ImageResizer\Classes\Image;

class ImageCrush extends Image
{

    protected function getThumbFilename($width, $height)
    {
        $width = (integer) $width;
        $height = (integer) $height;

        list($w, $h, $type, $attr) = getimagesize($this->filePath);

        $original_aspect = $w / $h;
        
        if($height != 0 && $width != 0) {
            $thumb_aspect = $width / $height;
        } else {
            $thumb_aspect = $original_aspect;
        }

        if($original_aspect < $thumb_aspect) {
            //debug('crop verticaly');
            $w = $width;
            $h = $w / $original_aspect;
            $max_offset_x = 0;
            $max_offset_y = -(($h - $height) / 2);
        } elseif($original_aspect > $thumb_aspect) {
            //debug('crop horizontaly');
            $h = $height;
            $w = $h * $original_aspect;
            $max_offset_y = 0;
            $max_offset_x = (($w - $width) / 2);
        } else {
            if($height == 0) {
                $w = $width;
                $h = $w / $original_aspect;
            } else {
                $h = $height;
                $w = $h * $original_aspect;
            }
            $max_offset_y = 0;
            $max_offset_x = 0;
        }
        
        $this->options['offset'][0] = -round($max_offset_x * (($this->options['offset'][0]/50)-1));
        $this->options['offset'][1] = round($max_offset_y * (($this->options['offset'][1]/50)-1));

        //
        // Clamp the offsets
        //
        $this->options['offset'][0] = max( -($w - $width)/2, $this->options['offset'][0]);
        $this->options['offset'][0] = min( ($w - $width)/2, $this->options['offset'][0]);
        $this->options['offset'][1] = max( -($h - $height)/2, $this->options['offset'][1]);
        $this->options['offset'][1] = min( ($h - $height)/2, $this->options['offset'][1]);
        
        return 'thumb__' . $width . '_' . $height . '_' . $this->options['offset'][0] . '_' . $this->options['offset'][1] . '_' . $this->options['mode'] . '.' . $this->options['extension'];
    }

    /**
     * Compresses a png image using tinyPNG
     * @return string
     */
    protected function compressWithTinyPng()
    {
        try {

            $filePath = $this->getCachedImagePath();
            if($this->options['extension'] == 'jpg' || $this->options['extension'] == 'jpeg') {
                exec('jpegoptim --max 80 --strip-all ' . $filePath);
            }
            elseif($this->options['extension'] == 'png') {
                exec('pngquant --quality 65-80 --force ' . $filePath);
            }

        }
        catch (\Exception $e) {
            // Log error - may help debug
            \Log::error('Bunker compress failed', [
                'message'   => $e->getMessage(),
                'code'      => $e->getCode()
            ]);
        }

    }

    /**
     * Checks if image compression is enabled for this image.
     * @return bool
     */
    protected function isCompressionEnabled()
    {
        return ($this->options['extension'] != 'gif' && $this->options['compress']);
    }

}
