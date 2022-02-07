<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BookController extends Controller
{
    //
    public function getAllBooks(){
        return Book::all();
    }

    public function addBook(Request $req){
        $book = new Book;
        $book->title = $req->title;
        $book->other = $req->other;
        $book->category = $req->category;
        $book->section = $req->section;
        $book->pages = $req->pages;
        $book->path = $req->path;
        $book->first_publish = $req->first_publish;
        if($book->save()){
            return ["Result"=>"Data has been saved!"];
        }else{
            return ["Result"=>"Data has been not saved!"];
        }

    }

    public function like(Request $req){
        $book = Book::find($req->_id);
        $book->increment('likes');
    }

    public function comment(Request $req)
    {
        $book = Book::find($req->_id);
        $book->comments = array_merge($book->comments, $req->comment);
        if($book->save()){
            return ["Result"=>"Commented!"];
        }else{
            return ["Result"=>"Comment not sent!"];
        }
    }
}
