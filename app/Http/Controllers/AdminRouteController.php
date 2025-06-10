<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;
use inertia\Inertia;
use inertia\Response;

class AdminRouteController extends Controller
{
   public function admin()
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

        return Inertia::render('Admin/Users', [
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


    // Simpan list baru 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        TaskList::create($validated);

        return redirect()->route('Admin/Users')->with('success', 'List created successfully!');
    }

    // Update list
    public function update(Request $request, TaskList $list)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $list->update($validated);

        return redirect()->route('Admin/Users')->with('success', 'List updated successfully!');
    }

    // Hapus list
    public function destroy(TaskList $list)
    {
        $list->delete();

        return redirect()->route('Admin/Users')->with('success', 'List deleted successfully!');
    }
    
}
