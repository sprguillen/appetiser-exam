<?php

use Illuminate\Http\Request;
use App\Passers;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/get-passers', function() {
    $passers = Passers::select(
        'name_of_examinee',
        'campus_eligibility',
        'school',
        'division'
    )->orderBy('name_of_examinee')->get();

    $response = [
        'status' => true,
        'data' => $passers
    ];

    return response(json_encode($response),200)->header('Content-Type', 'text/plain');
});

Route::get('/scrape', function() {
    $crawler = Goutte::request('GET', 'http://www.pshs.edu.ph/nce2018/');
    $output = $crawler->filter('.border_list')->extract('_text');

    $total = count($output);

    $data = [];

    for($i = 0; $i < $total; $i += 5) {
        $data[] = [
            'name_of_examinee' => $output[$i + 1],
            'campus_eligibility' => $output[$i + 2],
            'school' => $output[$i + 3],
            'division' => $output[$i + 4]
        ];
    }

    Passers::insert($data);

    $response = [
        'status' => true,
        'message' => 'Successfully scraped from site!'
    ];

    return response(json_encode($response),200)->header('Content-Type', 'text/plain');
});
Route::post('/add-passers', function (Request $request) {
    Passers::insert([
        'name_of_examinee' => $request->input('name_of_examinee'),
        'campus_eligibility' => $request->input('campus_eligibility'),
        'school' => $request->input('school'),
        'division' => $request->input('division')
    ]);

    $response = [
        'status' => true,
        'message' => 'Successfully inserted data!'
    ];

    return response(json_encode($response),200)->header('Content-Type', 'text/plain');
});