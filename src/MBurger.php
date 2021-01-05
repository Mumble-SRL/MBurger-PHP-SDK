<?php

namespace Mumble\MBurger;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MBurger
{
    const URL = 'https://mburger.cloud/api';

    protected $locale = 'en';

    protected $include = [];

    protected $sort = 'id';

    protected $skip = 0;

    protected $take = 25;

    protected $filters = [];

    protected $media_type = 'medium';

    protected $force_locale_fallback = false;

    protected $force_slug = false;

    protected $coordinates = [];

    protected $cache_ttl = 0;

    // Modifiers
    public function locale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function include(array $include = []): self
    {
        $this->include = $include;
        return $this;
    }

    public function includeContracts(): self
    {
        $this->include[] = 'contracts';
        return $this;
    }

    public function includeBeacons(): self
    {
        $this->include[] = 'beacons';
        return $this;
    }

    public function includeBlocks(): self
    {
        $this->include[] = 'blocks';
        return $this;
    }

    public function includeSections(): self
    {
        if (in_array('blocks')) {
            $this->include = array_merge(array_diff($this->include, ['blocks']), 'blocks.sections');
        } else {
            $this->include[] = 'sections';
        }

        return $this;
    }

    public function includeElements(): self
    {
        if (in_array('sections')) {
            $this->include = array_merge(array_diff($this->include, ['sections']), 'sections.elements');
        } else {
            $this->include[] = 'elements';
        }

        return $this;
    }

    public function includeStructure(): self
    {
        $this->include[] = 'structure';
        return $this;
    }

    public function sortBy(string $value, string $direction = 'asc'): self
    {
        $this->sort = $direction == 'asc' ? $value : '-'.$value;
        return $this;
    }

    public function skip(int $skip): self
    {
        $this->skip = $skip;
        return $this;
    }

    public function take(int $take): self
    {
        $this->take = $take;
        return $this;
    }

    public function filterByIds(array $ids): self
    {
        $this->filters['id'] = implode(',', $ids);
        return $this;
    }

    public function filterByRelation(int $block_id, int $section_id): self
    {
        $this->filters['relation'] = implode(',', [$block_id, $section_id]);
        return $this;
    }

    public function filterByValue(array $values, string $element_name = null): self
    {
        $this->filters['value'] = $element_name ?
            $element_name.'|'.implode(',', $values) :
            implode(',', $values);
        return $this;
    }

    public function filterByGeofence(float $latNE, float $latSW, float $lngNE, float $lngSW): self
    {
        $this->filters['geofence'] = implode(',', [$latNE, $latSW, $lngNE, $lngSW]);
        return $this;
    }

    public function originalMedia(): self
    {
        $this->media_type = 'original';
        return $this;
    }

    public function mediaType(string $type): self
    {
        $this->media_type = $type;
        return $this;
    }

    public function forceLocaleFallback(): self
    {
        $this->force_locale_fallback = true;
        return $this;
    }

    public function forceSlug(): self
    {
        $this->force_slug = true;
        return $this;
    }

    public function cache(int $cache_ttl = 0): self
    {
        $this->cache_ttl = $cache_ttl;
        return $this;
    }

    public function distance(float $latitude, float $longitude): self
    {
        $this->coordinates = [$latitude, $longitude];
        return $this;
    }

    /**
     * Retrieve the info about the project.
     *
     * @return mixed
     */
    public function getProject()
    {
        $query = [
            'locale' => $this->locale,
            'original_media' => $this->original_media,
            'force_locale_fallback' => $this->force_locale_fallback,
        ];

        if (! empty($this->includes)) {
            $query['include'] = implode(',', $this->include);
        }

        $url = '/project&'.http_build_query($query);

        return Cache::remember('mburger:project:'.$url, $this->cache_ttl, function () use ($url) {
            return $this->call($url);
        });
    }

    /**
     * Retrieve the blocks of the project.
     *
     * @return mixed
     */
    public function getBlocks()
    {
        $query = [
            'locale' => $this->locale,
            'original_media' => $this->original_media,
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
        ];

        if (! empty($this->includes)) {
            $query['include'] = implode(',', $this->include);
        }

        if (! empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                $query['filter['.$filter.']'] = $value;
            }
        }

        $url = '/blocks&'.http_build_query($query);

        return Cache::remember('mburger:blocks:'.$url, $this->cache_ttl, function () use ($url) {
            return $this->call($url);
        });
    }

    /**
     * Retrieve a block by id.
     *
     * @return mixed
     */
    public function getBlock($block_id)
    {
        $query = [
            'locale' => $this->locale,
            'original_media' => $this->original_media,
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
        ];

        if (! empty($this->includes)) {
            $query['include'] = implode(',', $this->include);
        }

        if (! empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                $query['filter['.$filter.']'] = $value;
            }
        }

        $url = '/blocks/'.$block_id.'&'.http_build_query($query);

        return Cache::remember('mburger:block:'.$block_id.':'.$url, $this->cache_ttl, function () use ($url) {
            return $this->call($url);
        });
    }

    /**
     * Retrieve the blocks of the project.
     *
     * @return mixed
     */
    public function getSections($block_id)
    {
        $query = [
            'locale' => $this->locale,
            'original_media' => $this->original_media,
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
        ];

        if (! empty($this->includes)) {
            $query['include'] = implode(',', $this->include);
        }

        if (! empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                $query['filter['.$filter.']'] = $value;
            }
        }

        if (! empty($this->coordinates)) {
            $query['distance'] = implode(',', $this->coordinates);
        }

        $url = '/blocks/'.$block_id.'/sections&'.http_build_query($query);

        return Cache::remember('mburger:sections:'.$url, $this->cache_ttl, function () use ($url) {
            return $this->call($url);
        });
    }

    /**
     * Retrieve a section by id or slug
     *
     * @return mixed
     */
    public function getSection($section_id_or_slug)
    {
        $query = [
            'locale' => $this->locale,
            'original_media' => $this->original_media,
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
            'use_slug' => $this->use_slug,
        ];

        if (! empty($this->includes)) {
            $query['include'] = implode(',', $this->include);
        }

        if (! empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                $query['filter['.$filter.']'] = $value;
            }
        }

        if (! empty($this->coordinates)) {
            $query['distance'] = implode(',', $this->coordinates);
        }

        $url = '/sections/'.$section_id_or_slug.'&'.http_build_query($query);

        return Cache::remember('mburger:section:'.$section_id_or_slug.':'.$url, $this->cache_ttl, function () use ($url) {
            return $this->call($url);
        });
    }

    /**
     * @param $path
     * @param  string  $method
     * @param  array  $data
     *
     * @return mixed
     */
    private function call($path, $method = 'GET', $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::URL.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-MBurger-Token:'.config('mburger.api_key'),
            'X-MBurger-Version: '.config('mburger.api_version'),
        ]);

        $response = curl_exec($ch);

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($response, true);

        curl_close($ch);

        Log::debug('--- MBurger API Call ---');
        Log::debug($status);
        Log::debug($response);

        if ($status < 300) {
            return $response;
        } elseif ($status < 400) {
            return $response;
        } elseif ($status < 500) {
            return $response;
        } else {
            return $response;
        }
    }

    /**
     * @param $blocks
     *
     * @return mixed
     */
    public static function transformBlocks($blocks)
    {
        return collect($blocks)->mapWithKeys(function ($block) {
            return [$block['url_title'] => collect($block['sections'])->mapWithKeys(function ($section) {
                return [$section['id'] => collect($section['elements'])->map(function ($element) {
                    if ($element['type'] == 'dropdown') {
                        foreach ($element['options'] as $opt) {
                            if ($opt['key'] == $element['value']) {
                                return $opt;
                            }
                        }
                    }
                    return $element['value'];
                })];
            })];
        });
    }

    /**
     * @param $block
     *
     * @return mixed
     */
    public static function transformBlock($block)
    {
        return collect($block['sections'])->mapWithKeys(function ($section) {
            return [$section['id'] => collect($section['elements'])->map(function ($element) {
                return $element['value'];
            })];
        });
    }
}
