<?php

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BooksControllerTest;
use App\Http\Controllers\BooksReviewControllerTest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//creating new controller test because controller need to be generate on the machine in order to use it

DB::enableQueryLog();

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// token required to access to this endpoint
Route::middleware(['auth:sanctum'])->group(function(){

    //postman syntax : http://localhost/mifx/api/books parameter: isbn, title, description, author, published_year
    Route::post('/books', [BooksControllerTest::class, 'store']);

    //postman syntax : http://localhost/mifx/books/{id}/reviews parameter: review, comment
    Route::post('/books/{id}/reviews', [BooksReviewControllerTest::class, 'store']);

    //postman syntax : http://localhost/mifx/auth/logout
    Route::post('/auth/logout', [UserController::class, 'logout']);
});

//postman syntax : http://localhost/mifx/api/books
Route::get('/books', [BooksControllerTest::class, 'index']);

//no need to pass to controller, just get the resource and filter it
//postman syntax : http://localhost/mifx/api/books/{id}
Route::get('/books/{id}', function($id){
    try{
        $bookResource = new BookResource(Book::findOrFail($id));
        return response()->json([
            'data' => $bookResource->paginate(1), 
        ], 200);
    } catch(\Exception $e){
        return response()->json(['message' => 'Book Not Found'], 404);
    }
});

//postman syntax : http://localhost/mifx/api/books/{id}/reviews/{id}
Route::delete('/books/{bookId}/reviews/{reviewId}', [BooksReviewControllerTest::class, 'destroy']);

//postman syntax : http://localhost/mifx/api/auth/register
Route::post('/auth/register', [UserController::class, 'register']);

//postman syntax : http://localhost/mifx/api/auth/login
Route::post('/auth/login', [UserController::class, 'login']);


// Route::post('/books', 'BooksController@store');
// Route::post('/books/{id}/reviews', 'BooksReviewController@store');
// Route::delete('/books/{bookId}/reviews/{reviewId}', 'BooksReviewController@destroy');
