<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\ApproveMemberRequest;
use App\Services\MemberService;

class MemberApprovalController extends Controller
{
    public function __construct(protected MemberService $memberService) {}

    public function show(int $member)
    {
        $member = $this->memberService->findPending($member);

        return view('members.approval', [
            'member' => $member,
            'plans' => $this->memberService->getPlan(),
            'matches' => $this->memberService->cardsMatching($member),
        ]);
    }

    public function approve(ApproveMemberRequest $request, int $member)
    {
        $this->memberService->approve($member, $request->validated());

        return to_route('members.index')->with('success', 'Membership approved.');
    }

    public function reject(int $member)
    {
        $this->memberService->reject($member);

        return to_route('members.index')->with('success', 'Registration rejected.');
    }
}
