<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure\Http;

use Illuminate\Foundation\Http\FormRequest;
use Src\Gifs\Domain\SearchCriteria;

final class SearchGifsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'max:'.SearchCriteria::MAX_QUERY_LENGTH],
            'limit' => ['nullable', 'integer', 'between:'.SearchCriteria::MIN_LIMIT.','.SearchCriteria::MAX_LIMIT],
            'offset' => ['nullable', 'integer', 'between:'.SearchCriteria::MIN_OFFSET.','.SearchCriteria::MAX_OFFSET],
        ];
    }
}
