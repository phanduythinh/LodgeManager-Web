

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $reports = Report::with('building')->paginate(10);
        return ReportResource::collection($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request): ReportResource
    {
        $report = Report::create($request->validated());
        return new ReportResource($report);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report): ReportResource
    {
        return new ReportResource($report->load('building'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReportRequest $request, Report $report): ReportResource
    {
        $report->update($request->validated());
        return new ReportResource($report);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report): Response
    {
        $report->delete();
        return response()->noContent();
    }
}
