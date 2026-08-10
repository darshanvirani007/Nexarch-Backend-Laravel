<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\JobApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ReflectionMethod;
use Tests\TestCase;

class JobApplicationStatusTest extends TestCase
{
    public function test_it_accepts_only_the_statuses_supported_by_supabase(): void
    {
        $method = new ReflectionMethod(JobApplicationController::class, 'rules');
        $rules = $method->invoke(new JobApplicationController, Request::create('/', 'POST'), null);

        foreach (['pending', 'applied', 'accepted', 'rejected'] as $status) {
            $validator = Validator::make([
                'job_name' => 'Software Engineer',
                'job_link' => 'https://example.com/jobs/engineer',
                'status' => $status,
                'display_order' => 0,
            ], $rules);

            $this->assertFalse($validator->fails(), "Expected {$status} to be accepted.");
        }

        foreach (['interested', 'interview', 'offer', 'withdrawn'] as $status) {
            $validator = Validator::make([
                'job_name' => 'Software Engineer',
                'job_link' => null,
                'status' => $status,
                'display_order' => 0,
            ], $rules);

            $this->assertTrue($validator->fails(), "Expected legacy status {$status} to be rejected.");
        }
    }
}
