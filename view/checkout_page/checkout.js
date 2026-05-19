
document.addEventListener('DOMContentLoaded', function() {
    loadOrderSummary();

    
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();

        var address        = document.getElementById('address').value.trim();
        var payment_method = document.getElementById('payment_method').value;

        
        if(address === '') {
            alert('Please enter your delivery address.');
            return;
        }

        if(payment_method === '') {
            alert('Please select a payment method.');
            return;
        }

        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../../control/checkout_control.php?chk_place_order=true', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        handleResponse(xhr, function() {
            var response = JSON.parse(xhr.responseText);

            if(response.success) {
                alert('Order placed successfully! Order ID: #' + response.order_id);
                window.location.href = '../home_page/customer_homepage.html';
            } else {
                alert(response.message || 'Order failed. Please try again.');
            }
        }, 'Order failed: ');

        xhr.send(
            'address='         + encodeURIComponent(address) +
            '&payment_method=' + encodeURIComponent(payment_method)
        );
    });
});


function handleResponse(obj, successfullCallback, errorMessage) {
    obj.onreadystatechange = function() {
        if(obj.readyState == 4 && obj.status == 200) {
            successfullCallback();
        } else if(obj.readyState == 4) {
            console.error(errorMessage + obj.status);
        }
    };
}


function loadOrderSummary() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../../control/customer_homepage_control.php?chk_fetch_cart=true', true);

    handleResponse(xhr, function() {
        var response  = JSON.parse(xhr.responseText);
        var itemsList = document.getElementById('checkout-items-list');
        var totalElem = document.getElementById('checkout-total-price');

        itemsList.innerHTML = '';
        var total = 0;

        if(response.length === 0) {
            itemsList.innerHTML = '<p style="color:#7f8c8d;text-align:center;padding:15px 0;">Your cart is empty.</p>';
            totalElem.innerHTML = '$0.00';
            return;
        }

        response.forEach(function(item) {
            var qty      = parseInt(item['quantity']) || 1;
            var price    = parseFloat(item['price']);
            var subtotal = price * qty;
            total += subtotal;

            itemsList.innerHTML +=
                '<div class="summary-item">'
                + '<div>'
                + '<div class="summary-item-name">' + item['title'] + '</div>'
                + '<div class="summary-item-qty">Qty: ' + qty + ' × $' + price.toFixed(2) + '</div>'
                + '</div>'
                + '<div class="summary-item-price">$' + subtotal.toFixed(2) + '</div>'
                + '</div>';
        });

        totalElem.innerHTML = '$' + total.toFixed(2);

        window.cartItems = response;
        window.cartTotal = total;

    }, 'Error loading cart: ');

    xhr.send();
}