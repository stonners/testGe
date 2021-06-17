<?php namespace BunkerPalace\BunkerData\FormWidgets;

use Input;
use ToughDeveloper\ImageResizer\Classes\Image;

class FileUploadFocus extends \Backend\FormWidgets\FileUpload {
    use \Backend\Traits\FormModelWidget;

    public function onUpdateFocusPoint()
    {
        $fileModel = $this->getRelationModel();
        if (($fileId = Input::get('file')) && ($file = $fileModel::find($fileId))) {
            if($file->bd_offset_x && $file->bd_offset_y) {
                $file->bd_offset_x = Input('x');
                $file->bd_offset_y = Input('y');
                $file->save();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('/modules/backend/formwidgets/fileupload/assets/css/fileupload.css', 'core');
        $this->addJs('/modules/backend/formwidgets/fileupload/assets/js/fileupload.js', 'core');
        $this->addJs('https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.5/lib/draggable.bundle.js', 'core');

        $this->addCss('css/fileuploadfocus.css', 'core');
        $this->addJs('js/fileuploadfocus.js', 'core');
    }

    /**
     * Loads the configuration form for an attachment, allowing title and description to be set.
     */
    public function onLoadAttachmentConfig()
    {
        $fileModel = $this->getRelationModel();
        $height = 300;
        if (($fileId = post('file_id')) && ($file = $fileModel::find($fileId))) {
            $file = $this->decorateFileAttributes($file);

            $image = new Image($file->getLocalPath());

            $this->vars['file'] = $file;
            $this->vars['fileThumb'] = $image->resize(0, $height);
            $this->vars['displayMode'] = $this->getDisplayMode();
            $this->vars['cssDimensions'] = 'height: ' . $height . 'px;';
            $this->vars['relationManageId'] = post('manage_id');
            $this->vars['relationField'] = post('_relation_field');

            list($w, $h, $type, $attr) = getimagesize($file->getLocalPath());
            $width = ceil(($w * $height) / $h);
            $this->vars['cssDimensionsFocus'] = 'width: ' . $width . 'px; height: ' . $height . 'px; margin-left: -' . $width/2 . 'px;';

            return $this->makePartial('config_form');
        }

        throw new ApplicationException('Unable to find file, it may no longer exist');
    }
}
