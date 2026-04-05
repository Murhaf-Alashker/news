<?php

namespace App\Models;

use App\Models\Scopes\ActivePostScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'commentable',
        'user_id',
        'category_id',
        'ulid',
        'views',
        'likes',
        'dislikes',
        'is_featured'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope( new  ActivePostScope());
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function interacts(): HasMany
    {
        return $this->hasMany(Interact::class);
    }

    public function scopeActivePosts($q)
    {
        return $q->where('status', 'approved');
    }
}

