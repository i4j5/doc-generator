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

        return response()->download(storage_path('app') . $path);
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

    /**
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    private function num2str($num) {
        $nul = 'ноль';

        $ten = [
            ['','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'],
            ['','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'],
        ];

        $a20 = [
            'десять',
            'одиннадцать',
            'двенадцать',
            'тринадцать',
            'четырнадцать',
            'пятнадцать',
            'шестнадцать',
            'семнадцать',
            'восемнадцать',
            'девятнадцать'
        ];

        $tens = [
            2 => 'двадцать',
            'тридцать',
            'сорок',
            'пятьдесят',
            'шестьдесят',
            'семьдесят',
            'восемьдесят',
            'девяносто'
        ];

        $hundred =['','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот'];
        
        $unit = [
            ['копейка' ,'копейки' ,'копеек',	 1],
            ['рубль'   ,'рубля'   ,'рублей'    ,0],
            ['тысяча'  ,'тысячи'  ,'тысяч'     ,1],
            ['миллион' ,'миллиона','миллионов' ,0],
            ['миллиард','милиарда','миллиардов',0],
        ];

        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));

        $out = [];

        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[] = $this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = $this->morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     */
    private function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }
}
