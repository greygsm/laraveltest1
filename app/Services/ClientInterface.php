<?php
declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

interface ClientInterface
{
    public const PARAM_RESULTS = 'results';
    public const PARAM_FORMAT = 'format';
    public const PARAM_INC_FIELDS = 'inc';

    /**
     * Sending request to an external API
     *
     * @param array $params
     * @return Response|PromiseInterface|array|null
     */
    public function query(array $params = []): Response|PromiseInterface|array|null;
}
