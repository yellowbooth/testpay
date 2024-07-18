<?php
namespace App\Livewire;

use Livewire\Component;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Subscription;
use App\Models\User;
use Auth;

class SubscriptionForm extends Component
{
    public $name;
    public $email;
    public $paymentMethod;
    public $paymentStatus = '';

    public function mount()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function subscribe()
    {
        if (!$this->paymentMethod) {
            $this->paymentStatus = 'error';
            session()->flash('error_message', 'Payment method not provided.');
            return;
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $customer = Customer::create([
                'email' => $this->email,
                'name' => $this->name,
            ]);

            $paymentMethod = PaymentMethod::retrieve($this->paymentMethod);
            $paymentMethod->attach(['customer' => $customer->id]);

            Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => 'price_1PcAA0FAg1veKjiGhT8jVY5H']],
                'default_payment_method' => $paymentMethod->id,
            ]);

            // Store the Stripe customer ID in the users table
            $user = auth::user();
            $user->stripe_customer_id = $customer->id;
            $user->save();

            $this->paymentStatus = 'success';
        } catch (\Exception $e) {
            $this->paymentStatus = 'error';
            session()->flash('error_message', $e->getMessage());
            \Log::error('Subscription Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.subscription-form')->layout('components.layouts.app');
    }
}
