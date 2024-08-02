<!DOCTYPE html>
<html>
<head>
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<form id="setup-form">
    <input type="hidden" id="customer-id" value="{{ $customer->id }}">
    <input type="hidden" id="setup-intent-client-secret" value="{{ $setupIntent->client_secret }}">
    <div>
        <label for="card-element">Card Details</label>
        <div id="card-element"></div>
    </div>
    <button type="submit">Save Payment Method</button>
</form>

<div id="charge-form" style="display: none;">
    <input type="number" id="amount" placeholder="Amount in cents">
    <button id="charge-button">Charge Customer</button>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const setupForm = document.getElementById('setup-form');
        setupForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const clientSecret = document.getElementById('setup-intent-client-secret').value;
            const customerId = document.getElementById('customer-id').value;

            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: cardElement,
                    },
                }
            );

            if (error) {
                console.error(error);
            } else {
                document.getElementById('charge-form').style.display = 'block';
                const chargeButton = document.getElementById('charge-button');

                chargeButton.addEventListener('click', () => {
                    const amount = document.getElementById('amount').value;
                    const paymentMethodId = setupIntent.payment_method;

                    fetch(`/charge-customer?customer_id=${customerId}&payment_method_id=${paymentMethodId}&amount=${amount}`, {
                        method: 'GET',
                    })
                        .then(response => response.json())
                        .then(data => console.log(data));
                });
            }
        });
    });
</script>
</body>
</html>
