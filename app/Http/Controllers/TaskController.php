<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    // tasks
    public function index()
    {
        $query = Task::with('list')->orderBy('created_at', 'desc');

        // Search
        if (request()->has('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter
        if (request()->has('filter') && request('filter') !== 'all') {
            $query->where('is_completed', request('filter') === 'completed');
        }

        $tasks = $query->paginate(10);

        $lists = TaskList::with('user')->get();

        return Inertia::render('Tasks/Index', [
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

    // POST /tasks
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

    // PUT /tasks/{task}
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

    // DELETE /tasks/{task}
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}
