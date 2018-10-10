var stripeApiKey = document.getElementById('stripeApiKey').value;
var stripe = Stripe(stripeApiKey);
var elements = stripe.elements({locale: 'fr'});

var style = {
    base: {
        fontSize: '16px',
        color: "#32325d",
    }
};

// CARD NUMBER

var cardNumber = elements.create('cardNumber', {style: style});
cardNumber.mount('#cardNumber-element');

cardNumber.addEventListener('change', function(event) {
    var displayError = document.getElementById('cardNumber-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// EXPIRATION

var cardExpiry = elements.create('cardExpiry', {style: style});
cardExpiry.mount('#cardExpiry-element');

cardExpiry.addEventListener('change', function(event) {
    var displayError = document.getElementById('cardExpiry-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// CRYPTOGRAMME

var cardCvc = elements.create('cardCvc', {style: style});
cardCvc.mount('#cardCvc-element');

cardCvc.addEventListener('change', function(event) {
    var displayError = document.getElementById('cardCvc-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// SUBMIT

var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
    event.preventDefault();
    stripe.createToken(cardNumber).then(function(result) {
        if (!document.getElementById('cgu').checked) {
            return false;
        }
        if (result.error == null) {
            stripeTokenHandler(result.token);
        }
    });
});

function stripeTokenHandler(token) {
    var tokenId = document.getElementById('tokenId');
    tokenId.setAttribute('value', token.id);

    // Submit the form
    //console.log(token.id);
    form.submit();
}