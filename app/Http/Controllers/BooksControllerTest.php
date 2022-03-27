<?php

namespace App\Http\Controllers;

use App\Book;
use App\Author;
use App\BookAuthor;
use App\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostBookRequest;

class BooksControllerTest extends Controller
{
    public function index()
    {
		//query all from Book model inside the book resource collection
		return BookResource::collection(Book::all());
    }

    public function store(PostBookRequest $request) {
		try{
			DB::beginTransaction();

			// Insert data to Books
			$books = new Book();
			$books->isbn = $request->isbn;
			$books->title = $request->title;
			$books->description = $request->description;
			$books->published_year = $request->published_year;

			if ($books->save()) {

				$author = Author::find($request->author);

				// Insert data to Book Author
				$bookAuthor = new BookAuthor();
				$bookAuthor->book_id = $books->id;
				$bookAuthor->author_id = $author->id;

				// Insert data to Book Review
				$bookReview = new BookReview();
				$bookReview->book_id = $books->id;
				$bookReview->user_id = Auth::id();
				$bookReview->review = 0;
				$bookReview->comment = '';


				if ($bookAuthor->save() && $bookReview->save()) {
					$books->authors = $author;
					$books->review = $bookReview;
					DB::commit();
					return response()->json([
						'message' => 'Book Added Successfully',
						'data' => $books
					], 401);
				}
			} else {
				return response()->json(['message' => 'Failed to insert new book'], 422);
			}
		} catch(\Exception $e){
			DB::rollback();
			return response()->json([
				'message' => 'BooksControllerTest Catch Store', 
				'code' => $e->getCode(), 
				'error' => $e->getMessage(),
				'line' => $e->getLine()
			], 422);
		}

        
    }

    public function destroy($bookID, $reviewID) {

        $bookReview = BookReview::where([
            'book_id' => $bookID,
            'review' => $reviewID
        ])->delete();

        return response()->json(204); 
    }
}
