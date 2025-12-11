<?php

namespace App\Services;

use Payrexx\Payrexx;
use Payrexx\Models\Request\Gateway;
use Payrexx\Models\Request\Transaction;
use Payrexx\PayrexxException;
use Illuminate\Support\Facades\Log;

class PayrexxService
{
    private Payrexx $payrexx;

    public function __construct()
    {
        $instance = config('payrexx.instance');
        $secret = config('payrexx.secret');
        
        // Debug: Log the config values (masked)
        Log::debug('Payrexx Config', [
            'instance' => $instance,
            'secret_length' => strlen($secret ?? ''),
            'secret_first_chars' => substr($secret ?? '', 0, 4) . '...',
        ]);
        
        $this->payrexx = new Payrexx($instance, $secret);
    }

    /**
     * Create a payment gateway (checkout page) for the given order.
     *
     * @param array $cart The cart data
     * @param string $referenceId Unique reference for this order
     * @return array Gateway data including redirect link
     * @throws PayrexxException
     */
    public function createGateway(array $cart, string $referenceId): array
    {
        $gateway = new Gateway();
        
        // Amount in cents
        $amountInCents = (int) round($cart['total'] * 100);
        
        $gateway->setAmount($amountInCents);
        $gateway->setCurrency(config('payrexx.currency', 'CHF'));
        $gateway->setReferenceId($referenceId);
        
        // Set success and cancel URLs
        $gateway->setSuccessRedirectUrl(route('payment.success', ['reference' => $referenceId]));
        $gateway->setCancelRedirectUrl(route('payment.cancel', ['reference' => $referenceId]));
        $gateway->setFailedRedirectUrl(route('payment.cancel', ['reference' => $referenceId]));
        
        // Optional: Set specific PSPs (payment methods)
        $psp = config('payrexx.psp', []);
        if (!empty($psp)) {
            $gateway->setPsp($psp);
        }
        
        // Optional: Add basket items for display
        if (!empty($cart['items'])) {
            $basketItems = [];
            foreach ($cart['items'] as $item) {
                $basketItems[] = [
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'amount' => (int) round($item['price'] * 100),
                ];
            }
            $gateway->setBasket($basketItems);
        }

        try {
            $response = $this->payrexx->create($gateway);
            
            return [
                'id' => $response->getId(),
                'hash' => $response->getHash(),
                'link' => $response->getLink(),
                'status' => $response->getStatus(),
            ];
        } catch (PayrexxException $e) {
            Log::error('Payrexx Gateway Creation Failed', [
                'message' => $e->getMessage(),
                'reference' => $referenceId,
            ]);
            throw $e;
        }
    }

    /**
     * Get gateway status by ID.
     *
     * @param int $gatewayId
     * @return array|null
     */
    public function getGateway(int $gatewayId): ?array
    {
        $gateway = new Gateway();
        $gateway->setId($gatewayId);

        try {
            $response = $this->payrexx->getOne($gateway);
            
            return [
                'id' => $response->getId(),
                'status' => $response->getStatus(),
                'hash' => $response->getHash(),
                'referenceId' => $response->getReferenceId(),
                'invoices' => $response->getInvoices(),
            ];
        } catch (PayrexxException $e) {
            Log::error('Payrexx Gateway Fetch Failed', [
                'message' => $e->getMessage(),
                'gateway_id' => $gatewayId,
            ]);
            return null;
        }
    }

    /**
     * Check if a gateway payment was successful.
     *
     * @param int $gatewayId
     * @return bool
     */
    public function isPaymentSuccessful(int $gatewayId): bool
    {
        $gateway = $this->getGateway($gatewayId);
        
        if (!$gateway) {
            return false;
        }

        // Check invoices for confirmed status
        $invoices = $gateway['invoices'] ?? [];
        foreach ($invoices as $invoice) {
            if (isset($invoice['status']) && $invoice['status'] === 'confirmed') {
                return true;
            }
        }

        return $gateway['status'] === 'confirmed';
    }
}
