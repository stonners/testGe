<?php namespace BunkerPalace\BunkerData\Behaviors;

use Db;
use Config;
use October\Rain\Extension\ExtensionBase;
use System\Classes\PluginManager;
use RainLab\Translate\Models\Locale;
use BunkerPalace\BunkerData\Models\SearchIndex;
use Str;

class SearchableModel extends ExtensionBase {

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
        $this->model->morphMany[] = [
            'search_indices' => ['BunkerPalace\BunkerData\Models\SearchIndex', 'name' => 'searchable']
        ];
    }

    public function beforeSave() {

        $locales = $this->getLocales();

        if (!$this->hasTranslatableFields()) {
            $locales = ['und'];
        }

        foreach($locales as $locale) {

            $indexData = [
                'title' => '',
                'data' => []
            ];

            $searchableFields = $this->model->searchableConfig['fields'];
            $titleAttribute = $this->model->searchableConfig['title_attribute'];

            if (!in_array($titleAttribute, $searchableFields)) {
                $searchableFields = array_merge($searchableFields, [$titleAttribute]);
            }

            foreach($this->model->searchableConfig['fields'] as $searchableField) {

                if ($this->isFieldTranslatable($searchableField)) {
                    $value = $this->model->noFallbackLocale()->lang($locale)->$searchableField;
                } else {
                    $value = $this->model->{$searchableField};
                }

                if (is_string($value)) {

                    if ($searchableField == $titleAttribute) {
                        $indexData['title'] = $this->sanitizeValue($value);
                    } else {
                        $indexData['data'][] = $this->sanitizeValue($value);
                    }

                }

            }

            $this->updateIndex($indexData, $locale);

        }

    }

    protected function sanitizeValue($value) {
        return strip_tags(html_entity_decode($value, ENT_COMPAT, 'UTF-8'));
    }

    protected function updateIndex($indexData, $locale) {

        $data = join($indexData['data']);
        $data = trim(preg_replace("/[\r\n]+/", ' ', $data));

        $title = $indexData['title'];

        $obj = Db::table('search_indices')
            ->where('locale', $locale)
            ->where('searchable_type', get_class($this->model))
            ->where('searchable_id', $this->model->getKey());

        $recordExists = $obj->count() > 0;

        if (!strlen($data) && !strlen($title)) {
            if ($recordExists) {
                $obj->delete();
            }
            return;
        }

        if ($recordExists) {
            $obj->update([
                'title' => $title,
                'data' => $data
            ]);
        } else {
            Db::table('search_indices')->insert([
                'locale' => $locale,
                'searchable_type' => get_class($this->model),
                'searchable_id' => $this->model->getKey(),
                'title' => $title,
                'data' => $data
            ]);
        }

    }

    protected function isFieldTranslatable($key) {
        return $this->hasTranslatableFields() && in_array($key, $this->model->getTranslatableAttributes());
    }

    protected function hasTranslatableFields() {
        return $this->isPluginAvailable('RainLab.Translate') && is_array($this->model->translatable);
    }

    protected function getLocales() {
        return $this->hasTranslatableFields() ? array_keys(Locale::ListEnabled()) : [Config::get('app.locale')];
    }

    protected function isPluginAvailable($name) {
        return PluginManager::instance()->hasPlugin($name)
        && !PluginManager::instance()->isDisabled($name);
    }

    public function searchConditions() {
        return [];
    }

    public function getSearchResultTitle($searchIndex) {
        return $this->model->{$this->model->searchableConfig['title_attribute']};
    }

    public function getSearchResultExcerpt($searchIndex) {

        $length = 250;

        $text = $searchIndex->data;
        $query = $searchIndex->query;

        debug($query);

        $keywords = implode('|', explode(' ', preg_quote($query)));
        $text = preg_replace("/($keywords)/i","<mark>$0</mark>", $text);

        $loweredText  = mb_strtolower($this->stripAccents($text));
        debug($loweredText);
        $loweredQuery = mb_strtolower($query);
        $position = mb_strpos($loweredText, '<mark>' . $loweredQuery . '</mark>');
        $start = (int)$position - ($length / 2);

        if ($start < 0) {
            $excerpt = Str::limit($text, $length);
        } else {
            $excerpt = '...' . trim(mb_substr($text, $start, $length)) . '...';
        }

        return $this->checkBorders($excerpt);
    }

    public function getSearchResultUrl($searchIndex) {
        return '';
    }

    protected function checkBorders($excerpt) {

        $openings = substr_count($excerpt, '<mark>');
        $closings = substr_count($excerpt, '</mark>');
        if ($openings !== $closings) {
            $position = mb_strrpos($excerpt, '<mark>');
            $excerpt  = trim(mb_substr($excerpt, 0, $position)) . '...';
        }
        return $excerpt;

    }

    protected function stripAccents($str) {
        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }

}
