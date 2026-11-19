<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingDetail extends Model
{
    protected $fillable = [
        'borrowing_id',
        'book_id',
        'quantity',
    ];

    /**
     * @return BelongsTo<Borrowing, BorrowingDetail>
     */
    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    /**
     * @return BelongsTo<Book, BorrowingDetail>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
