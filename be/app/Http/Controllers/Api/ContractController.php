

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HopDongRequest;
use App\Models\HopDong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Contracts",
 *     description="API Endpoints for managing contracts"
 * )
 */
class ContractController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contracts",
     *     summary="Get list of contracts",
     *     tags={"Contracts"},
     *     @OA\Parameter(
     *         name="room_id",
     *         in="query",
     *         description="Filter by room ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         description="Filter by customer ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by contract status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "expired", "terminated"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of contracts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Contract")
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
    public function index(Request $request)
    {
        try {
            $query = HopDong::query();

            if ($request->has('phong_id')) {
                $query->where('phong_id', $request->phong_id);
            }

            if ($request->has('khach_hang_id')) {
                $query->where('khach_hang_id', $request->khach_hang_id);
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $hopDongs = $query->with(['phong', 'khachHang', 'dichVu'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $hopDongs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách hợp đồng'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/contracts",
     *     summary="Create a new contract",
     *     tags={"Contracts"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ContractRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contract created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Contract")
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
    public function store(HopDongRequest $request)
    {
        try {
            DB::beginTransaction();

            $hopDong = HopDong::create($request->validated());

            if ($request->has('dich_vu_ids')) {
                $hopDong->dichVu()->attach($request->dich_vu_ids);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo hợp đồng thành công',
                'data' => $hopDong
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo hợp đồng'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/contracts/{id}",
     *     summary="Get contract details",
     *     tags={"Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Contract ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contract details",
     *         @OA\JsonContent(ref="#/components/schemas/Contract")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contract not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $hopDong = HopDong::with(['phong', 'khachHang', 'dichVu'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $hopDong
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy hợp đồng'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/contracts/{id}",
     *     summary="Update contract details",
     *     tags={"Contracts"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Contract ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ContractRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contract updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Contract")
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
     *         response=404,
     *         description="Contract not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(HopDongRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $hopDong = HopDong::findOrFail($id);

            // Kiểm tra nếu hợp đồng đã kết thúc
            if ($hopDong->trang_thai === 'da_ket_thuc') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể cập nhật hợp đồng đã kết thúc'
                ], 400);
            }

            $hopDong->update($request->validated());

            if ($request->has('dich_vu_ids')) {
                $hopDong->dichVu()->sync($request->dich_vu_ids);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật hợp đồng thành công',
                'data' => $hopDong
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật hợp đồng'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/contracts/{id}",
     *     summary="Delete a contract",
     *     tags={"Contracts"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Contract ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Contract deleted successfully"
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
     *         response=404,
     *         description="Contract not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $hopDong = HopDong::findOrFail($id);

            // Kiểm tra nếu hợp đồng đang có phòng đang thuê
            if ($hopDong->trang_thai === 'dang_thue') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa hợp đồng đang có phòng đang thuê'
                ], 400);
            }

            $hopDong->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa hợp đồng'
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = HopDong::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ma_hop_dong', 'like', "%{$keyword}%")
                        ->orWhereHas('khachHang', function ($q) use ($keyword) {
                            $q->where('ho_ten', 'like', "%{$keyword}%")
                                ->orWhere('so_dien_thoai', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('phong', function ($q) use ($keyword) {
                            $q->where('ten', 'like', "%{$keyword}%");
                        });
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $hopDongs = $query->with(['phong', 'khachHang', 'dichVu'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $hopDongs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm hợp đồng'
            ], 500);
        }
    }
}
