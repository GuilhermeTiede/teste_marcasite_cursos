<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoursesRequest;
use App\Http\Requests\EnrollUserRequest;
use App\Models\Course;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        try {
            $courses = Course::with('materials', 'users')->get();
            return Inertia::render('Courses/List', [
                'courses' => $courses,
            ]);
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao carregar os cursos.']);
        }
    }

    public function edit(Course $course)
    {
        try {
            $enrolledUsers = $course->users()->get();
            $users = User::select('id', 'name')
                ->where('role', 'user')
                ->where('active', 1)
                ->get();

            return Inertia::render('Courses/Edit', [
                'course' => $course,
                'enrolledUsers' => $enrolledUsers,
                'users' => $users,
            ]);
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao carregar o curso ou os usuários.']);
        }
    }

    public function update(CoursesRequest $request, $id)
    {
        try {
            $course = Course::findOrFail($id);

            $price = $request->price;
            if (strpos($price, 'R$') !== false) {
                $price = str_replace(['R$', ' ', '.'], '', $price);
                $price = str_replace(',', '.', $price);
            }

            $isActive = $request->input('is_active') === 'Ativo' || $request->input('is_active') == 1;

            $course->update([
                'name' => $request->input('name'),
                'category' => $request->input('category'),
                'price' => $price,
                'seats' => $request->input('seats'),
                'registration_start' => $request->input('registration_start'),
                'registration_end' => $request->input('registration_end'),
                'description' => $request->input('description'),
                'thumbnail' => $request->input('thumbnail'),
                'is_active' => $isActive,
            ]);

            return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso!');
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao atualizar o curso.']);
        }
    }

    public function store(CoursesRequest $request)
    {
        try {
            $price = $request->price;
            if (strpos($price, 'R$') !== false) {
                $price = str_replace(['R$', ' ', '.'], '', $price);
                $price = str_replace(',', '.', $price);
            }

            $isActive = $request->input('is_active') === 'Ativo' || $request->input('is_active') == 1;

            $course = Course::create([
                'name' => $request->input('name'),
                'category' => $request->input('category'),
                'price' => $price,
                'seats' => $request->input('seats'),
                'registration_start' => $request->input('registration_start'),
                'registration_end' => $request->input('registration_end'),
                'description' => $request->input('description'),
                'thumbnail' => $request->input('thumbnail'),
                'is_active' => $isActive,
            ]);

            return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso!');
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao criar o curso.']);
        }
    }

    public function create()
    {
        try {
            $users = User::select('id', 'name')
                ->where('role', 'user')
                ->where('active', 1)
                ->get();

            return Inertia::render('Courses/Edit', [
                'course' => new Course(),
                'users' => $users,
            ]);
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao carregar os dados necessários.']);
        }
    }

    public function destroy(Course $course)
    {
        try {
            if ($course->users()->exists()) {
                return Redirect::back()->withErrors(
                    ['error' => 'Não é possível excluir o curso porque há alunos matriculados.']
                );
            }

            $course->delete();
            return Redirect::route('courses.index')->with('success', 'Curso excluído com sucesso!');
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao excluir o curso.']);
        }
    }

    public function enrollUserInCourse(EnrollUserRequest $request, $courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $user = User::findOrFail($request->user_id);

            if ($course->users()->where('user_id', $user->id)->exists()) {
                return redirect()->route('courses.index', $courseId)->with(
                    'error',
                    'Este aluno já está inscrito neste curso.'
                );
            }

            if ($course->seats > 0) {
                $course->users()->attach($user->id);
                $course->seats -= 1;
                $course->save();

                return redirect()->route('courses.index', $courseId)->with('success', 'Aluno matriculado com sucesso!');
            }

            return redirect()->route('courses.index', $courseId)->with('error', 'Não há vagas disponíveis.');
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao inscrever o aluno no curso.']);
        }
    }

    public function unenrollUserFromCourse($courseId, $userId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $user = User::findOrFail($userId);

            if ($course->users()->where('user_id', $user->id)->exists()) {
                $course->users()->detach($user->id);
                $course->seats += 1;
                $course->save();

                return redirect()->route('courses.index', $courseId)->with(
                    'success',
                    'Aluno desmatriculado com sucesso!'
                );
            }

            return redirect()->route('courses.index', $courseId)->with('error', 'Aluno não está inscrito neste curso.');
        } catch (Exception $e) {
            return Redirect::back()->withErrors(['error' => 'Falha ao desmatricular o aluno.']);
        }
    }

    public function searchStudents(Request $request, $courseId)
    {
        $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        $course = Course::with([
            'users' => function ($query) use ($request) {
                if ($request->has('query')) {
                    $query->where('name', 'LIKE', '%' . $request->query('query') . '%');
                }
            }
        ])->findOrFail($courseId);

        return response()->json($course->users);
    }

    public function listMyCourses()
    {
        $user = auth()->user();
        $courses = $user->courses()->with('materials')->get();

        return Inertia::render('MyCourses/List', [
            'courses' => $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'category' => $course->category,
                    'enrolled_at' => $course->pivot->enrolled_at,
                    'status' => $course->pivot->status,
                    'price_paid' => $course->pivot->price_paid,
                    'materials' => $course->materials,
                ];
            }),
        ]);
    }

    public function editMyCourse(Course $course)
    {
        $user = auth()->user();

        $enrollment = $user->courses()->where('course_id', $course->id)->firstOrFail();

        return Inertia::render('MyCourses/Edit', [
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'category' => $course->category,
                'enrolled_at' => $enrollment->pivot->enrolled_at,
                'status' => $enrollment->pivot->status,
                'price_paid' => $enrollment->pivot->price_paid,
            ],
        ]);
    }


    public function showcase()
    {
        $courses = Course::where('is_active', 1)->get();

        return Inertia::render('Showcase/List', [
            'courses' => $courses,
        ]);
    }

    public function enroll($courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        if ($course->users()->where('user_id', $user->id)->exists()) {
            return redirect()->route('showcase')->with('error', 'Você já está matriculado neste curso.');
        }

        if ($course->seats > 0) {
            $course->users()->attach($user->id, [
                'enrolled_at' => now(),
                'price_paid' => $course->price,
                'status' => 'active'
            ]);

            $course->seats -= 1;
            $course->save();

            return redirect()->route('showcase')->with('success', 'Curso Compro e matrícula realizada com sucesso!');
        }

        return redirect()->route('showcase')->with('error', 'Não há vagas disponíveis.');
    }

}
