<div>
    @if ($paymentStatus === 'success')
        <div class="alert alert-success">
            Payment successful!
        </div>
    @elseif ($paymentStatus === 'error')
        <div class="alert alert-danger">
            There was an error processing your payment. Please try again.
        </div>
    @else
        <form id="payment-form">
            <div id="card-element">
                <!-- A Stripe Element will be inserted here. -->
            </div>
            <button id="submit">Pay £10</button>
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

        const form = document.getElementById('payment-form');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    // Include any additional billing details you want to collect
                    name: 'Customer Name'
                },
            });

            if (error) {
                // Display error.message in your UI.
                document.getElementById('card-errors').textContent = error.message;
            } else {
                // Send the paymentMethod.id to your server.
                @this.set('paymentMethod', paymentMethod.id, () => {
                    @this.call('pay');
                });
            }
        });
    });
</script>
