<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;
use Mail;
// use App\Mail\SendMail;
class SendEmailController extends Controller
{
	function send(Request $requet)
	{
		Mail::send(['text' => 'mail'], ['name', 'Digitalideas'], function ($message) {
			$message->to('info@digitalideasltd.co.uk', 'Digitalideas')->subject('Test Email');
			$message->from('info@digitalideasltd.co.uk', 'phoenix');
		});

		// Mail::to('info@digitalideasltd.co.uk')->send(new SendMail($data));
		// return view('emails.exception');
	}
}
