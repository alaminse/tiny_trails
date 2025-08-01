<?php

namespace Modules\UserRolePermission\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('userrolepermission::permission');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('userrolepermission::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('userrolepermission::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('userrolepermission::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}


    public function get(Request $request)
    {
        $patient_id = $request->input('id');

        $feedbacks = Feedback::query();
        if($patient_id != null) {
            $feedbacks = $feedbacks->where('patient_id', $patient_id);
        } else {
            $feedbacks = $feedbacks->where('add_by', Auth::user()->id);
        }
        $feedbacks = $feedbacks->latest()->paginate(500);
        $total = $feedbacks->total();
        $tableRows = '';

        foreach ($feedbacks as $key => $feedback) {
            $tableRows .= '
                <tr>
                    <td>' .  ++$key . '</td>
                    <td>' . $feedback->patient?->user?->name . '</td>
                    <td>' . $feedback->user?->name . '</td>
                    <td>' . Carbon::parse($feedback->date)->format('Y-m-d') . '</td>
                    <td class="text-center">
                        <a href="' . route('feedbacks.show', $feedback->slug) . '" class="btn btn-sm btn-info mb-1 me-1" style="cursor: pointer" title="Show"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>';
        }

        $pagination = '<nav aria-label="Page navigation"><ul class="pagination">';
        $pagination .= '<li class="page-item"><a class="page-link btn btn-sm button-yellow" href="' . $feedbacks->previousPageUrl() . '">Previous</a></li>';
        foreach (range(1, $feedbacks->lastPage()) as $page) {
            $pagination .= '<li class="page-item ' . ($feedbacks->currentPage() == $page ? 'active' : '') . '"><a class="page-link btn btn-sm button-yellow" href="' . $feedbacks->url($page) . '">' . $page . '</a></li>';
        }

        $pagination .= '<li class="page-item"><a class="page-link btn btn-sm button-yellow" href="' . $feedbacks->nextPageUrl() . '">Next</a></li>';
        $pagination .= '</ul></nav>';

        return response()->json([
            'tableRows' => $tableRows,
            'pagination' => $pagination,
            'total' => $total,
        ]);

    }

}
