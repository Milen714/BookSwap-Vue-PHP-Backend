// This is your test publishable API key.
const stripe = Stripe("pk_test_51Sg6KdK2gTp4lSWgeXRs1IOOJnXABzgGD7OOQfiC90hmQPgLqI4XegSnvd2ykeu2LeJb6plpqMxL4ZmsH6vXvWf200YJ4JyDGd");

initialize();

// Create a Checkout Session
async function initialize() {
  const fetchClientSecret = async () => {
    const response = await fetch("/checkout.php", {
      method: "POST",
    });
    const { clientSecret } = await response.json();
    return clientSecret;
  };

  const checkout = await stripe.initEmbeddedCheckout({
    fetchClientSecret,
  });

  // Mount Checkout
  checkout.mount('#checkout');
}