<?php

namespace App\Models;

use App\Models\Scopes\ActivePostScope;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * @property int $id
 * @property string $ulid
 * @property int $views
 * @property int $likes
 * @property int $dislikes
 * @property bool $is_featured
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property bool $commentable
 * @property int $user_id
 * @property int $category_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */

/**
 * @property-read User $user
 * @property-read Category $category
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
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
    /** @noinspection PhpUnused */
    public function scopeActivePosts($q)
    {
        return $q->where('status', 'approved');
    }
    /** @noinspection PhpUnused */
    public function scopeLoadActiveCommentsCount($q)
    {
        return $q->withCount(['comments' => fn($query) => $query->where('status',1)]);
    }
    /** @noinspection PhpUnused */
    public function scopeOrderByPopularCommentsCount($q)
    {
        return $q->withCount
            ([
                'comments as popular_comments_count' =>
                    function($q)
                    {
                        return $q
                            ->where('created_at', '>=' , Carbon::now()->subDays(10))
                            ->where('status',1);
                    }
            ])
            ->orderBy('popular_comments_count', 'desc');
    }
}

