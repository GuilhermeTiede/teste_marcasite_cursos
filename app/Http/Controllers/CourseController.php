<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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

    public function edit(Course $course)
    {

        return Inertia::render('Courses/Edit', [
            'course' => $course,
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
        return Inertia::render('Courses/Edit',[
            'course' => new Course(),
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
        $course->delete();

        return Redirect::route('courses.index')->with('success', 'Curso excluído com sucesso!');
    }
}
