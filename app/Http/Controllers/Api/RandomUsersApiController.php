<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RandomUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\ClientInterface;
use App\Services\RandomUserClientHelper;
use Spatie\ArrayToXml\ArrayToXml;

class RandomUsersApiController extends Controller
{

    /**
     * @param ClientInterface $client
     * @param RandomUserClientHelper $clientHelper
     */
    public function __construct(
        private readonly ClientInterface        $client,
        private readonly RandomUserClientHelper $clientHelper
    )
    {
    }

    /**
     * Make request to Random Users API and return response
     *
     * @param RandomUserRequest $request
     * @return JsonResponse|Response
     */
    public function execute(RandomUserRequest $request): JsonResponse|Response
    {
        $params = $request->validated();
        $sortOrder = $params['sort_order'] ?? 'asc';
        $sortBy = $params['sort_by'] ?? 'last';
        $format = $params['format'] ?? 'json';

        $response = $this->client->query($params);

        $results = $response['results'] ?? null;
        if ($results) {
            $results = $this->clientHelper->processUsers($results, $sortBy, $sortOrder);
        }
        if ($format === 'json') {
            return response()->json($results);
        } else {
            $xml = ArrayToXml::convert(['__numeric' => $results]);
            return response($xml)->header('Content-Type', 'application/xml');
        }
    }
}
