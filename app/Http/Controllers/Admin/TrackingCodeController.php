<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrackingCodeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("Admin/TrackingCodes/Index", [
            "codes" => TrackingCode::latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("Admin/TrackingCodes/Form");
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "name"      => "required|string|max:255",
            "code"      => "required|string",
            "position"  => "required|in:head,body_start,body_end",
            "is_active" => "boolean",
            "pages"     => "nullable|array",
        ]);

        TrackingCode::create($data);

        return redirect()->route("admin.tracking-codes.index")->with("success", "Código de rastreamento criado!");
    }

    public function edit(TrackingCode $trackingCode): Response
    {
        return Inertia::render("Admin/TrackingCodes/Form", ["trackingCode" => $trackingCode]);
    }

    public function update(Request $request, TrackingCode $trackingCode): RedirectResponse
    {
        $data = $request->validate([
            "name"      => "required|string|max:255",
            "code"      => "required|string",
            "position"  => "required|in:head,body_start,body_end",
            "is_active" => "boolean",
            "pages"     => "nullable|array",
        ]);

        $trackingCode->update($data);

        return redirect()->route("admin.tracking-codes.index")->with("success", "Código atualizado!");
    }

    public function destroy(TrackingCode $trackingCode): RedirectResponse
    {
        $trackingCode->delete();
        return redirect()->route("admin.tracking-codes.index")->with("success", "Código removido.");
    }
}