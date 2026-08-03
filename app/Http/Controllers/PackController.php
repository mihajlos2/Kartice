<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePackRequest;
use App\Models\Pack;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packs = Auth::user()->pack()->paginate(6);

        return view('packs.index', [
            'packs' => $packs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('packs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackRequest $request)
    {
        $validated = $request->validated();

        $pack = Auth::user()->pack()->create([
            'name' => $validated['name'],
            'date' => $validated['date'],
        ]);

        return redirect()
            ->route('create.code', ['pack' => $pack]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pack $pack) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pack $pack): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pack $pack): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pack $pack)
    {
        Gate::authorize('delete', $pack);
        $pack->delete();

        return redirect()
            ->route('packs.index')
            ->with('success', __('messages.pack_deleted'));
    }

    public function export(Pack $pack): StreamedResponse
    {
        Gate::authorize('view', $pack);

        $fileName = 'pack-'.$pack->id.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        return response()->stream(function () use ($pack) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                throw new RuntimeException('CSV file could not be opened.');
            }

            fputcsv(
                $handle,
                [
                    'ID',
                    'Name',
                    'Email',
                    'Amount',
                    'Send Date',
                    'Recipient Type',
                    'Code',
                    'Queued At',
                    'Sent At',
                ],
                ',',
                '"',
                ''
            );

            $pack->code()
                ->select([
                    'id',
                    'name',
                    'email',
                    'amount',
                    'date',
                    'recipient_type',
                    'code',
                    'queued_at',
                    'sent_at',
                ])
                ->chunkById(500, function ($codes) use ($handle) {
                    foreach ($codes as $code) {
                        fputcsv(
                            $handle,
                            [
                                $code->id,
                                $code->name,
                                $code->email,
                                $code->amount,
                                $code->date?->format('Y-m-d H:i:s'),
                                $code->recipient_type->value,
                                $code->code,
                                $code->queued_at?->format('Y-m-d H:i:s'),
                                $code->sent_at,
                            ],
                            ',',
                            '"',
                            ''
                        );
                    }
                });

            fclose($handle);
        }, 200, $headers);
    }
}
