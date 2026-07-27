<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\Pack;
use App\Notifications\CodeSender;
use Illuminate\Http\Request;
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
        return view('create_code',[
            'pack' => $pack,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Code = new Code();
        $CCode = $Code->generate_code() ?? 'kalak';


        if($request['send_options'] === 'send_at')
        {
            $sdate = $request['send_at'];
        }elseif($request['send_options'] === 'instant')
        {
            $sdate = now()->addMinutes(5);
        }else
        {
            $sdate = $request['send_options'];
        }



        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'string','email', 'max:255'],
            'amount' => ['required', 'integer', 'max:255'],
            'recipient_type' => ['required', 'string', 'max:10'],
            'pack_id' => ['required', 'integer', 'exists:packs,id']
            ]);


        $codes = Code::create([
            'name' => $validated['recipient_name'],
            'email' => $validated['recipient_email'],
            'amount' => $validated['amount'],
            'date' => $sdate,
            'recipient_type' => $validated['recipient_type'],
            'code' => $CCode,
            'hashcode' => hash('sha256', $CCode),
            'pack_id' => $validated['pack_id']
        ]);

        Auth::user()->notify(new CodeSender($codes));

        return redirect()
            ->route('create.code',['pack' => $validated['pack_id']])
            ->with('success','Uspesno daodato!!!');
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
    public function update(Request $request,Pack $pack, Code $code,)
    {
        if ($code->pack_id !== $pack->id) {
            abort(404);
        }


        if($request['send_options'] === 'send_at')
        {
            $sdate = $request['send_at'] ?? 'no date';
        }elseif($request['send_options'] === 'instant')
        {
            $sdate = now()->addMinutes(5);
        }else
        {
            $sdate = $request['send_options'];
        }


        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'email', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'recipient_type' => ['required', 'string', 'in:specific,bulk'],
        ]);

         $code->update([
            'name' => $validated['recipient_name'],
            'email' => $validated['recipient_email'],
            'amount' => $validated['amount'],
            'date' => $sdate,
            'recipient_type' => $validated['recipient_type'],
        ]);



        return redirect()
            ->route('show.code', ['pack' => $pack])
            ->with('success', 'Kod je uspešno izmenjen.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pack $pack, Code $code)
    {
        $code->delete();

        return redirect()
            ->route('show.code',['pack' => $pack]);
    }

}
