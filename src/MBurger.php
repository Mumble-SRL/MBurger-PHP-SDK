<?php


namespace Mumble\MBurger;

use Illuminate\Support\Facades\Cache;

class MBurger
{
    public static function headers()
    {
        return [
            'Accept:application/json',
            'X-MBurger-Token:' . config('mburger.api_key'),
            'X-MBurger-Version:3',
        ];
    }

    private static function ApiCall($url, $method = 'GET', $data = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Self::headers() ?? []);

        $curl_result = curl_exec($ch);
        
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($status != 200) {
            info('-- MBurger API Call ---');
            info($status);
            info($curl_result);
        }

        $response = json_decode($curl_result, true);

        curl_close($ch);

        return $response;
    }

    public static function getBlocks(array $block_ids, $original_media = false, $params = [], $filters = [], $order_asc = 1, $cache_seconds = 0)
    {
        $url = 'https://mburger.cloud/api/blocks';
        $query = [
            'original_media' => $original_media,
            'include' => 'sections.elements',
            'locale' => app()->getLocale(),
            'force_locale_fallback' => true,
            'sort' => 'order'
        ];
        if (!$order_asc) {
            $query['sort'] = '-order';
        }
        $query = http_build_query($query);

        $url = $url . '?filter[id]=' . implode(",", $block_ids) . '&' . $query;

        return Cache::remember('MBurger-getBlocks-' . $url, $cache_seconds, function () use ($url) {
            $response = Self::ApiCall($url);
            if (isset($response['body']['items'])) {
                return collect($response['body']['items'])->mapWithKeys(function ($block) {
                    return [$block['url_title'] => collect($block['sections'])
                        ->sortBy('order')
                        ->mapWithKeys(function ($section) {
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
            } else {
                throw new \Exception('MBurger Exception: ' . json_encode($response));
            }
        });
    }

    public static function getBlock($block_id, $original_media = 0, $params = [], $filters = [], $order_asc = 1, $cache_seconds = 0)
    {
        $url = 'https://mburger.cloud/api/blocks/' . $block_id . '/sections';
        $query = [
            'include' => 'elements',
            'sort' => 'order',
            'force_locale_fallback' => true,
            'locale' => app()->getLocale()
        ];

        $query = array_merge($query, $params);

        if ($original_media) {
            $query['original_media'] = true;
        }
        if (!$order_asc) {
            $query['sort'] = '-order';
        }
        $query = http_build_query($query);

        $url .= '?' . $query;

        $filters = collect($filters)->map(function ($item, $key) {
            return implode(',', $item);
        });

        $filters_string = '';
        foreach ($filters as $key => $value) {
            $filters_string .= "filter[$key]=$value";
        }
        $url .= "&" . rtrim($filters_string);

        return Cache::remember('MBurger-getBlock-' . $url, $cache_seconds, function () use ($url) {
            $response = Self::ApiCall($url);

            if (isset($response['body']['items'])) {
                return collect($response['body']['items'])->mapWithKeys(function ($section) {
                    return [$section['id'] => collect($section['elements'])->map(function ($element) {
                        return $element['value'];
                    })];
                });
            } else {
                throw new \Exception('MBurger Exception: ' . json_encode($response));
            }
        });

    }

    public static function getSection($secton_id, $original_media = 0, $cache_seconds = 0, $use_slug = 0)
    {
        $url = 'https://mburger.cloud/api/sections/' . $secton_id . '/elements';

        $query = http_build_query([
            'original_media' => $original_media,
            'locale' => app()->getLocale(),
            'force_locale_fallback' => true,
            'use_slug' => $use_slug
        ]);

        $url = $url . '?' . $query;

        return Cache::remember('MBurger-getSection-' . $url, $cache_seconds, function () use ($url) {
            $response = Self::ApiCall($url);
            if (isset($response['body']['items'])) {
                return collect($response['body']['items'])->mapWithKeys(function ($element) {
                    return [$element['name'] => $element['value']];
                });
            } else {
                throw new \Exception('MBurger Exception: ' . json_encode($response));
            }
        });
    }
}
