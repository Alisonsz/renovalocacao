<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    public function index(): Response
    {
        $all = SiteSetting::orderBy("group")->orderBy("label")->get();
        $grouped = $all->groupBy("group")->map(fn($group) => $group->map(fn($s) => [
            "id"    => $s->id,
            "key"   => $s->key,
            "value" => $s->value,
            "type"  => $s->type,
            "label" => $s->label,
            "group" => $s->group,
        ]));

        return Inertia::render("Admin/Settings/Index", ["settings" => $grouped]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $request->validate(["settings" => "required|array"])["settings"];

        foreach ($settings as $key => $value) {
            SiteSetting::where("key", $key)->update(["value" => $value]);
        }

        return redirect()->route("admin.settings.index")->with("success", "Configurações salvas com sucesso!");
    }
}