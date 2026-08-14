<?php

namespace App\Http\Controllers\Gestao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gestao\AreaRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(): View
    {
        $areas = Area::withCount('usuarios')
            ->orderBy('nome')
            ->paginate(15);

        return view('gestao.areas.index', compact('areas'));
    }

    public function create(): View
    {
        return view('gestao.areas.create', ['area' => new Area(['ativo' => true])]);
    }

    public function store(AreaRequest $request): RedirectResponse
    {
        Area::create($request->validated());

        return redirect()->route('gestao.areas.index')->with('sucesso', 'Área cadastrada com sucesso.');
    }

    public function edit(Area $area): View
    {
        return view('gestao.areas.edit', compact('area'));
    }

    public function update(AreaRequest $request, Area $area): RedirectResponse
    {
        $area->update($request->validated());

        return redirect()->route('gestao.areas.index')->with('sucesso', 'Área atualizada com sucesso.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        if ($area->usuarios()->exists()) {
            return back()->with('erro', 'Não é possível remover uma área com gestores vinculados. Reatribua os gestores antes.');
        }

        $area->delete();

        return redirect()->route('gestao.areas.index')->with('sucesso', 'Área removida.');
    }
}
