<?php namespace BunkerPalace\BunkerData\Models;

use Exception;
use System\Models\File;

/**
 * Video Model
 */
class Video extends File
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'system_videos';

    /**
     * Relations
     */
    public $morphTo = [
        'attachment' => []
    ];

    /**
     * @var array Hidden fields from array/json access
     */
    protected $hidden = ['attachment_type', 'attachment_id', 'is_public'];
}
