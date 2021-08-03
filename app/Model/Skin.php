<?php

declare (strict_types=1);
namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id 
 * @property int $hero_id 
 * @property string $name 
 * @property string $avatar 
 * @property string $image 
 * @property string $cropped 
 * @property array $additional
 * @property int $price 
 * @property int $fragments 
 * @property string $deploy 
 * @property int $status 
 * @property int $admin_id
 * @property Carbon $expired_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read string $desc 
 * @property-read string $introduce 
 * @property-read string $origin
 * @property-read string $able_exchange
 * @property-read Hero $hero
 */
class Skin extends Model
{
    public const AdditionalOrigin = 'origin';
    public const AdditionalDescription = "desc";
    public const AdditionalIntroduce = "introduce";
    public const AdditionalAbleExchange = "able_exchange";  //是否可以兑换

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'skins';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'hero_id', 'name', 'avatar', 'image', 'additional', 'price', 'deploy', 'cropped','expired_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'hero_id' => 'integer', 'price' => 'integer', 'fragments' => 'integer', 'status' => 'integer', 'admin_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'additional' => 'json','expired_at' => 'datetime'];

    /**  */
    public function hero() : BelongsTo
    {
        return $this->belongsTo(Hero::class);
    }
}