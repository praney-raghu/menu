<?php

namespace Modules\DynamicMenu\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $table = 'menu_item';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menu_id', 'title', 'parent_id', 'nickname', 'before_html', 'after_html', 'target', 'sort_order', 'depth'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getsons($id)
    {
        return $this->where("parent_id", $id)->get();
    }
    public function getall($id)
    {
        return $this->where("menu_id", $id)->orderBy("sort_order", "asc")->get();
    }

    public static function getNextSortRoot($menu)
    {
        $max_depth = self::where('menu_id', $menu)->max('depth');
        return self::where('menu_id', $menu)->max('sort_order') + 1;
    }
}
