<?php
namespace App\Http\Controllers;

use App\Services\PayrexxService;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Order\Create as CreateOrderAction;
use App\Actions\Order\Finalize as FinalizeOrderAction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PayrexxService $payrexxService
    ) {}

    /**
     * Handle successful payment return.
     */
    public function success(Request $request, string $reference): View|RedirectResponse
    {
        $storedReference = session()->get('payment_reference');
        $gatewayId = session()->get('payment_gateway_id');

        // Verify reference matches
        if ($reference !== $storedReference) {
            return redirect()->route('page.checkout.basket')->with('error', 'Ungültige Zahlungsreferenz.');
        }

        // Verify payment with Payrexx
        if ($gatewayId && $this->payrexxService->isPaymentSuccessful($gatewayId)) {
            $cart = (new GetCartAction())->execute();
            $invoiceAddress = session()->get('invoice_address', []);
            $deliveryAddress = session()->get('delivery_address', []);

            // Create order in database
            (new CreateOrderAction())->execute(
                $cart,
                $invoiceAddress,
                $deliveryAddress,
                'creditcard',
                $reference
            );

            // Finalize order (send emails, clear cart, etc.)
            (new FinalizeOrderAction())->execute();

            return redirect()->route('page.checkout.confirmation');
        }

        // Payment not confirmed yet - could be pending
        return view('pages.checkout.pending', [
            'reference' => $reference,
        ]);
    }

    /**
     * Handle cancelled/failed payment return.
     */
    public function cancel(Request $request, string $reference): RedirectResponse
    {
        // Clear payment session data but keep cart
        session()->forget(['payment_reference', 'payment_gateway_id']);

        return redirect()->route('page.checkout.summary')->with('error', 'Die Zahlung wurde abgebrochen. Bitte versuchen Sie es erneut.');
    }

    /**
     * Handle Payrexx webhook notifications.
     */
    public function webhook(Request $request)
    {
        // Payrexx sends transaction data via POST
        $transaction = $request->input('transaction');

        if (!$transaction) {
            return response()->json(['error' => 'No transaction data'], 400);
        }

        // Log webhook for debugging
        \Log::info('Payrexx Webhook Received', $transaction);

        // Here you would typically:
        // 1. Verify the webhook signature
        // 2. Find the order by reference ID
        // 3. Update order status based on transaction status
        // 4. Send confirmation emails, etc.

        return response()->json(['success' => true]);
    }
}
