<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    protected $fillable = [
        'transaction_code',
        'user_id',
        'member_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, Borrowing>
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Member, Borrowing>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return HasMany<BorrowingDetail, Borrowing>
     */
    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    /**
     * @return BelongsToMany<Book, Borrowing>
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'borrowing_details')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'borrowed' && $this->due_date->isPast();
    }
}
