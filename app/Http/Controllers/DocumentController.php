<?php

namespace App\Http\Controllers;

use PHPStamp\Templator;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\DocumentTemplate;
use PHPStamp\Document\WordDocument;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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

        $user_id = Auth::user()->id;

        $templates = DocumentTemplate::find($request->template_id);

        $documentPath = storage_path('app') . config('docs.path.templates') . "$user_id/" . $templates->file_name;

        $ext = File::extension($documentPath);

        $file_name = md5(microtime() . rand(0, 9999)) . '.' . $ext;

        $templator = new Templator(config('docs.path.cache'));
        $templator->debug = true;

        $document = new WordDocument($documentPath);

        $json = json_decode($request->json, true);

        $values = $json ? $json : [];

        $result = $templator->render($document, $values);

        Storage::disk('local')->put(config('docs.path.docunents') . "$user_id/$file_name", $result->output());

        $document = Document::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user_id,
            'file_name' => $file_name 
        ]);

        return redirect('documents/' . $document->id);
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
  
    public function download(Request $request, Document $document)
    {

        $user_id = Auth::user()->id;

        if (!$document) abort(404);
        if ($user_id != $document->user_id) abort(403);

        $path = config('docs.path.docunents') . "$user_id/" . $document->file_name;

        if (!Storage::exists($path)) abort(404);

        return 
            (new Response(Storage::get($path)))
                ->header('Content-Type', Storage::mimeType($path));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {

        $user_id = Auth::user()->id;

        if ($document->user_id === $user_id) {
            File::Delete(storage_path('app') . config('docs.path.docunents') . "$user_id/" . $document->file_name);
            $document->delete();
        }

        return redirect('documents');
    }
}
