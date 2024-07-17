<?php
namespace App\Livewire;

use Livewire\Component;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Subscription;

class SubscriptionForm extends Component
{
    public $name;
    public $email;
    public $paymentMethod;

    public function mount()
    {
        Stripe::setApiKey(config('services.stripe.secret')); // Ensure this line is present
    }

    public function subscribe()
    {
        $customer = Customer::create([
            'email' => $this->email,
            'name' => $this->name,
        ]);

        $paymentMethod = PaymentMethod::retrieve($this->paymentMethod);
        $paymentMethod->attach(['customer' => $customer->id]);

        Subscription::create([
            'customer' => $customer->id,
            'items' => [['price' => 'your_stripe_price_id']],
            'default_payment_method' => $paymentMethod->id,
        ]);

        session()->flash('message', 'Subscription created successfully!');
    }

    public function render()
    {
        return view('livewire.subscription-form')->layout('components.layouts.app');
    }
}
