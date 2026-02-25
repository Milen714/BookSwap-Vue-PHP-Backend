<?php /** @var \App\Views\View $this */ 

?>
<h2>Pay
    <?php echo isset($_SESSION['currentBookRequest']) ? $_SESSION['currentBookRequest']->shipping_cost : null; ?>
</h2>

<div id="checkout"></div>

<script>
const stripe = Stripe(
    'pk_test_51Sg6KdK2gTp4lSWgeXRs1IOOJnXABzgGD7OOQfiC90hmQPgLqI4XegSnvd2ykeu2LeJb6plpqMxL4ZmsH6vXvWf200YJ4JyDGd');

fetch('/create-checkout-session')
    .then(res => res.json())
    .then(data => {
        stripe.initEmbeddedCheckout({
            clientSecret: data.clientSecret
        }).then(checkout => {
            checkout.mount('#checkout');
        });
    });
</script>