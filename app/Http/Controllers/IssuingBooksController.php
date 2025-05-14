<?php

namespace App\Http\Controllers;

use App\Models\IssuingBooks;
use App\Models\Manuals;
use App\Models\User;
use Illuminate\Http\Request;

class IssuingBooksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('issue.index', ['Issue' => IssuingBooks::all()]);
    }

    public function noOfIssuingBooks()
    {
        $count = IssuingBooks::all();

        return $count->count();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(IssuingBooks $issuingBooks)
    {
        return view('issue.add', ['Users' => User::role('user')->get(), 'Manuals' => Manuals::where('type', 1)->get()]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IssuingBooks $issuingBooks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IssuingBooks $issuingBooks)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IssuingBooks $issuingBooks)
    {
        //
    }
}
