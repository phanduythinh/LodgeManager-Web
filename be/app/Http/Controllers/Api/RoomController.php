

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhongRequest;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Rooms",
 *     description="API Endpoints for managing rooms"
 * )
 */
class RoomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/rooms",
     *     summary="Get list of rooms",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="building_id",
     *         in="query",
     *         description="Filter by building ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by room status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"available", "occupied", "maintenance"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of rooms",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Room")
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
            $query = Phong::query();

            if ($request->has('toa_nha_id')) {
                $query->where('toa_nha_id', $request->toa_nha_id);
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $phongs = $query->with(['toaNha', 'hopDong'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $phongs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách phòng'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/rooms",
     *     summary="Create a new room",
     *     tags={"Rooms"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoomRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Room created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Room")
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
    public function store(PhongRequest $request)
    {
        try {
            DB::beginTransaction();

            $phong = Phong::create($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo phòng thành công',
                'data' => $phong
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo phòng'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/rooms/{id}",
     *     summary="Get room details",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room details",
     *         @OA\JsonContent(ref="#/components/schemas/Room")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $phong = Phong::with(['toaNha', 'hopDong'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $phong
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy phòng'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/rooms/{id}",
     *     summary="Update room details",
     *     tags={"Rooms"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoomRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Room")
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
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(PhongRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $phong = Phong::findOrFail($id);

            // Kiểm tra nếu phòng đang có hợp đồng
            if ($phong->hopDong && $phong->hopDong->trang_thai === 'dang_thue') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể cập nhật phòng đang có hợp đồng thuê'
                ], 400);
            }

            $phong->update($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật phòng thành công',
                'data' => $phong
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật phòng'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/rooms/{id}",
     *     summary="Delete a room",
     *     tags={"Rooms"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Room deleted successfully"
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
     *         description="Room not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $phong = Phong::findOrFail($id);

            // Kiểm tra nếu phòng đang có hợp đồng
            if ($phong->hopDong && $phong->hopDong->trang_thai === 'dang_thue') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa phòng đang có hợp đồng thuê'
                ], 400);
            }

            $phong->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa phòng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa phòng'
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Phong::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ten', 'like', "%{$keyword}%")
                        ->orWhere('loai_phong', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('toa_nha_id')) {
                $query->where('toa_nha_id', $request->toa_nha_id);
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $phongs = $query->with(['toaNha', 'hopDong'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $phongs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm phòng'
            ], 500);
        }
    }
}
