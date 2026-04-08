<?php

namespace App\Http\Controllers;

use App\DataTables\MemberPlanDataTable;
use App\Http\Requests\MemberPlan\StoreMemberPlanRequest;
use App\Services\MemberPlanService;

class MemberPlanController extends Controller
{
    public function __construct(protected MemberPlanService $memberPlanService)
    {

    }

    public function index(MemberPlanDataTable $dataTable)
    {
        return $dataTable->render('member_plans.index');
    }

    public function create()
    {
        return view('member_plans.create');
    }

    public function edit($id)
    {
        $memberPlan = $this->memberPlanService->findById($id);
        return view('member_plans.edit', compact('memberPlan'));
    }

    public function store(StoreMemberPlanRequest $request)
    {
        $data = $request->validated();
        $data = $this->memberPlanService->createMemberPlan($data);
        return to_route('member-plans.index')->with('success', 'Member plan created successfully.');
    }

    public function update(StoreMemberPlanRequest $request, $id)
    {
        $data = $request->validated();
        $this->memberPlanService->updateMemberPlan($id, $data);
        return to_route('member-plans.index')->with('success', 'Member plan updated successfully.');
    }

    public function destroy($id)
    {
        $this->memberPlanService->deleteMemberPlan($id);
        return to_route('member-plans.index')->with('success', 'Member plan deleted successfully.');
    }
}
