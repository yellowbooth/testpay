<div>
    @if ($paymentStatus === 'success')
        <div class="alert alert-success">
            Subscription successful!
        </div>
    @elseif ($paymentStatus === 'error')
        <div class="alert alert-danger">
            There was an error processing your subscription. Please try again.
        </div>
        <div>
            <p><strong>Error Details:</strong></p>
            <p>{{ session('error_message') }}</p>
        </div>
    @else
        <form id="subscription-form">
            <input type="text" wire:model="name" placeholder="Name">
            <input type="email" wire:model="email" placeholder="Email">
            <div id="card-element">
                <!-- A Stripe Element will be inserted here. -->
            </div>
            <button id="submit">Subscribe</button>
            <div id="card-errors" role="alert"></div>
        </form>
    @endif
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const form = document.getElementById('subscription-form');
        const nameInput = document.querySelector('input[wire\\:model="name"]');
        const emailInput = document.querySelector('input[wire\\:model="email"]');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            console.log('Form submitted');

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: nameInput.value,
                    email: emailInput.value,
                },
            });

            if (error) {
                console.log('Error creating payment method:', error);
                // Display error.message in your UI.
                document.getElementById('card-errors').textContent = error.message;
            } else {
                console.log('Payment method created:', paymentMethod.id);
                // Send the paymentMethod.id to your server.
                @this.set('paymentMethod', paymentMethod.id);
                @this.call('subscribe');
            }
        });
    });
</script>
