<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        if (!$keyword) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm.');
        }

        $originalKeyword = trim($keyword);

        // Loại bỏ các từ dư thừa (stop words) phổ biến trong câu hỏi tiếng Việt
        $stopWords = [
            'tôi', 'muốn', 'mua', 'cần', 'tìm', 'cho', 'hỏi', 'có', 'bán', 'không',
            'vài', 'một', 'chút', 'kí', 'kg', 'lạng', 'thử', 'cái', 'loại', 'gì', 'đó',
            'thì', 'với', 'như', 'là', 'xin', 'vui', 'lòng', 'giúp', 'đặt', 'hàng',
            'bạn', 'ơi', 'shop', 'cửa', 'ở', 'đây', 'thế', 'nào', 'để', 'những', 'các', 'thêm',
            'dòng', 'chữ', 'thiệt', 'dài', 'vd'
        ];
        //xử lý stop words
        $cleanedKeyword = $originalKeyword;
        foreach ($stopWords as $word) {
            $pattern = '/(?<=^|\s)' . preg_quote($word, '/') . '(?=\s|$)/iu';
            $cleanedKeyword = preg_replace($pattern, '', $cleanedKeyword);
        }
        //xử lý khoảng trắng thừa
        $cleanedKeyword = trim(preg_replace('/\s+/', ' ', $cleanedKeyword));

        if (empty($cleanedKeyword)) {
            $cleanedKeyword = $originalKeyword;
        }
        // Mức 1: Tìm kiếm chính xác chuỗi đã làm sạch
        $queryLevel1 = Product::where('name', 'like', '%' . $cleanedKeyword . '%')
            ->orWhere('description', 'like', '%' . $cleanedKeyword . '%');

        if ($queryLevel1->count() > 0) {
            $products = $queryLevel1->paginate(12);
        } else {
            $words = explode(' ', $cleanedKeyword);
            $words = array_filter($words, fn($w) => mb_strlen($w) >= 2);

            if (!empty($words)) {
                // Mức 2: Yêu cầu TẤT CẢ các từ khóa phải xuất hiện
                $queryLevel2 = Product::where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->where(function ($subQ) use ($word) {
                            $subQ->where('name', 'like', '%' . $word . '%')
                                 ->orWhere('description', 'like', '%' . $word . '%');
                        });
                    }
                });

                if ($queryLevel2->count() > 0) {
                    $products = $queryLevel2->paginate(12);
                } else {
                    // Mức 3: Yêu cầu BẤT KỲ từ khóa nào xuất hiện
                    $queryLevel3 = Product::where(function ($q) use ($words) {
                        foreach ($words as $word) {
                            $q->orWhere('name', 'like', '%' . $word . '%')
                              ->orWhere('description', 'like', '%' . $word . '%');
                        }
                    });

                    $products = $queryLevel3->paginate(12);
                }
            } else {
                $products = $queryLevel1->paginate(12);
            }
        }
        //xử lí 
        $products->appends(['keyword' => $originalKeyword]);
        $keyword = $originalKeyword;

        return view('clients.pages.product_search', compact('keyword', 'products'));
    }
}
