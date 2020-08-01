<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use PHPStamp\Templator;
use PHPStamp\Document\WordDocument;

class DocumentController extends Controller
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
        $documents = Document::where('user_id', $user_id)->paginate(10);
        return view('documents.index')->with(compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_id = Auth::user()->id;
        $templates = DocumentTemplate::where('user_id', $user_id)->get();
        return view('documents.create')->with(compact('templates'));
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
            'template_id' => 'required',
        ]);

        $templates = DocumentTemplate::find($request->template_id);

        $user_id = Auth::user()->id;

        $documentPath = storage_path() . '/app/' . $templates->path;

        $ext = File::extension($documentPath);

        $file_name = md5(microtime() . rand(0, 9999)) . '.' . $ext;

        $cachePath = storage_path() . '/app/local/cache/';
        $templator = new Templator($cachePath);
        $templator->debug = true;

        $document = new WordDocument($documentPath);

        $values = [
            'title' => 'ffffffffffffff'
        ];

        $result = $templator->render($document, $values);

        $path = "local/documents/$user_id/documents/$file_name";
        Storage::disk('local')->put($path, $result->output());

        $document = Document::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user_id,
            'path' => $path
        ]);

        dd($document);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        if (Auth::user()->id != $document->user_id) {
            return redirect('documents');
        }

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        $document->delete();
        return redirect('documents');
    }
}
