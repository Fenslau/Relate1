<?php

namespace App\Http\Controllers;

use stdClass;
use App\Mail\FeedbackMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function send(Request $request) {
        if (empty($request->user)) {
          $request->validate([
              'opros' => 'required|min:15|max:255',
          ]);
        } else {
          $request->validate([
              'opros' => 'required|min:15',
          ]);
        }
        $data = new stdClass();
                $data->message = $request->opros;
        try {
          $to = explode(',', env('ADMIN_EMAILS'));
          Mail::to($to)->send(new FeedbackMailer($data));
        } catch (\Swift_TransportException $exception) {
          return back()->with('error', 'Что-то пошло не так, сообщение не было отправлено. '.$exception->getMessage());
        }
          return back()->with('success', 'Ваше сообщение успешно отправлено');
    }
}
