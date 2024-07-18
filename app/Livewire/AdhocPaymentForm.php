<?php
namespace App\Livewire;

use Livewire\Component;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Auth;

class AdhocPaymentForm extends Component
{
    public $paymentMethod;
    public $paymentStatus = '';

    public function mount()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function pay()
    {
        $user = Auth::user();
        $customerId = $user->stripe_customer_id;

        if (!$this->paymentMethod || !$customerId) {
            $this->paymentStatus = 'error';
            session()->flash('error_message', 'Payment method or customer ID not provided.');
            return;
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => 1000, // £10 in cents
                'currency' => 'gbp',
                'customer' => $customerId, // Associate with the customer
                'payment_method' => $this->paymentMethod,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // Disable redirects
                ],
            ]);

            $this->paymentStatus = 'success';
        } catch (\Exception $e) {
            $this->paymentStatus = 'error';
            session()->flash('error_message', $e->getMessage());
            \Log::error('Payment Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.adhoc-payment-form')->layout('components.layouts.app');
    }
}
