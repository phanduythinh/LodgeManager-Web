<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalDocumentRequest;
use App\Http\Resources\LegalDocumentResource;
use App\Models\LegalDocument;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LegalDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $documents = LegalDocument::with('building')->paginate(10);
        return LegalDocumentResource::collection($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LegalDocumentRequest $request): LegalDocumentResource
    {
        $document = LegalDocument::create($request->validated());
        return new LegalDocumentResource($document);
    }

    /**
     * Display the specified resource.
     */
    public function show(LegalDocument $legalDocument): LegalDocumentResource
    {
        return new LegalDocumentResource($legalDocument->load('building'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LegalDocumentRequest $request, LegalDocument $legalDocument): LegalDocumentResource
    {
        $legalDocument->update($request->validated());
        return new LegalDocumentResource($legalDocument);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LegalDocument $legalDocument): Response
    {
        $legalDocument->delete();
        return response()->noContent();
    }
}
