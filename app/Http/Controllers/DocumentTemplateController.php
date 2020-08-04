<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DocumentTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        $documentTemplates = DocumentTemplate::where('user_id', $user_id)->paginate(10);
        return view('document_templates.index')->with(compact('documentTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('document_templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|min:3',
            'file' => 'required|mimes:doc,docx|max:204000',
        ]);

        $file = $request->file('file');

        $user_id = Auth::user()->id;
        $file_name = md5(microtime() . rand(0, 9999)) . '.' . $file->getClientOriginalExtension();

        $path = $request->file->storeAs(config('docs.path.templates') . $user_id, $file_name);

        $documentTemplate = DocumentTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::user()->id,
            'file_name' => $file_name 
        ]);

        return redirect('document-templates/' . $documentTemplate->id);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        if (Auth::user()->id != $documentTemplate->user_id) {
            return redirect('document-templates');
        }

        return view('document_templates.show', compact('documentTemplate'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentTemplate $documentTemplate)
    {

        if (Auth::user()->id != $documentTemplate->user_id) {
            return redirect('document-templates');
        }

        return view('document_templates.edit', compact('documentTemplate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $request->validate([
            'name' => 'required|min:3',
            'file' => 'mimes:doc,docx|max:204000',
        ]);

        $user_id = Auth::user()->id;

        if ($documentTemplate->user_id != $user_id ) {
            return redirect('document-templates');
        }

        $documentTemplate->name = $request->name;
        $documentTemplate->description = $request->description;

        $file = $request->file('file');
        if ($file) {
            $file_name = md5(microtime() . rand(0, 9999)) . '.' . $file->getClientOriginalExtension();
            File::Delete(storage_path('app') . config('docs.path.templates') . "$user_id/" . $documentTemplate->file_name);
            $path = $request->file->storeAs(config('docs.path.templates') . $user_id, $file_name);
            $documentTemplate->file_name = $file_name;
        }
        

        $documentTemplate->save();

        return redirect('document-templates/' . $documentTemplate->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        $user_id = Auth::user()->id;

        if ($documentTemplate->user_id === $user_id) {
            File::Delete(storage_path('app') . config('docs.path.templates') . "$user_id/" . $documentTemplate->file_name);
            $documentTemplate->delete();
        }

        return redirect('document-templates');
    }

    public function download(Request $request, DocumentTemplate $documentTemplate)
    {

        $user_id = Auth::user()->id;

        if (!$documentTemplate) abort(404);
        if ($user_id != $documentTemplate->user_id) abort(403);

        $path = config('docs.path.templates') . "$user_id/" . $documentTemplate->file_name;

        if (!Storage::exists($path)) abort(404);

        return 
            (new Response(Storage::get($path)))
                ->header('Content-Type', Storage::mimeType($path));
    }
}
