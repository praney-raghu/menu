<?php

namespace Modules\DynamicMenu\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
    
    public function menuitems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
