<div>
    <form wire:submit.prevent="subscribe" id="payment-form">
        <input type="text" wire:model="name" placeholder="Name">
        <input type="email" wire:model="email" placeholder="Email">
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>
        <button id="submit">Subscribe</button>
        <div id="card-errors" role="alert"></div>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod(
                'card', cardElement, {
                    billing_details: { name: document.querySelector('input[wire\\:model="name"]').value }
                }
            );

            if (error) {
                // Display error.message in your UI.
                document.getElementById('card-errors').textContent = error.message;
            } else {
                // Send the paymentMethod.id to your server.
                @this.set('paymentMethod', paymentMethod.id);
            }
        });
    });

    Livewire.on('livewire:load', function () {
        // Place any additional initialization or event handling code here if needed.
    });
</script>
