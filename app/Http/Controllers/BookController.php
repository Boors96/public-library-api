<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use \Illuminate\Auth\Access\Response;

class BookController extends Controller
{
    // get all books
    public function getAllBooks(){
        return Book::all();
    }
    // add new book
    public function addBook(Request $req){
        $upload = $req->file('book')->store('books');
        if($upload){
            $book = new Book;
            $book->title = $req->title;
            $book->other = $req->other;
            $book->category = $req->category;
            $book->section = $req->section;
            $book->pages = $req->pages;
            $book->path = $upload;
            $book->first_publish = $req->first_publish;
            $book->download_link = '';
            if($book->save()){
                if($this->setDownloadLink($book)){
                    return response()->json(["Result"=>"Data has been saved!"]);
                }else{
                    return response()->json(["Result"=>"Download link not saved!"]);
                }
            }else{
                return response()->json( ["Result"=>"Data has been not saved!"]);
            }
        }
    }
    // like a book
    public function like(Request $req){
        $book = Book::find($req->id);
        $book->increment('likes');
    }
    // comment on a book
    public function comment(Request $req)
    {
        $book = Book::find($req->id);
        $book->comments = array_merge($book->comments, $req->comment);
        if($book->save()){
            return ["Result"=>"Commented!"];
        }else{
            return ["Result"=>"Comment not sent!"];
        }
    }
    // get book comments
    public function getBookComments(Request $req)
    {
        $book = Book::find($req->id);
        if($book->comments != null){
            return response()->json($book->comments);
        }else{
            return ["Result"=>"No Comments found!"];
        }
    }

    // download file
    public function downloadBook(Request $req)
    {
       $book = Book::find($req->id);
       $bookFile = storage_path() . '/app/' . $book->path;
       $headers = ['Content-Type: application/pdf'];
       return Response()->download($bookFile, $book->title . '.pdf', $headers);
    }

    // set download link
    private function setDownloadLink($book){
        $book->download_link = url('/api/download-book/'. $book->id);
        if($book->save()){
            return true;
        }else{
            return false;
        }
    }
}
