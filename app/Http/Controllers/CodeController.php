<?php

namespace App\Http\Controllers;

use App\Enums\RecipientType;
use App\Helpers\CodeGenerator;
use App\Helpers\SendOptionsCheck;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Requests\UpdateCodeRequest;
use App\Models\Code;
use App\Models\Pack;
use App\Notifications\CodeSender;
use App\Notifications\CodeSenderUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

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
        return view('create_code',[
            'pack' => $pack,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCodeRequest $request)
    {
        $CCode = CodeGenerator::generate_code();

        $sendDate = SendOptionsCheck::resolveSendDate(
            $request['send_options'],
            $request['send_at'] ?? null
        );

        $validated = $request->validated();

        $codes = Code::create([
            'name' => $validated['recipient_name'],
            'email' => $validated['recipient_email'],
            'amount' => $validated['amount'],
            'date' => $sendDate,
            'recipient_type' => RecipientType::from($validated['recipient_type']),
            'code' => $CCode,
            'hashcode' => hash('sha256', $CCode),
            'pack_id' => $validated['pack_id']
        ]);

        Auth::user()->notify(new CodeSender($codes));

        if($request['send_options'] === 'instant')
        {
            Notification::route('mail', $codes->email)
                ->notify(new CodeSenderUser($codes));
        }

        return redirect()
            ->route('create.code',['pack' => $validated['pack_id']])
            ->with('success', __('messages.code_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pack $pack, Code $code)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pack $pack, Code $code)
    {
        Gate::authorize('view', $code);
        if ($code->pack_id !== $pack->id) {
            abort(404);
        }

        return view('edit_one_code', [
            'pack' => $pack,
            'code' => $code,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCodeRequest $request,Pack $pack, Code $code,)
    {
        Gate::authorize('update', $code);

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

        return redirect()
            ->route('show.code', ['pack' => $pack])
            ->with('success', __('messages.code_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pack $pack, Code $code)
    {
        $code->delete();

        return redirect()
            ->route('show.code',['pack' => $pack])
            ->with('success', __('messages.code_delete'));
    }

}
