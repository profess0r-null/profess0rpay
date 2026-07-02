# Profess0rPay API Documentation

Welcome to the Profess0rPay API documentation. This guide will help you integrate Profess0rPay into your eCommerce website or application.

## Overview
Profess0rPay allows you to easily accept payments through bKash, Nagad, Rocket, and other methods. The integration consists of two main parts:
1. **Creating a Payment Request**: Redirecting your user to the Profess0rPay checkout page.
2. **Webhook Listener (IPN)**: Receiving a secure server-to-server callback when the payment is completed.

---

## 1. Create a Payment

To initiate a payment, you need to send a `POST` request to the Profess0rPay API endpoint.

**Endpoint:**
`POST /api/create-payment` (Replace with your actual API endpoint URL)

**Headers:**
```json
{
  "Content-Type": "application/json",
  "Authorization": "Bearer YOUR_API_KEY"
}
```

**Payload Example:**
```json
{
  "amount": 500,
  "currency": "BDT",
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_mobile": "01700000000",
  "return_url": "https://yourwebsite.com/success.php",
  "webhook_url": "https://yourwebsite.com/webhook.php",
  "metadata": {
    "order_id": "ORD-12345"
  }
}
```

**Response Example:**
```json
{
  "status": true,
  "message": "Payment created successfully",
  "checkout_url": "https://profess0rpay.com/checkout/xxxxx"
}
```
**Action:** Redirect your customer to the `checkout_url`.

---

## 2. Webhook Listener (IPN)

Once the user completes the payment, or if the admin approves the payment, Profess0rPay will send a `POST` request to the `webhook_url` you provided.

**Method:** `POST`
**Content-Type:** `application/json`

**Payload Example Received by Your Server:**
```json
{
  "pp_id": "893748937498",
  "status": "completed",
  "amount": 500,
  "fee": 5,
  "total": 495,
  "metadata": {
    "order_id": "ORD-12345"
  },
  "transaction_id": "BKASH12345",
  "date": "Oct 25, 2023 10:30 AM"
}
```

### Example Webhook Implementation (PHP)
Save this code in your `webhook.php` file:

```php
<?php
// Receive JSON payload
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if ($data && isset($data['status'])) {
    // Check if payment is successful
    if ($data['status'] === 'completed') {
        $order_id = $data['metadata']['order_id'] ?? null;
        $amount = $data['amount'] ?? 0;
        $transaction_id = $data['transaction_id'] ?? '';

        // TODO: Validate order_id and amount with your database
        // TODO: Update your database (e.g., mark order as PAID)
        // TODO: Deliver the product to the customer

        http_response_code(200);
        echo "OK";
        exit;
    }
}

http_response_code(400);
echo "Bad Request";
?>
```

---

## 3. Success Page (Return URL)

After payment, the user will be redirected to your `return_url` (e.g., `success.php`). 
**Note:** Do not update your database from the success page. Only use this page to show a "Thank You" message to the user by checking the updated status in your database (which was updated by the Webhook).
