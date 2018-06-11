<?php

namespace Modules\DynamicMenu\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\DynamicMenu\Models\Menu;
use Modules\DynamicMenu\Models\Menus;
use Modules\DynamicMenu\Models\MenuItem;
use Illuminate\Support\Str;

class DynamicMenuController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $menus = Menu::all();       
        return view('dynamicmenu::index')->with('menus', $menus);
    }

    // To create new menu
    public function createnewmenu()
    {

        $menu = new Menus();
        $menu->name = request()->input("menuname");
        $menu->save();
        return json_encode(array("resp" => $menu->id));
    }

    // To delete menu
    public function deleteitemmenu()
    {
        $menuitem = MenuItem::find(request()->input("id"));

        if($menuitem->default_item == 1)
        {
            return json_encode(array("resp" => "You can't delete Default Menu Item", "error" => 1));
        }

        $menuitem_child = MenuItem::where('parent_id', $menuitem->id)->get();
        if($menuitem_child->count() != 0)
        {
            return json_encode(array("resp" => "You have to delete its child first!!!", "error" => 1));
        }
        else {
            $menuitem->delete();
            if (\Cache::has('neev.menu.' . $menuitem->menu->name . '.en'))
                \Cache::forget('neev.menu.' . $menuitem->menu->name . '.en');
        }
    }

    // To delete menu item
    public function deletemenug()
    {
        $menus = new MenuItem();
        $getall = $menus->getall(request()->input("id"));
        if (count($getall) == 0) {
            $menudelete = Menus::find(request()->input("id"));
            $menudelete->delete();

            if (\Cache::has('neev.menu.' . $menudelete->name . '.en'))
                \Cache::forget('neev.menu.' . $menudelete->name . '.en');

            return json_encode(array("resp" => "You are deleting this item."));
        } else {
            return json_encode(array("resp" => "You have to delete all items first", "error" => 1));

        }
    }

    // To update menu item
    public function updateitem()
    {
        $arraydata = request()->input("arraydata");
        if (is_array($arraydata)) {
            foreach ($arraydata as $value) {
                $menuitem = MenuItem::find($value['id']);
                $menuitem->title = $value['label'];
                $menuitem->target = $value['link'];
                $menuitem->before_html = $value['class'];
                $menuitem->save();
            }
        } else {
            $menuitem = MenuItem::find(request()->input("id"));
            $menuitem->title = request()->input("label");
            $menuitem->target = request()->input("url");
            $menuitem->before_html = request()->input("clases");
            $menuitem->save();
        }
        if (\Cache::has('neev.menu.' . $menuitem->menu->name . '.en'))
            \Cache::forget('neev.menu.' . $menuitem->menu->name . '.en');
    }

    public function addcustommenu()
    {
        $menuitem = new MenuItem;
        $menuitem->title = request()->input("labelmenu");
        $menuitem->target = request()->input("linkmenu");
        $menuitem->nickname = Str::studly(request()->input("labelmenu"));
        $menuitem->menu_id = request()->input("idmenu");
        $menuitem->sort_order = MenuItem::getNextSortRoot(request()->input("idmenu"));
        $menuitem->save();

        if (\Cache::has('neev.menu.' . $menuitem->menu->name . '.en'))
            \Cache::forget('neev.menu.' . $menuitem->menu->name . '.en');

    }

    public function generatemenucontrol()
    {
        $menu = Menus::find(request()->input("idmenu"));
        $menu->name = request()->input("menuname");
        $menu->save();
        if (is_array(request()->input("arraydata"))) {
            foreach (request()->input("arraydata") as $value) {

                $menuitem = MenuItem::find($value["id"]);
                $menuitem->parent_id = $value["parent"];
                $menuitem->sort_order = $value["sort"];
                $menuitem->depth = $value["depth"];
                $menuitem->save();
            }
        }
        if (\Cache::has('neev.menu.' . $menuitem->menu->name . '.en'))
            \Cache::forget('neev.menu.' . $menuitem->menu->name . '.en');
        echo json_encode(array("resp" => 1));

    }
}
