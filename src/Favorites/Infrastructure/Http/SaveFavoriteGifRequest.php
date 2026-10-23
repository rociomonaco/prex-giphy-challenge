<?php

declare(strict_types=1);

namespace Src\Favorites\Infrastructure\Http;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Src\Favorites\Domain\Alias;
use Src\Gifs\Domain\GifId;

final class SaveFavoriteGifRequest extends FormRequest
{
    public const FORBIDDEN_MESSAGE = 'You cannot save favorites for another user.';

    public function authorize(): bool
    {
        $userId = $this->input('user_id');

        if (! $this->filled('user_id') || ! is_numeric($userId)) {
            return true;
        }

        return (int) $userId === (int) $this->user()?->getAuthIdentifier();
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'gif_id' => ['required', 'string', 'alpha_num', 'max:'.GifId::MAX_LENGTH],
            'alias' => ['required', 'string', 'max:'.Alias::MAX_LENGTH],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException(self::FORBIDDEN_MESSAGE);
    }
}
