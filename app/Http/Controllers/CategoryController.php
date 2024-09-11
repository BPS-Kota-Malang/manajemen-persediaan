<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class CategoryController extends Controller
{
    protected $user;
    protected $categories;
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $categories = Category::all();
        return view('admin.category', compact('categories'));//n
        
        // Debugging data
        dd($categories);

        return view('admin.category', [
            'categories' => $categories
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.create-category');
        // return view('categories.create');
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ]);

        Category::create([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    
        // $category = Category::create($request->all());
        // $categories = Category::all();
        // return redirect()->route('sukses', $category->id);
        // //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $categories = Category::get();
        $users = User::all(); 
        return view('admin.category', compact('categories', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
