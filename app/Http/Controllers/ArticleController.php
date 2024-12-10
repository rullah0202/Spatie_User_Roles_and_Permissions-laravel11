<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller implements HasMiddleware
{
    public static function middleware() : array
    {
        return [
            new Middleware('permission:view articles', only:['index']),
            new Middleware('permission:edit articles', only:['edit']),
            new Middleware('permission:create articles', only:['create']),
            new Middleware('permission:delete articles', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::latest()->paginate(25);
        return view('articles.list',[
            'articles' => $articles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|min:5',
            'author' => 'required|min:5'
        ]);

        if($validator->passes()){
            Article::create([
                'title' => $request->title,
                'text' => $request->text,
                'author' => $request->author
            ]);
            return redirect()->route('articles.list')->with('success','Article added successfully');
        }
        else {
            return redirect()->back()->withInput()->withErrors($validator);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = Article::findorFail($id);

        return view('articles.edit',[
            'article' => $article
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::findorFail($id);

        $validator = Validator::make($request->all(),[
            'title' => 'required|unique:articles,title,'.$id.',id'
        ]);
        // Rule::unique('articles')->ignore($article->id)

        if($validator->passes()){
            
            $article->title = $request->title;
            $article->text = $request->text;
            $article->author = $request->author;
            $article->save();
            return redirect()->route('articles.list')->with('success','Article updated successfully');
        }
        else {
            return redirect()->back()->withInput()->withErrors($validator);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        $id = $request->id;
    
        try {
            $article = Article::find($id);
            if($article !== null){
                $article->delete();
                return response()->json([
                    'status' => true,
                    'success_message' => 'Article Deleted Successfully'
                ],200);
                
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong in Article.destroy',
                'error' =>$e->getMessage()
            ],400);    
        }

    }
}
