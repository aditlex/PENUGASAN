<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mulai membangun query, jangan langsung dieksekusi dengan get()
        $query = TaskList::with('tasks');

        // Ambil nilai search dari request
        $search = request('search');

        // Jika ada nilai search, tambahkan kondisi where ke query
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Eksekusi query setelah semua kondisi ditambahkan
        $lists = $query->get(); // Atau bisa juga $query->paginate(10); jika datanya banyak

        return Inertia::render('Lists/Index', [
            'lists' => $lists, // Sekarang ini akan berisi data yang sudah difilter
            'filters' => [
                'search' => $search, // Mengembalikan nilai search ke frontend agar tetap ada di input
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        TaskList::create([
            ...$validated,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('admin')->with('success', 'List created successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskList $list)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $list->update($validated);

        return redirect()->route('admin')->with('success', 'List updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskList $list)
    {
        $list->delete();
        return redirect()->route('admin')->with('success', 'List deleted successfully!');
    }
}

