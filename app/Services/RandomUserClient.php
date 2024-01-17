<?php
declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class RandomUserClient implements ClientInterface
{
    private const DEFAULT_RESULTS = '10';
    private const REQUEST_FORMAT = 'json';
    private string $url;
    private string $apiVersion;

    /**
     * Constructor.
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        if (empty(config('randomusers.url'))) {
            throw new InvalidArgumentException('Random User endpoint url is empty.');
        }

        if (empty(config('randomusers.version'))) {
            throw new InvalidArgumentException('Random User endpoint version is empty.');
        }

        $this->url = config('randomusers.url');
        $this->apiVersion = config('randomusers.version') ?
            config('randomusers.version') . '/' : null;
    }

    /**
     * @inheritDoc
     */
    public function query(array $params = []): Response|null|array|PromiseInterface
    {
        $url = $this->url . $this->apiVersion ?? '';

        if (!empty($params)) {
            $queryParams = $this->prepareQueryParams($params);
            $url .= '?' . Arr::query($queryParams);
        }

        return Http::accept('application/json')->get($url);
    }

    /**
     * Preparation query params for external request
     *
     * @param array $params
     * @return array
     */
    private function prepareQueryParams(array $params): array
    {
        $queryParams = [];
        $queryParams[self::PARAM_RESULTS] = $params['user_qty'] ?? self::DEFAULT_RESULTS;
        $queryParams[self::PARAM_FORMAT] = self::REQUEST_FORMAT;

        $fields = isset($params['fields']) ? implode(',', $params['fields']) : [];
        if (!empty($fields)) {
            $queryParams[self::PARAM_INC_FIELDS] = $fields;
        }

        return $queryParams;
    }
}
