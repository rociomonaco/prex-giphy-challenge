<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure\Http;

use Illuminate\Http\JsonResponse;
use Src\Gifs\Application\SearchGifs\SearchGifsHandler;
use Src\Gifs\Application\SearchGifs\SearchGifsQuery;

final readonly class SearchGifsController
{
    public function __construct(private SearchGifsHandler $handler)
    {
    }

    public function __invoke(SearchGifsRequest $request): JsonResponse
    {
        $result = $this->handler->handle(new SearchGifsQuery(
            query: $request->string('query')->toString(),
            limit: $request->filled('limit') ? $request->integer('limit') : null,
            offset: $request->filled('offset') ? $request->integer('offset') : null,
        ));

        return new JsonResponse([
            'data' => array_map(GifResource::toArray(...), $result->gifs),
            'pagination' => [
                'total' => $result->pagination->total,
                'count' => $result->pagination->count,
                'offset' => $result->pagination->offset,
            ],
        ]);
    }
}
