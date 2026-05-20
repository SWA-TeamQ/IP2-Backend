<?php
namespace App\Services;

class PaymentService {
    /**
     * Simulates a payment processing logic
     */
    public function process($amount, $paymentDetails) {
        // In a real app, you'd call Stripe/PayPal API here.
        // For now, we simulate a successful transaction if card details are provided.
        
        if (empty($paymentDetails['card_number']) || strlen($paymentDetails['card_number']) < 16) {
            return [
                'status' => 'failed',
                'message' => 'Invalid card number'
            ];
        }

        // Logic: All payments over $5000 require "manual review" (just an example of business logic)
        if ($amount > 5000) {
            return [
                'status' => 'pending',
                'message' => 'Large transaction awaiting verification'
            ];
        }

        return [
            'status' => 'success',
            'transaction_id' => 'TXN-' . strtoupper(uniqid())
        ];
    }
}