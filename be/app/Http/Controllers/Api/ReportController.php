<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::all();
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaBaoCao' => 'required|string|unique:reports',
            'DoanhThu' => 'required|numeric',
            'LoiNhuan' => 'required|numeric',
            'SoNguoiThue' => 'required|integer',
            'SoNhaTro' => 'required|integer',
            'SoPhongTrong' => 'required|integer',
            'MaChuTro' => 'required|string|exists:owners,MaChuTro',
            'MaHopDong' => 'nullable|string|exists:contracts,MaHopDong'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = Report::create($request->all());
        return response()->json($report, 201);
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        return response()->json($report);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'DoanhThu' => 'numeric',
            'LoiNhuan' => 'numeric',
            'SoNguoiThue' => 'integer',
            'SoNhaTro' => 'integer',
            'SoPhongTrong' => 'integer',
            'MaChuTro' => 'string|exists:owners,MaChuTro',
            'MaHopDong' => 'nullable|string|exists:contracts,MaHopDong'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = Report::findOrFail($id);
        $report->update($request->all());
        return response()->json($report);
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return response()->json(null, 204);
    }
}
