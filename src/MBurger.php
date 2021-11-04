<?php

namespace Mumble\MBurger;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Mumble\MBurger\Exceptions\MBurgerValidationException;
use Mumble\MBurger\Exceptions\MBurgerInvalidRequestException;
use Mumble\MBurger\Exceptions\MBurgerNotFoundException;
use Mumble\MBurger\Exceptions\MBurgerServerErrorException;
use Mumble\MBurger\Exceptions\MBurgerThrottlingException;
use Mumble\MBurger\Exceptions\MBurgerUnauthenticatedException;
use Mumble\MBurger\Exceptions\MBurgerUnauthorizedException;

class MBurger
{
    const URL = 'https://mburger.cloud/api';

    protected $debug = false;

    protected $token = null;

    protected $api_version = null;

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

    public function __construct($token = null, $api_version = null)
    {
        $this->token = $token;
        $this->api_version = $api_version;
    }

    // Modifiers
    public function debug(): self
    {
        $this->debug = true;
        return $this;
    }

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
        if (in_array('blocks', $this->include)) {
            $this->include = array_merge(array_diff($this->include, ['blocks']), ['blocks.sections']);
        } else {
            $this->include[] = 'sections';
        }

        return $this;
    }

    public function includeElements(): self
    {
        if (in_array('blocks.sections', $this->include)) {
            $this->include = array_merge(array_diff($this->include, ['blocks.sections']), ['blocks.sections.elements']);
        } elseif (in_array('sections', $this->include)) {
            $this->include = array_merge(array_diff($this->include, ['sections']), ['sections.elements']);
        } else {
            $this->include[] = 'elements';
        }

        return $this;
    }

    public function includeStructure(): self
    {
        if (in_array('blocks', $this->include)) {
            $this->include = array_merge(array_diff($this->include, ['blocks']), ['blocks.structure']);
        } else {
            $this->include[] = 'structure';
        }

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

    public function filterByTitle(string $title): self
    {
        $this->filters['title'] = $title;
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
        if (! in_array('elements', $this->include)) {
            $this->include[] = 'elements';
        }

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
            'original_media' => $this->media_type == 'original',
            'force_locale_fallback' => $this->force_locale_fallback,
        ];

        if (! empty($this->include)) {
            $query['include'] = implode(',', $this->include);
        }

        $url = '/project?'.http_build_query($query);

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
            'skip' => $this->skip,
            'take' => $this->take,
            'original_media' => $this->media_type == 'original',
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
        ];

        if (! empty($this->include)) {
            $query['include'] = implode(',', $this->include);
        }

        if (! empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                $query['filter['.$filter.']'] = $value;
            }
        }

        $url = '/blocks?'.http_build_query($query);

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
            'original_media' => $this->media_type == 'original',
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
        ];

        if (! empty($this->include)) {
            $query['include'] = implode(',', $this->include);
        }

        if (! empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                $query['filter['.$filter.']'] = $value;
            }
        }

        $url = '/blocks/'.$block_id.'?'.http_build_query($query);

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
            'skip' => $this->skip,
            'take' => $this->take,
            'original_media' => $this->media_type == 'original',
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
        ];

        if (! empty($this->include)) {
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

        $url = '/blocks/'.$block_id.'/sections?'.http_build_query($query);

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
            'original_media' => $this->media_type == 'original',
            'force_locale_fallback' => $this->force_locale_fallback,
            'sort' => $this->sort,
            'use_slug' => $this->force_slug,
        ];

        if (! empty($this->include)) {
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

        $url = '/sections/'.$section_id_or_slug.'?'.http_build_query($query);

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

        $this->token = $this->token ? : config('mburger.api_key');
        $this->api_version = $this->api_version ? : config('mburger.api_version');

        curl_setopt($ch, CURLOPT_URL, self::URL.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-MBurger-Token: '.$this->token,
            'X-MBurger-Version: '.$this->api_version,
        ]);

        $response = curl_exec($ch);

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($response, true);

        curl_close($ch);

        if ($this->debug) {
            Log::debug('--- MBurger API Call ---');
            Log::debug($status);
            Log::debug($response);
        }

        if ($status < 300) {
            return $response;
        } elseif ($status == 401) {
            throw MBurgerUnauthenticatedException::create($response['message'], $status);
        } elseif ($status == 403) {
            throw MBurgerUnauthorizedException::create($response['message'], $status);
        } elseif ($status == 404) {
            throw MBurgerNotFoundException::create($response['message'], $status);
        } elseif ($status == 422) {
            throw MBurgerValidationException::create($response['message'], $status);
        } elseif ($status == 429) {
            throw MBurgerThrottlingException::create($response['message'], $status);
        } elseif ($status < 500) {
            throw MBurgerInvalidRequestException::create($response['message'], $status);
        } else {
            throw MBurgerServerErrorException::create($response['message'], $status);
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
