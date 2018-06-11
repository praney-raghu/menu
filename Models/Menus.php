<?php

namespace Modules\DynamicMenu\Models;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
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

    public static function byName($name)
    {
        return self::where('name', '=', $name)->first();
    }

    public static function render()
    {
        $menu = new Menus();
        $menuitems = new MenuItem();
        $menulist = $menu->all();
        $menulist = $menulist->pluck('name', 'id')->prepend('Select menu', 0)->all();
        // dd($menulist);
        if ((request()->has("action") && empty(request()->input("menu"))) || request()->input("menu") == '0') {
            return view('dynamicmenu::menu-html')->with("menulist", $menulist);
        } else {

            $menu = Menus::find(request()->input("menu"));
            $menus = $menuitems->getall(request()->input("menu"));

            $data = ['menus' => $menus, 'indmenu' => $menu, 'menulist' => $menulist];
            return view('dynamicmenu::menu-html', $data);
        }

    }

    public static function scripts()
    {
        return view('dynamicmenu::scripts');
    }

    public static function select($name = "menu", $menulist = array())
    { 
        $html = '<select name="' . $name . '">';

        foreach ($menulist as $key => $val) {
            $active = '';
            if (request()->input('menu') == $key) {
                $active = 'selected="selected"';
            }
            $html .= '<option ' . $active . ' value="' . $key . '">' . $val . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public static function getByName($name)
    {
        $menu_id = Menus::byName($name)->id;
        return self::get($menu_id);
    }

    public static function get($menu_id)
    {
        $menuItem = new MenuItem;
        $menu_list = $menuItem->getall($menu_id);

        $roots = $menu_list->where('menu_id', (integer)$menu_id)->where('parent_id', 0);

        $items = self::tree($roots, $menu_list);
        return $items;
    }

    private static function tree($items, $all_items)
    {
        $data_arr = array();
        $i = 0;
        foreach ($items as $item) {
            $data_arr[$i] = $item->toArray();
            $find = $all_items->where('parent_id', $item->id);

            $data_arr[$i]['child'] = array();

            if ($find->count()) {
                $data_arr[$i]['child'] = self::tree($find, $all_items);
            }

            $i++;
        }

        return $data_arr;
    }
}