<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'category_id',
        'volume_code',
        'title',
        'author',
        'publication_year',
        'synopsis',
        'curator_note',
        'epub_path',
        'stock',
        'total_copies',
    ];

    /**
     * @return BelongsTo<Category, Book>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<BorrowingDetail, Book>
     */
    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }
}
