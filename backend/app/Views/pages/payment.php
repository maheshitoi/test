<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Integration</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <div>Do not press button button. Payment is processing.....</div>
    <button id="rzp-button1" style="display:none;">Pay</button>

    <script>
        var options = {
            "key": "<?php echo $data['key']; ?>",
            "amount": "<?php echo $data['amount']; ?>", 
            "currency": "INR",
            "name": "IAOI",
            "description": "Test Transaction",
            "image": "<?php echo BASEURL ?>assets/img/IAOI_Logo_PNG.png",
            "order_id": "<?php echo $data['order_id']; ?>", 
            "callback_url": "<?php echo BASEURL . '/verifyPayment'; ?>",
            "prefill": {
                "name": "<?php echo $data['prefill']['name']; ?>",
                "email": "<?php echo $data['prefill']['email']; ?>",
                "contact": "<?php echo $data['prefill']['mobile_no']; ?>"
            },
            "notes": {
                "address": "IAOI",
                "payment_fk_id": "<?php echo $data['payment_fk_id']; ?>"
            },
            "theme": {
                "color": "#3399cc"
            }
        };

        var rzp1 = new Razorpay(options);
        document.getElementById('rzp-button1').onclick = function(e) {
            rzp1.open();
            e.preventDefault();
        }

        document.getElementById('rzp-button1').click();
    </script>
</body>
</html>
