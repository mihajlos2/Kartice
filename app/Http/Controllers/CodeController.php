<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\SendCodeEmail;
use App\Enums\RecipientType;
use App\Helpers\CodeGenerator;
use App\Helpers\SendOptionsCheck;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Requests\UpdateCodeRequest;
use App\Models\Code;
use App\Models\Pack;
use App\Notifications\CodeSender;
use App\Services\CodeEmailDispatcher;
use App\Services\CodeEmailScheduler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Pack $pack)
    {
        Gate::authorize('view', $pack);
        $codes = $pack->code;

        return view('show_code', [
            'codes' => $codes,
            'pack' => $pack,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Pack $pack)
    {
        Gate::authorize('view', $pack);

        return view('create_code', [
            'pack' => $pack,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCodeRequest $request, Pack $pack,CodeEmailDispatcher $emailDispatcher) //CodeEmailScheduler $emailScheduler
    {
        Gate::authorize('view', $pack);

        $CodeCode = CodeGenerator::generate_code();

        $sendDate = SendOptionsCheck::resolveSendDate(
            $request['send_options'],
            $request['send_at'] ?? null
        );

        $validated = $request->validated();

        $code = Code::create([
            'name' => $validated['recipient_name'],
            'email' => $validated['recipient_email'],
            'amount' => $validated['amount'],
            'date' => $sendDate,
            'recipient_type' => RecipientType::from($validated['recipient_type']),
            'code' => $CodeCode,
            'hashcode' => hash('sha256', $CodeCode),
            'pack_id' => $pack->id,
        ]);

        Auth::user()->notify(new CodeSender($code));

        $emailDispatcher->dispatch($code);   //$emailScheduler->schedule($code);

        return redirect()
            ->route('create.code', ['pack' => $pack->id])
            ->with('success', __('messages.code_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pack $pack, Code $code) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pack $pack, Code $code)
    {
        Gate::authorize('update', [$code,$pack]);

        return view('edit_one_code', [
            'pack' => $pack,
            'code' => $code,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCodeRequest $request, Pack $pack, Code $code) //CodeEmailScheduler $emailScheduler
    {
        Gate::authorize('update', [$code, $pack]);

        $sendDate = SendOptionsCheck::resolveSendDate(
            $request['send_options'],
            $request['send_at'] ?? null
        );

        $validated = $request->validated();

        $code->update([
            'name' => $validated['recipient_name'],
            'email' => $validated['recipient_email'],
            'amount' => $validated['amount'],
            'date' => $sendDate,
            'recipient_type' => RecipientType::from($validated['recipient_type']),
        ]);

        /*if($code->wasChanged('date')){
            $emailScheduler->schedule($code);
        }*/

        return redirect()
            ->route('show.code', ['pack' => $pack])
            ->with('success', __('messages.code_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pack $pack, Code $code)
    {
        Gate::authorize('delete', [$code, $pack]);
        $code->delete();

        return redirect()
            ->route('show.code', ['pack' => $pack])
            ->with('success', __('messages.code_delete'));
    }
}
