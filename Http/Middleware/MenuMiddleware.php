<?php

namespace Modules\DynamicMenu\Http\Middleware;

use Closure;
use Modules\DynamicMenu\Models\MenuItem;

class MenuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Adding 'Dynamic Menu' item to Admin Menu
        \Menu::make('AdminMenu', function ($menu) {
            $menu->add('Dynamic Menu', ['url' => 'dynamicmenu'])
                ->prepend('<i class="fa fa-wrench"></i><span class="title">')
                ->append('</span>');
        });
        
        //\Cache::flush();

        $menus = \Modules\DynamicMenu\Models\Menu::all();
        
        foreach($menus as $menu)
        {            
            if($menu->menuitems->count() === 0)
            continue;
            $name = $menu->name;
            $result = \Cache::has('neev.menu.'.$name.'.en');
            
            if($result)
            {
                $name = \Cache::get('neev.menu.' . $name . '.en');
                \View::share($menu->name, $name);
            }
            else
            {
                // $items = \Modules\DynamicMenu\Models\MenuItem::tree();
                $items = \Modules\DynamicMenu\Models\Menus::get($menu->id);
                
                foreach ($items as $item) 
                {
                    $menuname = \Menu::make($menu->name, function ($menu) use ($item) {
                        $menu->add($item['title'], ['url' => $item['target']])->nickname($item['nickname'])
                            ->prepend('<i class="' . $item['before_html'] . '"></i><span class="title">')
                            ->append('</span>');
                    });

                    if($item['child'] !== null)
                    $this->getChild($item, $menu->name);

                    \Cache::forever('neev.menu.' . $menu->name . '.en', $menuname);
                    \View::share($menu->name, $menuname);

                }
            }
        }
        
        return $next($request);
    }
    
    public function getChild($item, $name)
    {
        if ($item['child'] != null) 
        {
            foreach($item['child'] as $child)
            {
                $menuname = \Menu::make($name, function ($menu) use ($child, $item) {
                    if ($menu->item($item['nickname']) != null) 
                    {                   
                        $menu->item($item['nickname'])->add($child['title'], ['url' => $child['target']])->nickname($child['nickname'])
                            ->prepend('<i class="' . $child['before_html'] . '"></i><span class="title">')
                            ->append('</span>');
                    }                  
                });

                if($child['child'] != null)
                {
                    $this->getChild($child, $name);
                }                
            }
        }
    }
}
