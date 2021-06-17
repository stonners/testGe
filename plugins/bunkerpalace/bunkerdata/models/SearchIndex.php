<?php namespace BunkerPalace\BunkerData\Models;

use Model;
use Config;

/**
 * SearchIndex Model
 */
class SearchIndex extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'search_indices';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    public $morphTo = [
        'searchable' => []
    ];

    protected $appends = [
        'title',
        'excerpt',
        'url',
        'query'
    ];

    public static function search($searchQuery, $types = []) {

        $searchQueryParts = self::getQueryParts($searchQuery);

        if (empty($searchQueryParts)) {
            return collect([]);
        }

        $searchQuery = implode('* ', $searchQueryParts) . '*';

        $match = "MATCH (title,data) AGAINST (? IN BOOLEAN MODE)";

        $query = self::whereIn('searchable_type', $types)
        ->where(function($query) {

            $query->where('locale', Config::get('app.locale'))->orWhere('locale', 'und');

        })->whereRaw($match, [$searchQuery]);

        if (!empty($types)) {

            $query->where(function($query) use ($types) {

                foreach($types as $type) {

                    $query->orWhere(function($query) use ($type) {

                        $query->where('searchable_type', $type);

                        $model = new $type;

                        if (!empty($model->searchConditions())) {

                            $query->whereIn('searchable_id', function($query) use ($model) {
                                $table = $model->getTable();
                                $query->select($table . '.id')->from($table)->where($model->searchConditions());
                            });

                        }

                    });

                }

            });

        }

        $results = $query->get();

        $results->map(function($result, $key) use ($searchQueryParts) {
            return $result->query = implode(' ', $searchQueryParts);
        });

        return $results;

    }

    public static function getQueryParts($query) {
        $query = preg_replace('/[^\p{Latin}\d ]/u', ' ', $query);
        return array_filter(explode(' ', $query));
    }

    public function getTitle() {

        return $this->searchable->getSearchResultTitle($this);

    }

    public function getExcerpt() {

        return $this->searchable->getSearchResultExcerpt($this);

    }

    public function getUrl() {

        return $this->searchable->getSearchResultUrl($this);

    }
}
