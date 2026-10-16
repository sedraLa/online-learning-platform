<?php

namespace App\Models;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Scopes\PublishedScope;
use App\Traits\HasCategories;
use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Course extends Model
{
    use HasFactory, HasComments, HasCategories;

    protected $fillable = [
        'title',
        'description',
        'status',
        'level',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'status'  => CourseStatus::class,
            'level'   => CourseLevel::class,
            'is_free' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new PublishedScope());
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return static::withoutGlobalScope(PublishedScope::class)
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->first();
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_free', true);
    }

    public function scopeByLevel(Builder $query, CourseLevel $level): Builder
    {
        return $query->where('level', $level->value);
    }

    public function scopeForTeacher(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('enrolled_at');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
