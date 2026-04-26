<?php

namespace App\Space;

use App\Models\ApiProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AmountToWords
{
    /**
     * Convert an amount to words using RapidAPI Number2Words.
     *
     * Caches successful results forever to minimise API calls.
     *
     * @param  float|int  $amount  Already divided by 100 (i.e. decimal value)
     * @param  string  $locale  languageCode accepted by the API (e.g. 'en', 'fr')
     */
    public static function convert(float|int $amount, string $currencyCode = 'EUR', string $locale = 'fr', ?int $companyId = null): string
    {
        // Round to 2 decimals to keep the cache key stable
        $normalizedAmount = round((float) $amount, 2);
        $cacheKey = 'amount_to_words_'.md5("{$normalizedAmount}_{$currencyCode}_{$locale}");

        // Return immediately if already cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Prefer per-company API provider from DB; fall back to .env / services.php
            $providerQuery = ApiProvider::query()
                ->where('driver', 'number2words')
                ->where('active', true);

            if ($companyId) {
                $providerQuery->where('company_id', $companyId);
            } else {
                $providerQuery->whereCompany();
            }

            $provider = $providerQuery->first();

            if ($provider) {
                $primaryKey = $provider->key;
                $backupKeys = $provider->config['keys'] ?? [];
                $host = $provider->host;
            } else {
                $config = config('services.rapidapi.number2words');
                $primaryKey = $config['key'] ?? null;
                $backupKeys = [];
                $host = $config['host'] ?? 'number2words4.p.rapidapi.com';
            }

            $allKeys = array_filter(array_unique(array_merge([$primaryKey], $backupKeys)));

            if (empty($allKeys)) {
                return (string) $normalizedAmount;
            }

            // Shuffle keys for random rotation
            shuffle($allKeys);

            // The API requires a dialect that matches the locale.
            $dialectMap = [
                'en' => 'GB',
                'fr' => 'FR',
                'de' => 'DE',
                'es' => 'ES',
                'ar' => 'AR',
                'nl' => 'NL',
                'pt' => 'PT',
                'ru' => 'RU',
                'tr' => 'TR',
                'pl' => 'PL',
                'uk' => 'UK',
            ];
            $localeBase = strtolower(explode('_', $locale)[0]);
            $dialect = $dialectMap[$localeBase] ?? 'GB';

            $lastResult = (string) $normalizedAmount;

            foreach ($allKeys as $currentKey) {
                try {
                    $response = Http::timeout(10)
                        ->withHeaders([
                            'x-rapidapi-host' => $host,
                            'x-rapidapi-key' => $currentKey,
                            'Content-Type' => 'application/json',
                        ])
                        ->get("https://{$host}/api", [
                            'value' => $normalizedAmount,
                            'languageCode' => $localeBase,
                            'currencyCode' => $currencyCode === 'MAD' ? 'EUR' : $currencyCode,
                            'dialect' => $dialect,
                            'formatStyle' => 'standard',
                        ]);

                    if ($response->successful()) {
                        $raw = trim($response->body());
                        $result = json_decode($raw, true) ?? $raw;

                        if (is_string($result) && ! empty($result)) {
                            // Patch currency wording for MAD
                            if ($currencyCode === 'MAD') {
                                $result = str_replace(
                                    ['euros', 'euro', 'centimes', 'centime'],
                                    ['dirhams', 'dirham', 'centimes', 'centime'],
                                    $result
                                );
                            }

                            // Cache forever — only on success
                            Cache::forever($cacheKey, $result);

                            return $result;
                        }
                    }

                    // If we get a 429 (Too Many Requests), we continue to the next key
                    if ($response->status() === 429) {
                        Log::warning('AmountToWords: Key quota reached (429). Rotating to next key.', ['key' => substr($currentKey, 0, 8).'...']);

                        continue;
                    }

                    // For other errors (401, 500, etc.), we log and stop according to user request
                    Log::error('AmountToWords API error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    return (string) $normalizedAmount;
                } catch (\Exception $e) {
                    Log::error('AmountToWords exception during rotation: '.$e->getMessage());

                    // On exception (timeout, etc.), we could also try next key, but let's stick to the 429 rule
                    return (string) $normalizedAmount;
                }
            }

            return $lastResult;
        } catch (\Exception $e) {
            Log::error('AmountToWords general exception: '.$e->getMessage());

            return (string) $normalizedAmount;
        }
    }

    /**
     * Test the API connection with provided credentials.
     * Supports testing rotation if an array of keys is provided.
     */
    public static function test(string|array $keys, string $host, string $driver): array
    {
        if ($driver !== 'number2words') {
            return ['success' => false, 'message' => 'Driver not supported for testing.'];
        }

        $allKeys = is_array($keys) ? $keys : [$keys];
        $allKeys = array_filter(array_unique($allKeys));

        if (empty($allKeys)) {
            return ['success' => false, 'message' => 'No API keys provided for testing.'];
        }

        $results = [];
        $anySuccess = false;

        foreach ($allKeys as $currentKey) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'x-rapidapi-host' => $host,
                        'x-rapidapi-key' => $currentKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->get("https://{$host}/api", [
                        'value' => 123.45,
                        'languageCode' => 'en',
                        'currencyCode' => 'USD',
                        'dialect' => 'GB',
                        'formatStyle' => 'standard',
                    ]);

                if ($response->successful()) {
                    $raw = trim($response->body());
                    $result = json_decode($raw, true) ?? $raw;
                    $results[] = [
                        'key' => substr($currentKey, 0, 8).'...',
                        'status' => 'Success',
                        'data' => $result,
                    ];
                    $anySuccess = true;
                } else {
                    $results[] = [
                        'key' => substr($currentKey, 0, 8).'...',
                        'status' => 'Error: '.$response->status(),
                        'body' => $response->body(),
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'key' => substr($currentKey, 0, 8).'...',
                    'status' => 'Exception',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => $anySuccess,
            'message' => $anySuccess ? 'Rotation test completed. At least one key is working.' : 'All keys failed.',
            'details' => $results,
        ];
    }
}
