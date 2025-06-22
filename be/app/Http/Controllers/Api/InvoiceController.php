

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Invoices",
 *     description="API Endpoints for managing invoices"
 * )
 */
class InvoiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/invoices",
     *     summary="Get list of invoices",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by invoice number or customer name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by invoice status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "paid", "overdue", "cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter by start date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter by end date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of invoices",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Invoice")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Invoice::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('start_date')) {
            $query->where('issue_date', '>=', $request->get('start_date'));
        }

        if ($request->has('end_date')) {
            $query->where('issue_date', '<=', $request->get('end_date'));
        }

        $invoices = $query->with(['customer', 'contract', 'services'])->paginate(10);

        return InvoiceResource::collection($invoices);
    }

    /**
     * @OA\Post(
     *     path="/api/invoices",
     *     summary="Create a new invoice",
     *     tags={"Invoices"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InvoiceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Invoice created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Invoice")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
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
