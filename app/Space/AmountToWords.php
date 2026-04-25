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
                $key = $provider->key;
                $host = $provider->host;
            } else {
                $config = config('services.rapidapi.number2words');
                $key = $config['key'] ?? null;
                $host = $config['host'] ?? 'number2words4.p.rapidapi.com';
            }

            if (empty($key)) {
                return (string) $normalizedAmount;
            }

            // The API requires a dialect that matches the locale.
            // e.g. 'en' → 'GB', 'fr' → 'FR', 'de' → 'DE', etc.
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

            // The API returns plain text (not JSON), e.g.:
            // "twelve million three hundred… US dollars and ninety cents"
            $response = Http::timeout(10)
                ->withHeaders([
                    'x-rapidapi-host' => $host,
                    'x-rapidapi-key' => $key,
                    'Content-Type' => 'application/json',
                ])
                ->get("https://{$host}/api", [
                    'value' => $normalizedAmount,
                    'languageCode' => $localeBase,
                    'currencyCode' => $currencyCode === 'MAD' ? 'EUR' : $currencyCode,
                    'dialect' => $dialect,
                    'formatStyle' => 'standard',
                ]);

            if (! $response->successful()) {
                Log::error('AmountToWords API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return (string) $normalizedAmount;
            }

            // Response body is a JSON-encoded plain string: "\"twelve million…\""
            // Strip surrounding quotes if the API wraps the text in JSON string encoding.
            $raw = trim($response->body());
            $result = json_decode($raw, true) ?? $raw;

            if (! is_string($result) || empty($result)) {
                Log::error('AmountToWords unexpected response', ['body' => $raw]);

                return (string) $normalizedAmount;
            }

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
        } catch (\Exception $e) {
            Log::error('AmountToWords exception: '.$e->getMessage());

            return (string) $normalizedAmount;
        }
    }

    /**
     * Test the API connection with provided credentials.
     */
    public static function test(string $key, string $host, string $driver): array
    {
        if ($driver !== 'number2words') {
            return ['success' => false, 'message' => 'Driver not supported for testing.'];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'x-rapidapi-host' => $host,
                    'x-rapidapi-key' => $key,
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

                return [
                    'success' => true,
                    'message' => 'Connection successful!',
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => 'API returned an error: '.$response->status(),
                'error' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
            ];
        }
    }
}
