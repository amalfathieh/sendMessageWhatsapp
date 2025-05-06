<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOtpWhatsapp{

    protected $tokenSingle;
    protected $wassengerToken;
    public function __construct($tokenSingle)
    {
        $this->tokenSingle = $tokenSingle;
    }
    public function sendOtpSingle($phone, $message)
    {
        $payload = [
            'to' => $phone,
            'message' => $message // Generates a 6-digit random OTP
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer "  . $this->tokenSingle,
            'Content-Type' => 'application/json'
        ])->post("https://sing.7th-october.com/send", $payload);

        return $response;
    }

    public function sendUltraMsg($phone, $message)
    {
        $payload = [
            'token' =>  $this->tokenSingle,
            'to' => $phone,
            'body' => $message,
        ];

        $response = Http::asForm()
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->post('https://api.ultramsg.com/instance118489/messages/chat', $payload);

        return $response;
    }



    public function sendMediaMessage($phone, $message, $mediaUrl)
    {
        $payload = [
            'phone' => $phone,
            'message' => $message,
            'media' => [
                'url' => $mediaUrl,
                'expiration' => '7d',
                'viewOnce' => false
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Token' => $this->wassengerToken
            ])->post("https://api.wassenger.com/v1/messages", $payload);

            return $response;
        } catch (\Exception $e) {
            Log::error('Wassenger API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
