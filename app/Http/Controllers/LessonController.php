<?php

namespace App\Http\Controllers;

use App\Company;
use App\Department;
use App\Position;
use App\Branch;
use App\Lesson;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class LessonController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
            )->get();

        return view('lesson.create',
            compact('company', 'department', 'position', 'staffs'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $lesson = new Lesson();
        $lesson->created_by = Auth::id();
        $lesson->title = $request->title;
        $lesson->status = config('app.approve_status_approve');
        $lesson->company_id = $request->company_id;
        $lesson->departments = $request->departments;
        $lesson->positions = $request->positions;
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $lesson->attachment = store_lesson_file($atts);
        }

        if ($lesson->save()) {
            return redirect()->back()->with(['status' => 1]);
        }
        return redirect()->back()->with(['status' => 4]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        ini_set("memory_limit", -1);
           
        $data = Lesson::find($id);
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
            )->get();

        return view('lesson.edit', compact('data', 'company', 'department', 'position', 'staffs'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);
        $lesson->title = $request->title;       
        $lesson->status = config('app.approve_status_approve');
        $lesson->company_id = $request->company_id;
        $lesson->departments = $request->departments;
        $lesson->positions = $request->positions;
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $lesson->attachment = store_lesson_file($atts);
        }
        if ($lesson->save()) {
            return back()->with(['status' => 2]);
        }

        return redirect()->back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data = Lesson::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('lesson.show', compact('data'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $lesson = Lesson::find($id)->attachment;
        @$oldFile = @$lesson->src;
        @File::delete(@$oldFile);

        Lesson::destroy($id);
        return response()->json(['success' => 1]);
    }

    public function publicLesson(Request $request)
    {
        $data = Lesson::leftjoin('users', 'users.id', '=', 'lesson.created_by')
            ->leftjoin('companies', 'companies.id', '=', 'lesson.company_id');

        $user = Auth::user();
        if (@$user->company_id != 1 && !@$user->action_object->can_lesson && Auth::id() != 3062) { // if user not in stsk group not show all
            $data = $data ->whereIn('lesson.company_id', [$user->company_id]);  
        }

        $company_id = $request->company_id;
        if ($company_id != null) { // All
            $data = $data->where('lesson.company_id', 'like', $company_id);  
        }

        $department_id = $request->department_id;
        if ($department_id != null) { // All
            $data = $data->where('lesson.departments', 'like', '%"'.$department_id.'"%');  
        }

        $position_id = $request->position_id;
        if ($position_id != null) { // All
            $data = $data->where('lesson.positions', 'like', '%"'.$position_id.'"%');  
        }

        $keyword = $request->keyword;
        if ($keyword != null) { // All
            $data = $data->where('lesson.title', 'like', '%'.$keyword.'%');  
        }

        $data = $data->where('lesson.status', config('app.approve_status_approve'))
            ->whereNull('lesson.deleted_at'); 

        $total = $data->count();

        $data = $data->select([
                'lesson.*',
                'users.name as requester_name',
                'companies.name as company_name'
            ])
            ->orderBy('lesson.id', 'DESC')
            ->paginate(30);
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        return view('lesson.public_lesson', compact(
            'data',
            'total',
            'company',
            'department',
            'position'
        ));
    }
}
