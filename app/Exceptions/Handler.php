<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $exception) {
            try {
                $name = "DSWD TRAIN: System Error";

                $message = "**Error:** " . e($exception->getMessage()) . "\n\n";
                $message .= "**File:** " . e($exception->getFile()) . "\n";
                $message .= "**Line:** " . e($exception->getLine()) . "\n\n";
                $message .= "**Stack Trace:**\n```\n" . substr($exception->getTraceAsString(), 0, 2000) . "\n```\n\n";

                // Check if we're in a web request
                if (app()->runningInConsole() === false && request()->fullUrl()) {
                    $requestUrl = request()->fullUrl();
                    $message .= "**Request URL:** [Click here]($requestUrl)";
                } else {
                    $message .= "**Request URL:** N/A (CLI or unavailable)";
                }

                $webhookUrl = "https://chat.googleapis.com/v1/spaces/AAQAt0xQNus/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=25XZz9SyOQgAy_TzZMi3ahmTX-ieKu0OOEM6GIJVdA8";

                $payload = [
                    'cards' => [[
                        'header' => [
                            'title' => $name,
                        ],
                        'sections' => [[
                            'widgets' => [
                                [
                                    'textParagraph' => [
                                        'text' => $message
                                    ]
                                ],
                            ],
                        ]],
                    ]],
                ];

                Http::post($webhookUrl, $payload);
            } catch (Throwable $e) {
                Log::warning('Failed to send error notification to Google Chat: ' . $e->getMessage());
            }
        });
    }
}
