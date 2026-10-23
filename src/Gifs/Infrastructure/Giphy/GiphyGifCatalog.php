<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure\Giphy;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Psr\Log\LoggerInterface;
use Src\Gifs\Domain\Gif;
use Src\Gifs\Domain\GifCatalog;
use Src\Gifs\Domain\GifCollection;
use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\GifNotFound;
use Src\Gifs\Domain\GifProviderUnavailable;
use Src\Gifs\Domain\SearchCriteria;
use Throwable;
use UnexpectedValueException;

final readonly class GiphyGifCatalog implements GifCatalog
{
    private const CONNECT_TIMEOUT_SECONDS = 3;
    private const RETRY_TIMES = 2;
    private const RETRY_SLEEP_MILLISECONDS = 200;

    public function __construct(
        private HttpFactory $http,
        private GiphyConfig $config,
        private GiphyGifMapper $mapper,
        private LoggerInterface $logger,
    ) {
    }

    public function search(SearchCriteria $criteria): GifCollection
    {
        $path = '/gifs/search';

        $response = $this->get($path, [
            'q' => $criteria->query(),
            'limit' => $criteria->limit(),
            'offset' => $criteria->offset(),
        ]);

        if ($response->failed()) {
            throw $this->unavailable($path, $response->status());
        }

        try {
            return $this->mapper->toCollection($response->json());
        } catch (UnexpectedValueException) {
            throw $this->unavailable($path, $response->status(), 'unexpected payload');
        }
    }

    public function find(GifId $id): Gif
    {
        $path = '/gifs/'.$id->value();

        $response = $this->get($path);

        if ($response->notFound() || $response->badRequest()) {
            throw new GifNotFound();
        }

        if ($response->failed()) {
            throw $this->unavailable($path, $response->status());
        }

        $data = $response->json('data');

        if ($data === null || $data === []) {
            throw new GifNotFound();
        }

        try {
            return $this->mapper->toGif($data);
        } catch (UnexpectedValueException) {
            throw $this->unavailable($path, $response->status(), 'unexpected payload');
        }
    }

    /**
     * @param array<string, int|string> $query
     */
    private function get(string $path, array $query = []): Response
    {
        try {
            return $this->http
                ->baseUrl($this->config->baseUrl)
                ->acceptJson()
                ->connectTimeout(self::CONNECT_TIMEOUT_SECONDS)
                ->timeout($this->config->timeout)
                ->retry(
                    self::RETRY_TIMES,
                    self::RETRY_SLEEP_MILLISECONDS,
                    fn (Throwable $e): bool => $e instanceof ConnectionException
                        || ($e instanceof RequestException && $e->response->serverError()),
                    throw: false,
                )
                ->get($path, ['api_key' => $this->config->apiKey, ...$query]);
        } catch (ConnectionException) {
            throw $this->unavailable($path, null, 'connection error');
        }
    }

    private function unavailable(string $path, ?int $status, string $reason = 'unsuccessful response'): GifProviderUnavailable
    {
        $this->logger->warning('GIPHY request failed.', [
            'path' => $path,
            'status' => $status,
            'reason' => $reason,
        ]);

        return new GifProviderUnavailable();
    }
}
