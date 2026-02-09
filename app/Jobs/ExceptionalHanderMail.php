<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\ExceptionOccured;
use Mail;

class ExceptionalHanderMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $email;
    public $message;
    public function __construct($email,$message)
    {
       $this->email=$email;
        $this->message=$message;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
         Mail::to($this->email)->send(new ExceptionOccured($this->message));
    }
}
