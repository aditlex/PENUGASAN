<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminTaskController extends Controller
{
    // GET: Halaman utama admin
    public function index()
    {
        $query = Task::with('list')
            ->orderBy('created_at', 'desc');

        // Tambahkan fitur search & filter jika kamu mau

        $tasks = $query->paginate(10);

        // Ambil semua list tanpa filter user
        $lists = TaskList::all();

        return Inertia::render('Admin/User', [
            'tasks' => $tasks,
            'lists' => $lists,
            'filters' => [
                'search' => request('search', ''),
                'filter' => request('filter', 'all'),
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    // POST: Buat task baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'required|string',
            'due_date' => 'nullable|date',
            'list_id' => 'required|exists:lists,id',
            'is_completed' => 'boolean'
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    // PUT: Update task
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'is_completed' => 'boolean',
            'list_id' => 'required|exists:lists,id'
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    // DELETE: Hapus task
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}
