<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\TemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __construct(private TemplateService $templateService) {}

    public function index(): View
    {
        $templates = Template::ordered()->get();

        return view('admin.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'prompt_hint' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $this->templateService->create($data, $request->file('image'));

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template created.');
    }

    public function edit(Template $template): View
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'prompt_hint' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $this->templateService->update($template, $data, $request->file('image'));

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template updated.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        $this->templateService->delete($template);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template deleted.');
    }

    public function toggle(Template $template): RedirectResponse
    {
        $this->templateService->toggleActive($template);

        return back()->with('success', 'Template status updated.');
    }
}
