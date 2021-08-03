<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Model;

/**
 * @property int $id 
 * @property string $name 
 * @property string $icon 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class HeroRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hero_role';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}