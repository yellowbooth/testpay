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
            return;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $intent = PaymentIntent::create([
                'amount' => 1000, // £10 in cents
                'currency' => 'gbp',
                'payment_method' => $this->paymentMethod,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            $this->paymentStatus = 'success';
        } catch (\Exception $e) {
            $this->paymentStatus = 'error';
        }
    }

    public function render()
    {
        return view('livewire.adhoc-payment-form')->layout('components.layouts.app');
    }
}
