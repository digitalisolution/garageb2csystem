<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function send()
    {


    	Mail::send(['text'=>'mail'],['name','DigitalIdeas'],function($message){
    		$message->to('info@digitalideasltd.co.uk','DigitalIdeas')->subject('Hello !!!');
    		$message->from('info@digitalideasltd.co.uk','phoenix');
    	});exit;
    }
}
