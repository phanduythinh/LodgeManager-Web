<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $invoices = Invoice::with(['contract', 'customer', 'items'])->paginate(10);
        return InvoiceResource::collection($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceRequest $request): InvoiceResource
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::create($request->validated());

            foreach ($request->items as $item) {
                $invoice->items()->create($item);
            }

            DB::commit();
            return new InvoiceResource($invoice->load(['contract', 'customer', 'items']));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): InvoiceResource
    {
        $invoice->load(['contract', 'customer', 'items']);
        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvoiceRequest $request, Invoice $invoice): InvoiceResource
    {
        try {
            DB::beginTransaction();

            $invoice->update($request->validated());

            $invoice->items()->delete();
            foreach ($request->items as $item) {
                $invoice->items()->create($item);
            }

            DB::commit();
            return new InvoiceResource($invoice->load(['contract', 'customer', 'items']));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): Response
    {
        try {
            DB::beginTransaction();

            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
