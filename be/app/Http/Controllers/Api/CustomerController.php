

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KhachHangRequest;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Customers",
 *     description="API Endpoints for managing customers"
 * )
 */
class CustomerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Get list of customers",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name, email or phone",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by customer status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of customers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Customer")
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
            $query = KhachHang::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $khachHangs = $query->with(['hopDong'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $khachHangs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách khách hàng'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Create a new customer",
     *     tags={"Customers"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CustomerRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
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
    public function store(KhachHangRequest $request)
    {
        try {
            DB::beginTransaction();

            $khachHang = KhachHang::create($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo khách hàng thành công',
                'data' => $khachHang
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo khách hàng'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Get customer details",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer details",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $khachHang = KhachHang::with(['hopDong'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $khachHang
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     summary="Update customer details",
     *     tags={"Customers"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CustomerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
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
     *         description="Customer not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(KhachHangRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $khachHang = KhachHang::findOrFail($id);

            // Kiểm tra nếu khách hàng đang có hợp đồng
            if ($khachHang->hopDong && $khachHang->hopDong->trang_thai === 'dang_thue') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể cập nhật khách hàng đang có hợp đồng thuê'
                ], 400);
            }

            $khachHang->update($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật khách hàng thành công',
                'data' => $khachHang
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật khách hàng'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     summary="Delete a customer",
     *     tags={"Customers"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Customer deleted successfully"
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
     *         description="Customer not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $khachHang = KhachHang::findOrFail($id);

            // Kiểm tra nếu khách hàng đang có hợp đồng
            if ($khachHang->hopDong && $khachHang->hopDong->trang_thai === 'dang_thue') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa khách hàng đang có hợp đồng thuê'
                ], 400);
            }

            $khachHang->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa khách hàng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa khách hàng'
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = KhachHang::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$keyword}%")
                        ->orWhere('cmnd_cccd', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $khachHangs = $query->with(['hopDong'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $khachHangs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm khách hàng'
            ], 500);
        }
    }
}
