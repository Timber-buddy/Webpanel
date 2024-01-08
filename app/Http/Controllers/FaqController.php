<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqQuestion;
use Session;
class FaqController extends Controller
{
    public function index()
    {
        $faqs = FaqQuestion::get();
        return view('backend.faq.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq = new FaqQuestion();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        Session::put(['message' => 'Faq has been Created successfully', 'SmgStatus' => 'success']);
        return redirect()->back();
    }

    public function show($id)
    {


        $faq = FaqQuestion::find($id);
        $record = array(
            'faq' => $faq,
            'url' => route('faqs.update', $id)
        );
        return $record;

    }

    public function update(Request $request, $id){

        $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq = FaqQuestion::find($id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        Session::put(['message' => 'Faq has been Updated successfully', 'SmgStatus' => 'success']);
        return redirect()->back();
    }

    public function destroy($id){

        $faq = FaqQuestion::find($id);
        $faq->delete();
        Session::put(['message' => 'Faq has been Deleted successfully', 'SmgStatus' => 'success']);
        return redirect()->back();
    }

    public function change_status(Request $request) {
        $faq = FaqQuestion::find($request->id);
        $faq->status = $request->status;
        $faq->save();
        return 1;
    }

}
