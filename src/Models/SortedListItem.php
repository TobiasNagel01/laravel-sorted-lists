<?php

namespace Mintellity\LaravelSortedLists\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SortedListItem extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    protected $fillable = [
        'list_key',
        'sequence',
        'name',
    ];

    /**
     * Snake case the primary key name.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        //Replace primaryKeyName = (classname)_id
        $this->primaryKey = Str::snake(class_basename(static::class)) . '_id';
    }

    /**
     * All items in this list.
     *
     * @return HasMany
     */
    public function siblings(): HasMany
    {
        return $this->hasMany(static::class, 'sorted_list_item_id', 'sorted_list_item_id')
            ->where('sorted_list_name', $this->list_key);
    }

    /**
     * The next item in this list.
     *
     * @return HasOne
     */
    public function next(): HasOne
    {
        return new HasOne(
            $this->siblings()
                ->firstWhere('sequence', '>', $this->position)
                ->getQuery(),
            $this,
            $this->getKeyName(),
            $this->getKeyName());
    }

    /**
     * The previous item in this list.
     *
     * @return HasOne
     */
    public function previous(): HasOne
    {

        return new HasOne(
            $this->siblings()
                ->firstWhere('sequence', '>', $this->position)
                ->getQuery(),
            $this,
            $this->getKeyName(),
            $this->getKeyName());
    }
}