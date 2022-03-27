<?php

namespace App\Http\Controllers;

use App\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookReviewResource;
use App\Http\Requests\PostBookReviewRequest;

class BooksReviewControllerTest extends Controller
{
    public function store(int $bookId, PostBookReviewRequest $request)
    {
        try {
            DB::beginTransaction();

            //insert data to Book Review
            $bookReview = new BookReview();
            $bookReview->book_id = $bookId;
            $bookReview->user_id = Auth::id();
            $bookReview->review = $request->get('review');
            $bookReview->comment = $request->get('comment');

            if ($bookReview->save()) {
                DB::commit();
                return response()->json([
                    'message' => 'success',
                    'data' => new BookReviewResource($bookReview)
                ], 201);
            }
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'BooksReviewControllerTest store catch',
                'code' => $e->getCode(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 422);
        }
        
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        try {
            DB::beginTransaction();
            //clause for query params
            $clause = [
                'book_id' => $bookId,
                'id' => $reviewId
            ];
            $query = DB::table('book_reviews');
            foreach ($clause as $field => $value) {
                //search data according to query params
                $query->where($field, $value);
            }
            if ($query->delete()) {
                DB::commit();
                return response()->json(['message' => 'Success'], 204);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'BooksReviewControllerTest destroy catch',
                'code' => $e->getCode(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'lq' => DB::getQueryLog()
            ], 422);
        }
    }
}
