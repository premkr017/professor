<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'type',
        'icon',
        'color',
        'status',
        'is_system',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['path'];

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the user that owns the category.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent category (self-referencing).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories (self-referencing).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the sub-categories recursively (for eager loading trees).
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Determine whether this category has a parent.
     */
    public function isChild(): bool
    {
        return ! is_null($this->parent_id);
    }

    /**
     * Get the display path e.g. "Food → Grocery".
     */
    public function getPathAttribute(): string
    {
        $parts = [$this->name];

        $current = $this->parent_id ? $this->parent : null;
        $guard = 0;

        while ($current && $guard < 10) {
            array_unshift($parts, $current->name);
            $current = $current->parent_id ? $current->parent : null;
            $guard++;
        }

        return implode(' → ', $parts);
    }

    /**
     * Build a flattened list of only parent categories (excluding given id and its descendants).
     */
    public static function getParentOptions($excludeId = null)
    {
        $query = Category::where('user_id', auth()->id())
            ->whereNull('parent_id')
            ->with('childrenRecursive');

        $categories = $query->get();

        if ($excludeId !== null) {
            $categories = $categories->reject(function ($cat) use ($excludeId) {
                return self::isSelfOrDescendant($cat, $excludeId);
            });
        }

        return $categories;
    }

    /**
     * Check if a category equals the target id or has it among descendants.
     */
    private static function isSelfOrDescendant(Category $category, int $targetId): bool
    {
        if ((int) $category->id === $targetId) {
            return true;
        }

        foreach ($category->childrenRecursive ?? collect() as $child) {
            if (self::isSelfOrDescendant($child, $targetId)) {
                return true;
            }
        }

        return false;
    }
}

