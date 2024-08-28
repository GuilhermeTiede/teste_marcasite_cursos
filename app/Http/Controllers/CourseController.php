<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('materials', 'users')->get();

        return Inertia::render('Courses/List', [
            'courses' => $courses,
        ]);
    }

    public function edit(Course $course)
    {
        // Obtém todos os usuários inscritos no curso específico
        $enrolledUsers = $course->users()->get();

        // Obtém todos os usuários para permitir a inscrição de novos alunos
        $users = User::select('id', 'name')
            ->where('role', 'user')
            ->where('active', 1)
            ->get();

        return Inertia::render('Courses/Edit', [
            'course' => $course,
            'enrolledUsers' => $enrolledUsers,
            'users' => $users,
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Verifica se o preço contém 'R$'. Se sim, realiza a conversão.
        if (strpos($request->price, 'R$') !== false) {
            $price = str_replace(['R$', ' ', '.'], '', $request->input('price'));
            $price = str_replace(',', '.', $price);
        } else {
            $price = $request->input('price');
        }

        // Converte o valor de is_active para booleano
        $isActive = $request->input('is_active') === 'Ativo' || $request->input('is_active') == 1;

        // Atualiza o curso com os valores processados
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
    }


    public function create()
    {
        $users = User::select('id', 'name')
            ->where('role', 'user')
            ->where('active', 1)
            ->get();

        return Inertia::render('Courses/Edit', [
            'course' => new Course(),
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $price = str_replace(['R$', ' ', '.'], '', $request->input('price'));
        $price = str_replace(',', '.', $price);

        $isActive = $request->input('is_active') === 'Ativo' || $request->input('is_active') == 1;

        // Cria um novo curso com os valores processados
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
    }



    public function destroy(Course $course)
    {
        if ($course->users()->exists()) {
            return Redirect::back()->withErrors([
                'message' => 'Não é possível excluir o curso porque há alunos matriculados.'
            ]);
        }

        $course->delete();

        return Redirect::route('courses.index')->with('success', 'Curso excluído com sucesso!');
    }


    public function enrollUserInCourse(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = User::findOrFail($request->user_id);

        if ($course->users()->where('user_id', $user->id)->exists()) {
            return redirect()->route('courses.index', $courseId)->with('error', 'Este aluno já está inscrito neste curso.');
        }

        if ($course->seats > 0) {

            $course->users()->attach($user->id);

            $course->seats -= 1;
            $course->save();

            return redirect()->route('courses.index', $courseId)->with('success', 'Aluno matriculado com sucesso!');
        }

        return redirect()->route('courses.index', $courseId)->with('error', 'Não há vagas disponíveis.');
    }

    public function unenrollUserFromCourse($courseId, $userId)
    {
        $course = Course::findOrFail($courseId);
        $user = User::findOrFail($userId);

        if ($course->users()->where('user_id', $user->id)->exists()) {
            // Remove o usuário do curso
            $course->users()->detach($user->id);

            // Aumenta o número de vagas
            $course->seats += 1;
            $course->save();

            return redirect()->route('courses.index', $courseId)->with('success', 'Aluno desmatriculado com sucesso!');
        }

        return redirect()->route('courses.index', $courseId)->with('error', 'Aluno não está inscrito neste curso.');
    }
    public function listMyCourses()
    {
        $user = auth()->user();
        $courses = $user->courses()->with('materials')->get();

        return Inertia::render('MyCourses/List', [
            'courses' => $courses->map(function($course) {
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

        // Verifica se o usuário está matriculado no curso
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

}
