<?php

namespace App\Http\Controllers;

use App\Http\Requests\Report\ReportCreateRequest;
use App\Models\Transaction;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct(protected ReportService $reportService) { }

    /**
     * Display a listing of the resource.
     */
    public function index(ReportCreateRequest $request)
    {
        $report = $this->reportService->getReport($request->get('start_date'), $request->get('end_date'));

        return view('report.index', compact('report'));
    }
}
