<?php
namespace App\Http\Controllers;

use App\Services\PayrexxService;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Destroy as DestroyCartAction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Payrexx\PayrexxException;

class PaymentController extends Controller
{
    public function __construct(
        private PayrexxService $payrexxService
    ) {}

    /**
     * Display the checkout summary page.
     */
    public function summary(): View|RedirectResponse
    {
        $cart = (new GetCartAction())->execute();

        if (empty($cart['items'])) {
            return redirect()->route('cart.index');
        }

        return view('pages.checkout.summary', [
            'cart' => $cart,
        ]);
    }

    /**
     * Initiate payment with Payrexx.
     */
    public function initiate(Request $request): RedirectResponse
    {
        $cart = (new GetCartAction())->execute();

        if (empty($cart['items'])) {
          return redirect()->route('cart.index')->with('error', 'Ihr Warenkorb ist leer.');
        }

        // Generate unique reference ID
        $referenceId = 'ORDER-' . strtoupper(Str::random(8));

        // Store reference in session for later verification
        session()->put('payment_reference', $referenceId);
        session()->put('payment_cart', $cart);

        try {
            $gateway = $this->payrexxService->createGateway($cart, $referenceId);

            // Store gateway ID for verification
            session()->put('payment_gateway_id', $gateway['id']);

            // Redirect to Payrexx payment page
            return redirect()->away($gateway['link']);

        } catch (PayrexxException $e) {
            return redirect()->route('checkout.summary')->with('error', 'Zahlung konnte nicht initialisiert werden. Bitte versuchen Sie es erneut.');
        }
    }

    /**
     * Handle successful payment return.
     */
    public function success(Request $request, string $reference): View|RedirectResponse
    {
        $storedReference = session()->get('payment_reference');
        $gatewayId = session()->get('payment_gateway_id');

        // Verify reference matches
        if ($reference !== $storedReference) {
            return redirect()->route('cart.index')->with('error', 'Ungültige Zahlungsreferenz.');
        }

        // Verify payment with Payrexx
        if ($gatewayId && $this->payrexxService->isPaymentSuccessful($gatewayId)) {
            $cart = session()->get('payment_cart');

            // Clear cart and payment session data
            (new DestroyCartAction())->execute();
            session()->forget(['payment_reference', 'payment_gateway_id', 'payment_cart']);

            return view('pages.checkout.success', [
                'reference' => $reference,
                'cart' => $cart,
            ]);
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
        session()->forget(['payment_reference', 'payment_gateway_id', 'payment_cart']);

        return redirect()->route('checkout.summary')->with('error', 'Die Zahlung wurde abgebrochen. Bitte versuchen Sie es erneut.');
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
