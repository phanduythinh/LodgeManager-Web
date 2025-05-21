<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $contracts = Contract::with(['room', 'customer', 'invoices'])->paginate(10);
        return ContractResource::collection($contracts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractRequest $request): ContractResource
    {
        try {
            DB::beginTransaction();

            $contract = Contract::create($request->validated());

            if ($request->has('services')) {
                foreach ($request->services as $service) {
                    $contract->services()->attach($service['service_id'], [
                        'price' => $service['price']
                    ]);
                }
            }

            DB::commit();
            return new ContractResource($contract->load(['room', 'customer', 'services']));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
