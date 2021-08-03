<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\HasMany;

/**
 * @property int $id
 * @property int $hero_role_id
 * @property string $name
 * @property string $avatar
 * @property string $image
 * @property string $story
 * @property string $history
 * @property array $additional
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Hyperf\Database\Model\Collection|\App\Model\Skin[] $skins
 */
class Hero extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'heroes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','hero_role_id', 'name', 'avatar', 'image', 'story', 'history'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'hero_role_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'additional' => 'array'];
    /** @var string 爬虫源数据 */
    public const AdditionalOrigin = 'origin';

    public function skins(): HasMany
    {
        return $this->hasMany(Skin::class);
    }
}