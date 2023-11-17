<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\AbstractApiController;
use App\Http\Requests\Report\ReportCreateRequest;
use App\Models\Transaction;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends AbstractApiController
{
    public function __construct(protected ReportService $reportService) { }

    /**
     * Display a listing of the resource.
     */
    public function index(ReportCreateRequest $request)
    {
        $report = $this->reportService->getReport($request->get('start_date'), $request->get('end_date'));

        return $this->success($report);
    }
}
