<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('materials')->get();

        return Inertia::render('Courses/List', [
            'courses' => $courses,
        ]);
    }

    public function edit($id)
    {
        $course = Course::with('materials')->findOrFail($id);

        return Inertia::render('Courses/Edit', [
            'course' => $course,
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $course->update($request->all());

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    public function store(Request $request)
    {
        $course = Course::create($request->all());

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }
}
