<?php

namespace App\Http\Controllers;

use App\DataTables\MemberDataTable;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Services\MemberService;

class MemberController extends Controller
{

    public function __construct(protected MemberService $memberService)
    {
    }
    public function index(MemberDataTable $dataTable)
    {
        return $dataTable->render('members.index');
    }

    public function create()
    {
        $memberCode = $this->memberService->getNextMemberCode();
        $plans = $this->memberService->getPlan();
        return view('members.create', compact('memberCode', 'plans'));
    }

    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();
        $data['member_code'] = $this->memberService->getNextMemberCode();
        $this->memberService->createMember($data);

        return to_route('members.index')->with('success', 'Member created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $member = $this->memberService->findById($id);
        $plans = $this->memberService->getPlan();
        return view('members.edit', compact('member', 'plans'));
    }

    public function update(UpdateMemberRequest $request, $id)
    {
        $data = $request->validated();
        $this->memberService->updateMember($id, $data);
        return to_route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy($id)
    {
        $this->memberService->deleteMember($id);
        
        return to_route('members.index')->with('success', 'Member deleted successfully.');
    }
}
