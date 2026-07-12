<?php
// Simple mail test script for Profess0rPay users
$to = $_GET['to'] ?? '';
$from = $_GET['from'] ?? '';

if(empty($to) || empty($from)) {
    die("Please provide 'to' and 'from' parameters. Example: ?to=your_email@gmail.com&from=contact@profess0r-null.xyz");
}

$subject = "Profess0rPay Debug Test Email";
$message = "This is a test email from the debug script.";
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: Profess0rPay <$from>\r\n";

echo "Attempting to send email...<br>";
echo "To: $to<br>";
echo "From: $from<br>";

$result = @mail($to, $subject, $message, $headers, "-f $from");

if($result) {
    echo "<h3 style='color:green;'>SUCCESS! PHP mail() returned TRUE.</h3>";
} else {
    echo "<h3 style='color:red;'>FAILED! PHP mail() returned FALSE.</h3>";
    echo "<pre>";
    print_r(error_get_last());
    echo "</pre>";
}
