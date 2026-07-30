<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Filters\CodeFilter;
use App\Helpers\CodeGenerator;
use App\Helpers\Enums\RecipientType;
use App\Helpers\SendOptionsCheck;
use App\Http\Requests\BulkDeleteCodesRequest;
use App\Http\Requests\FilterCodesRequest;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Requests\UpdateCodeRequest;
use App\Models\Code;
use App\Models\Pack;
use App\Notifications\CodeSender;
use App\Services\CodeEmailDispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterCodesRequest $request, Pack $pack, CodeFilter $codeFilter)
    {
        Gate::authorize('view', $pack);

        $codes = $codeFilter
            ->apply($pack->code()->getQuery(), $request->validated())
            ->paginate(10)
            ->withQueryString();

        return view('codes.index', [
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

        return view('codes.create', [
            'pack' => $pack,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCodeRequest $request, Pack $pack, CodeEmailDispatcher $emailDispatcher)
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

        $emailDispatcher->dispatch($code);

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
        Gate::authorize('update', [$code, $pack]);

        return view('codes.edit', [
            'pack' => $pack,
            'code' => $code,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCodeRequest $request, Pack $pack, Code $code, CodeEmailDispatcher $emailDispatcher)
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

        $emailDispatcher->dispatch($code);

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

    public function export(Pack $pack, Code $code): StreamedResponse
    {
        Gate::authorize('view', $code);

        $fileName = 'pack-'.$pack->id.'-code'.$code->id.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        return response()->stream(function () use ($code) {
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

            fclose($handle);
        }, 200, $headers);
    }

    public function destroySelected(BulkDeleteCodesRequest $request,Pack $pack)
    {
        $validated = $request->validated();

        $codes = $pack->code()
            ->whereIn('id', $validated['code_ids'])
            ->get();

        foreach ($codes as $code) {
            Gate::authorize('delete', [$code, $pack]);
        }

        foreach ($codes as $code) {
            $code->delete();
        }

        return redirect()
            ->route('show.code', ['pack' => $pack])
            ->with('success', __('messages.codes_deleted'));
    }
}
