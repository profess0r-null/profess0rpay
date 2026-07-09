# Profess0rPay API Documentation

Welcome to the official **Profess0rPay** Developer Documentation! This guide provides all the necessary details to seamlessly integrate Profess0rPay's payment gateway into your website, application, or software.

Our API is designed to be developer-friendly, secure, and fast. Follow the instructions below to start processing payments.

---

## ⚡ Quick Reference

- **Base URL**: `https://yourdomain.com` *(Replace with your actual Profess0rPay domain)*
- **Create Endpoint**: `POST /api/create-payment`
- **Verify Endpoint**: `POST /api/verify-payment`
- **Authentication**: `Authorization: Bearer <API_KEY>`
- **Content-Type**: `application/json`

---

## 🔐 Authentication

Every request made to the Profess0rPay API must be authenticated using your unique API Key. You can pass the API Key in the headers of your HTTP request.

| Header Method | Example Value | Status |
|---|---|---|
| **Preferred** | `Authorization: Bearer your_api_key_here` | Recommended |
| **Alternative** | `mhs-profess0rpay-api-key: your_api_key_here` | Supported |

> [!IMPORTANT]  
> Make sure your API key has the necessary **scopes** (e.g., `Create Payment`, `Verify Payment`) enabled in your merchant dashboard, otherwise the requests will fail.

---

## 📝 Common Rules

- Always set the header `Content-Type: application/json`.
- The `amount` must be greater than `0`.
- The domains you provide in `return_url` and `webhook_url` **must be whitelisted and active** in your Merchant Dashboard under the "Domains" section.
- `metadata` can be used to pass custom information (like Order ID) and it will be returned intact in the webhook and verify responses.

---

## 1️⃣ Create Payment

Generate a hosted checkout link for your customer.

**Endpoint:** `POST /api/create-payment`

### Request Parameters

| Field | Type | Required | Description |
|---|---|---|---|
| `full_name` | string | **Yes** | The full name of the customer. *(Alias: `customer_name`)* |
| `email_address` | string | **Yes** | Customer's valid email address. *(Alias: `customer_email`)* |
| `mobile_number` | string | **Yes** | Customer's mobile number. *(Alias: `customer_mobile`)* |
| `amount` | number | **Yes** | The exact invoice amount (must be positive). |
| `currency` | string | No | Currency code. Default is `BDT`. |
| `return_url` | string | **Yes** | URL to redirect the customer after payment. *(Alias: `redirect_url`, `cancel_url`)* |
| `webhook_url` | string | No | URL where the server-to-server callback will be sent. |
| `metadata` | object | No | Custom JSON object to track order data (e.g., `{"order_id": "123"}`). |

### Response Example

```json
{
  "status": true,
  "message": "Payment created successfully",
  "checkout_url": "https://yourdomain.com/payment/PAYMENT_ID"
}
```

> [!TIP]
> **Action:** Automatically redirect your customer to the `checkout_url` provided in the response.

### 💻 Code Examples: Create Payment

<details>
<summary><b>PHP (cURL)</b></summary>

```php
<?php
$payload = [
    "full_name" => "John Doe",
    "email_address" => "john@example.com",
    "mobile_number" => "01700000000",
    "amount" => 1200,
    "currency" => "BDT",
    "return_url" => "https://merchant.com/success",
    "webhook_url" => "https://merchant.com/api/webhook",
    "metadata" => [
        "order_id" => "ORD-1001",
        "customer_id" => "CUS-9001"
    ]
];

$ch = curl_init("https://yourdomain.com/api/create-payment");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer YOUR_API_KEY",
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
```
</details>

<details>
<summary><b>JavaScript (Fetch)</b></summary>

```javascript
const response = await fetch("https://yourdomain.com/api/create-payment", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Authorization": "Bearer YOUR_API_KEY"
  },
  body: JSON.stringify({
    full_name: "John Doe",
    email_address: "john@example.com",
    mobile_number: "01700000000",
    amount: 1200,
    return_url: "https://merchant.com/success",
    webhook_url: "https://merchant.com/api/webhook",
    metadata: {
      order_id: "ORD-1001"
    }
  }),
});

const data = await response.json();
console.log(data.checkout_url);
```
</details>

<details>
<summary><b>Python (Requests)</b></summary>

```python
import requests

response = requests.post(
    "https://yourdomain.com/api/create-payment",
    headers={
        "Content-Type": "application/json",
        "Authorization": "Bearer YOUR_API_KEY",
    },
    json={
        "full_name": "John Doe",
        "email_address": "john@example.com",
        "mobile_number": "01700000000",
        "amount": 1200,
        "return_url": "https://merchant.com/success",
        "metadata": {"order_id": "ORD-1001"}
    }
)

print(response.json())
```
</details>

---

## 2️⃣ Verify Payment

Securely check the status of a specific payment transaction from your backend.

**Endpoint:** `POST /api/verify-payment`

### Request Parameters

| Field | Type | Required | Description |
|---|---|---|---|
| `pp_id` | string | **Yes** | The Profess0rPay Reference ID (found at the end of the `checkout_url` or from the webhook payload). |

### Response Example

```json
{
  "pp_id": "REF123456789",
  "full_name": "John Doe",
  "email_address": "john@example.com",
  "mobile_number": "01700000000",
  "gateway": "bKash",
  "amount": 1200,
  "fee": 10,
  "discount_amount": 0,
  "total": 1190,
  "local_net_amount": 1190,
  "currency": "BDT",
  "local_currency": "BDT",
  "metadata": {
    "order_id": "ORD-1001"
  },
  "sender": "017XXXXXXXX",
  "transaction_id": "BKASH_TRX_999",
  "status": "completed",
  "date": "Jul 09, 2026 10:30 AM"
}
```

> [!WARNING]
> Only deliver the service/product if the `"status"` is exactly `"completed"` and the `"amount"` matches your database record.

---

## 3️⃣ Webhook (IPN) Integration

A webhook is an automatic server-to-server callback. When a customer successfully pays or a transaction is approved by the admin, Profess0rPay will send a `POST` request to your `webhook_url` instantly.

### Webhook Payload
The webhook receives the exact same JSON payload structure as the **Verify Payment** response. 

### Webhook Best Practices
1. **Always verify:** When your webhook script receives a payload, extract the `pp_id` and call the `/api/verify-payment` endpoint to securely confirm that the callback wasn't spoofed.
2. **Respond with 200 OK:** Your script must return an HTTP status code `200` to acknowledge receipt. If Profess0rPay does not receive a `200 OK` response, it will retry sending the webhook automatically!

### Example Webhook Script (PHP)

```php
<?php
// Receive JSON payload
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if ($data && isset($data['status'])) {
    
    // Check if the payment is completed
    if ($data['status'] === 'completed') {
        
        $order_id = $data['metadata']['order_id'] ?? null;
        $amount = $data['amount'];
        $transaction_id = $data['transaction_id'];
        
        // TODO: Look up $order_id in your database
        // TODO: Validate that the $amount matches the order total
        
        // Mark order as PAID and deliver the product!
        
        http_response_code(200);
        echo "Webhook processed successfully";
        exit;
    }
}

http_response_code(400);
echo "Bad Request";
?>
```

---

## 💡 Recommended Merchant Flow

1. **Create Order:** User initiates checkout on your website. Save the order as `PENDING` in your database.
2. **Create Invoice:** Call `POST /api/create-payment` with the user's details and your Order ID in the `metadata`.
3. **Redirect:** Send the user to the received `checkout_url`.
4. **Receive Webhook:** Profess0rPay sends a callback to your `webhook_url` when the user pays.
5. **Update Database:** In your webhook script, verify the status, update your order to `PAID`, and fulfill the service.
6. **Thank You Page:** The user is redirected back to your `return_url` to see a success message. (Do not run sensitive database updates on this page, rely on the webhook).

Happy Coding! 🚀
