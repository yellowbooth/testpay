<?php
namespace App\Livewire;

use Livewire\Component;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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
        if (!$this->paymentMethod) {
            $this->paymentStatus = 'error';
            session()->flash('error_message', 'Payment method not provided.');
            return;
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => 1002, // £10 in cents
                'currency' => 'gbp',
                'payment_method' => $this->paymentMethod,
                // 'confirmation_method' => 'manual', // Commented out to avoid the error
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
