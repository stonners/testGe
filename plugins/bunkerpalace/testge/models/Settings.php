<?php namespace BunkerPalace\TestGe\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'BunkerPalace_testGe_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
   
    protected $rules = [
        'site_title' => 'required',
        'site_meta_keywords' => 'required',
        'site_meta_description' => 'required',
    ];
    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'bunkerpalace_testge_settings';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
