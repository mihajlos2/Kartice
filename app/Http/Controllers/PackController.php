<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackRequest;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packs = Auth::user()->pack;

        return view('show_pack', [
            'packs' => $packs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('create_pack');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackRequest $request)
    {
        $validated = $request->validated();

        $pack = Auth::user()->pack()->create([
            'name' => $validated->name,
            'date' => $validated->date,
        ]);

        return redirect()
            ->route('create.code',['pack' => $pack]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pack $pack)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pack $pack)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pack $pack)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pack $pack)
    {
        $pack->delete();

        return redirect()
            ->route('packs.index')
            ->with('success', 'Pack i svi povezani kodovi su obrisani.');
    }
}
