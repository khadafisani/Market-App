<?php

namespace App\Http\Controllers;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Requests\MemberStore;
use App\Http\Requests\MemberUpdate;
use App\Http\Requests\MemberGetMember;
use App\Models\Member;

class MemberController extends Controller
{
    public function store(MemberStore $request)
    {
        $request->validated();

        $id = IdGenerator::generate(['table' => 'Members', 'length' => 9, 'prefix' => '312']);

        $data = [
            'id' => $id,
            'name' => $request->name,
            'email' => $request->email,
        ];

        Member::create($data);

        return response()->json([
            'status' => 'ok',
            'code' => 201,
            'message' => "Member Successfully Created!",
            'data' => [],
        ]);
    }

    public function update(MemberUpdate $request)
    {
        $request->validated();

        $member = Member::find($request->id);
        $member->name = $request->name;
        $member->email = $request->email;
        $member->save();

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'message' => 'Member Successfully Update',
            'data' => [],
        ]);
    }

    public function delete($id)
    {
        $member = Member::find($id);
        if($member)
        {
            $member->delete();

            $data = [
                'status' => 'ok',
                'code' => 200,
                'message' => 'Member has been deleted',
                'data' => [],
            ];
        }
        else
        {
            $data = [
                'status' => 'failed',
                'code' => 400,
                'message' => 'Member not found',
                'data' => [],
            ];
        }

        return response()->json($data);
    }

    public function members()
    {
        return response()->json([
            'status' => 'ok',
            'message' => '',
            'code' => 200,
            'data' => Member::all()]);
    }

    public function getMember(MemberGetMember $request)
    {
        $request->validated();

        return response()->json([
            'status' => 'ok',
            'message' => '',
            'code' => 200,
            'data' => Member::find($request->id)]);
    }
}
