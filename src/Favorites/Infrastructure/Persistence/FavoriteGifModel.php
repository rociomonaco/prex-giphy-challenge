<?php

declare(strict_types=1);

namespace Src\Favorites\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'gif_id', 'alias'])]
final class FavoriteGifModel extends Model
{
    protected $table = 'favorite_gifs';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }
}
