<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('index');
    }

    public function fetchdata()
    {
        $students = Student::get();
        return response()->json([
            'students' => $students
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:students',
            'image' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->getMessageBag()
            ]);
        }
        $student = new Student();
        $student->name = $request->name;
        $student->email = $request->email;

        if ($request->hasfile('image')) {
            $img = $request->file('image');
            $imgName = time() . "." . $img->getClientOriginalExtension();
            $img->move('image/', $imgName);
            $student->image = $imgName;
        }
        $student->save();

        return response()->json([
            'status' => 200,
            'message' => 'record successfully subimitted'
        ]);
        // return redirect(Route('students.create'))->with('status', 'record successfully subimitted');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
        return response()->json([
            'student'=> $student
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:students',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->getMessageBag()
            ]);
        }
        $student->name = $request->name;
        $student->email = $request->email;

        if ($request->hasfile('image')) {
            $path = 'image/' . $student->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $img = $request->file('image');
            $imgName = time() . "." . $img->getClientOriginalExtension();
            $img->move('image/', $imgName);
            $student->image = $imgName;
        }
        $student->update();
        return response()->json([
            'status' => 200,
            'message' => 'record successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
        $path = 'image/' . $student->image;
        if (File::exists($path)) {
            File::delete($path);
        }
        $student->delete();
        return response()->json([
            'message'=>'record successfully deleted',
        ]);
        
    }
}
