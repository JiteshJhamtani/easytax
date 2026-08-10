<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index');
    }

    public function datatable(Request $request)
    {
        $query = Page::query();

        // Datatables Server-Side Processing
        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');

        // Order
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir');

        $columns = [
            0 => 'id',
            1 => 'title',
            2 => 'slug',
            3 => 'is_active',
            4 => 'created_at',
        ];

        if (! empty($orderColumnIndex) && isset($columns[$orderColumnIndex])) {
            $orderColumn = $columns[$orderColumnIndex];
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->latest();
        }

        // Search
        $search = $request->input('search.value');
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
            $totalFiltered = $query->count();
        }

        $pages = $query->offset($start)
            ->limit($limit)
            ->get();

        $data = [];
        foreach ($pages as $page) {
            $editUrl = route('admin.pages.edit', $page->id);
            $toggleUrl = route('admin.pages.toggle', $page->id);
            $deleteUrl = route('admin.pages.destroy', $page->id);

            $csrf = csrf_token();

            $toggleBtnClass = $page->is_active ? 'btn-warning' : 'btn-success';
            $toggleBtnIcon = $page->is_active ? 'fa-ban' : 'fa-check-circle';
            $toggleBtnText = $page->is_active ? 'Deactivate' : 'Activate';

            $action = '
                <div class="btn-group btn-group-sm">
                    <a href="'.$editUrl.'" class="btn btn-outline-primary shadow-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="'.$toggleUrl.'" method="POST" class="d-inline" style="margin: 0;">
                        <input type="hidden" name="_token" value="'.$csrf.'">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="btn btn-outline-'.($page->is_active ? 'warning' : 'success').' shadow-sm toggle-btn" title="Toggle Status">
                            <i class="fas '.($page->is_active ? 'fa-ban' : 'fa-check').'"></i>
                        </button>
                    </form>
                    <form action="'.$deleteUrl.'" method="POST" class="d-inline" style="margin: 0;" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent(\'confirm-action\', { detail: { form: this, title: \'Delete Page?\', message: \'Are you sure you want to delete this page?\' } }));">
                        <input type="hidden" name="_token" value="'.$csrf.'">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-outline-danger shadow-sm delete-btn" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            ';

            $data[] = [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'is_active' => $page->is_active,
                'created_at' => $page->created_at->format('M d, Y H:i'),
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request)
    {
        Page::create($request->validated());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page)
    {
        $page->update($request->validated());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function toggle(Page $page)
    {
        $page->update(['is_active' => ! $page->is_active]);

        return redirect()->back()->with('success', 'Page status toggled.');
    }
}
