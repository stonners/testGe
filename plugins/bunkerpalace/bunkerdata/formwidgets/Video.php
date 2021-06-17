<?php namespace BunkerPalace\BunkerData\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Exception;
use October\Rain\Exception\ApplicationException;

/**
 * Video Form Widget
 */
class Video extends FormWidgetBase
{
    use \Backend\Traits\FormModelWidget;

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('config_form');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['videoList'] = $this
            ->getRelationObject()
            ->withDeferred($this->sessionKey)
            ->orderBy('sort_order')
            ->get();
        $this->vars['relationType'] = $this->getRelationType();
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/video.css', 'core');
        $this->addJs('js/video.js', 'core');
    }

    /**
     * Validate config form
     */
    public function onGenerateVideo()
    {
        $url = input('video_upload_url', '');

        $api = "https://noembed.com/embed?url=".$url;
        $json = file_get_contents($api);
        $data = json_decode($json, true);

        if(isset($data['error'])) {
            throw new ApplicationException("Sorry, the recovery of this video is not allowed !");
        } else {
            $save = $this->onSaveVideo($data);
            $this->vars['data'] = $save;
            return $this->makePartial('video');
        }
    }

    /**
     * Save a video attachment.
     */
    public function onSaveVideo($data){
        $video = new \BunkerPalace\BunkerData\Models\Video();
        $video->disk_name = uniqid(null, true);
        $video->title = $data['title'];
        $video->url = $data['url'];
        $video->provider_name = $data['provider_name'];
        $video->width = $data['width'];
        $video->height = $data['height'];
        $video->thumbnail_url = $data['thumbnail_url'];
        $video->thumbnail_width = $data['thumbnail_width'];
        $video->thumbnail_height = $data['thumbnail_height'];
        $video->html = $data['html'];
        $video->url = $data['url'];
        $video->save();

        $videoRelation = $this->getRelationObject();

        /**
         * Attach directly to the parent model if it exists and attachOnUpload has been set to true
         * else attach via deferred binding
         */
        $parent = $videoRelation->getParent();
        if ($this->attachOnUpload && $parent && $parent->exists) {
            $videoRelation->add($video);
        } else {
            $videoRelation->add($video, $this->sessionKey);
        }

        return $video;
    }

    /**
     * Removes a video attachment.
     */
    public function onRemoveVideo()
    {
        $videoModel = $this->getRelationModel();
        if (($fileId = post('video_id')) && ($file = $videoModel::find($fileId))) {
            $this->getRelationObject()->remove($file, $this->sessionKey);
        }
    }

    /**
     * Sorts video attachments.
     */
    public function onSortVideo()
    {
        if ($sortData = post('sortOrder')) {
            $ids = array_keys($sortData);
            $orders = array_values($sortData);

            $videoModel = $this->getRelationModel();
            $videoModel->setSortableOrder($ids, $orders);
        }
    }
}
