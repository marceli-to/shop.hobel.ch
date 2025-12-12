<?php

namespace App\Services;

use Payrexx\Payrexx;
use Payrexx\Models\Request\Gateway;
use Payrexx\Models\Request\Design;
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
        
        // Apply custom design if configured
        $designId = config('payrexx.design_id');
        if ($designId) {
            $gateway->setDesign($designId);
        }
        
        // Optional: Set specific PSPs (payment methods)
        $psp = config('payrexx.psp', []);
        if (!empty($psp)) {
            $gateway->setPsp($psp);
        }
        
        // Add basket items for display (including shipping and tax)
        if (!empty($cart['items'])) {
            $basketItems = [];
            
            foreach ($cart['items'] as $item) {
                // Product item
                $basketItems[] = [
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'amount' => (int) round($item['price'] * 100),
                ];
                
                // Shipping per item (if applicable)
                if (!empty($item['shipping_price']) && $item['shipping_price'] > 0) {
                    $basketItems[] = [
                        'name' => 'Versand: ' . $item['name'],
                        'quantity' => 1,
                        'amount' => (int) round($item['shipping_price'] * 100),
                    ];
                }
            }
            
            // Add tax as separate line item
            if (!empty($cart['tax']) && $cart['tax'] > 0) {
                $taxRate = config('invoice.tax_rate', 8.1);
                $basketItems[] = [
                    'name' => "MwSt. {$taxRate}%",
                    'quantity' => 1,
                    'amount' => (int) round($cart['tax'] * 100),
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

    /**
     * Create a custom design for the payment page.
     *
     * @param array $options Design options
     * @return array Design data
     * @throws PayrexxException
     */
    public function createDesign(array $options = []): array
    {
        $design = new Design();
        
        // Basic settings
        $design->setDefault($options['default'] ?? false);
        $design->setName($options['name'] ?? 'Shop Design');
        
        // Header image shape: square, rectangular or round
        $design->setHeaderImageShape($options['headerImageShape'] ?? 'square');
        
        // Logo settings (Hex codes without #)
        $design->setLogoBackgroundColor($options['logoBackgroundColor'] ?? 'FFFFFF');
        $design->setLogoBorderColor($options['logoBorderColor'] ?? 'ffffff');
        
        // Background: 'color' or 'image'
        $design->setBackground($options['background'] ?? 'color');
        $design->setBackgroundColor($options['backgroundColor'] ?? 'ffffff');
        
        // Header background: 'color' or 'image'
        $design->setHeaderBackground($options['headerBackground'] ?? 'color');
        $design->setHeaderBackgroundColor($options['headerBackgroundColor'] ?? 'ffffff');
        
        // VPOS gradient colors
        $design->setVPOSGradientColor1($options['vposGradientColor1'] ?? 'ffffff');
        $design->setVPOSGradientColor2($options['vposGradientColor2'] ?? 'ffffff');
        
        // Typography
        $design->setFontFamily($options['fontFamily'] ?? 'Arial');
        $design->setFontSize($options['fontSize'] ?? '14');
        $design->setTextColor($options['textColor'] ?? '24363A');
        $design->setTextColorVPOS($options['textColorVPOS'] ?? '24363A');
        
        // Link colors
        $design->setLinkColor($options['linkColor'] ?? '0074D6');
        $design->setLinkHoverColor($options['linkHoverColor'] ?? '2A6496');
        
        // Button colors
        $design->setButtonColor($options['buttonColor'] ?? '99CC33');
        $design->setButtonHoverColor($options['buttonHoverColor'] ?? '19B8F2');
        
        // Other settings
        $design->setEnableRoundedCorners($options['enableRoundedCorners'] ?? true);
        $design->setUseIndividualEmailLogo($options['useIndividualEmailLogo'] ?? false);
        $design->setEmailHeaderBackgroundColor($options['emailHeaderBackgroundColor'] ?? 'FAFAFA');
        
        // Header image (logo) - use default or custom path
        $logoPath = $options['headerImagePath'] ?? public_path('img/logo.png');
        if (file_exists($logoPath)) {
            $design->setHeaderImage(new \CURLFile($logoPath));
        }
        
        // Custom header links per language
        if (!empty($options['headerImageCustomLink'])) {
            $design->setHeaderImageCustomLink($options['headerImageCustomLink']);
        }

        try {
            $response = $this->payrexx->create($design);
            
            $id = null;
            $name = $options['name'] ?? 'Shop Design';
            
            try {
                $id = $response->getId();
            } catch (\Error $e) {
                // ID not set in response
            }
            
            try {
                $name = $response->getName();
            } catch (\Error $e) {
                // Name not set in response
            }
            
            return [
                'id' => $id,
                'name' => $name,
                'response' => $response,
            ];
        } catch (PayrexxException $e) {
            Log::error('Payrexx Design Creation Failed', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
