<?php
/**
 * Ikusasa Technologies — Contact Form Email Handler
 * Sends a professionally styled HTML email to the recipient
 */

// ============================================
// CONFIGURATION
// ============================================
$recipient_email = 'deven@ikusasatech.co.za';
$from_email      = 'noreply@ikusasatech.co.za';
$from_name       = 'Ikusasa Technologies Website';
$site_url        = 'https://www.ikusasatech.co.za';

// ============================================
// HEADERS
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://www.ikusasatech.co.za');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// ============================================
// HONEYPOT SPAM CHECK
// ============================================
if (!empty($_POST['website'])) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Thank you for your message']);
    exit();
}

// ============================================
// SANITIZE INPUT
// ============================================
$name    = isset($_POST['fullName'])  ? htmlspecialchars(trim($_POST['fullName']),  ENT_QUOTES, 'UTF-8') : '';
$company = isset($_POST['company'])   ? htmlspecialchars(trim($_POST['company']),   ENT_QUOTES, 'UTF-8') : '';
$email   = isset($_POST['email'])     ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$phone   = isset($_POST['phone'])     ? htmlspecialchars(trim($_POST['phone']),     ENT_QUOTES, 'UTF-8') : '';
$service = isset($_POST['service'])   ? htmlspecialchars(trim($_POST['service']),   ENT_QUOTES, 'UTF-8') : '';
$message = isset($_POST['message'])   ? htmlspecialchars(trim($_POST['message']),   ENT_QUOTES, 'UTF-8') : '';

// ============================================
// VALIDATION
// ============================================
$errors = [];

if (strlen($name) < 2)                          $errors[] = 'Please enter your full name.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
if (strlen($message) < 10)                      $errors[] = 'Message is too short.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit();
}

// ============================================
// SERVICE LABEL
// ============================================
$service_labels = [
    'managed-it'     => 'Managed IT Services & Support',
    'cybersecurity'  => 'Cybersecurity',
    'cloud'          => 'Cloud Solutions',
    'consulting'     => 'IT Consulting',
    'procurement'    => 'Hardware & Software Procurement',
    'data-protection'=> 'Data Protection',
    'networking'     => 'Networking / Cabling / CCTV',
    'multiple'       => 'Multiple Services',
    'other'          => 'Other / Not Sure',
];
$service_label = !empty($service) && isset($service_labels[$service]) ? $service_labels[$service] : ($service ?: 'Not specified');

// ============================================
// HTML EMAIL TEMPLATE
// ============================================
$subject = "New Enquiry from {$name}" . ($company ? " ({$company})" : '');

$html = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Enquiry — Ikusasa Technologies</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Arial,sans-serif;">
<table role="presentation" style="width:100%;background:#f4f4f5;padding:40px 20px;">
  <tr><td align="center">
    <table role="presentation" style="width:100%;max-width:600px;">

      <!-- HEADER -->
      <tr>
        <td style="background:#111111;padding:36px 32px;text-align:center;border-radius:12px 12px 0 0;">
          <h1 style="margin:0;color:#fff;font-size:24px;font-weight:800;letter-spacing:-0.5px;">
            Ikusasa <span style="color:#E8731A;">Technologies</span>
          </h1>
          <p style="margin:8px 0 0;color:#888;font-size:13px;">New Website Enquiry</p>
        </td>
      </tr>

      <!-- ALERT BANNER -->
      <tr>
        <td style="background:#fff;padding:32px 32px 0;">
          <table role="presentation" style="width:100%;">
            <tr>
              <td style="background:#E8731A;padding:18px 24px;border-radius:8px;text-align:center;">
                <p style="margin:0;color:#fff;font-size:15px;font-weight:600;">
                  You have received a new enquiry from your website!
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- CONTACT DETAILS -->
      <tr>
        <td style="background:#fff;padding:24px 32px 0;">
          <table role="presentation" style="width:100%;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:0;">
            <tr><td style="padding:24px;">

              <!-- Name -->
              <table role="presentation" style="width:100%;margin-bottom:18px;">
                <tr>
                  <td style="width:44px;vertical-align:top;">
                    <div style="width:38px;height:38px;background:#E8731A;border-radius:8px;display:flex;align-items:center;justify-content:center;text-align:center;line-height:38px;color:#fff;font-size:16px;">N</div>
                  </td>
                  <td style="vertical-align:top;padding-left:14px;">
                    <p style="margin:0 0 3px;color:#888;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Full Name</p>
                    <p style="margin:0;color:#111;font-size:15px;font-weight:500;">' . $name . '</p>
                  </td>
                </tr>
              </table>

              ' . ($company ? '
              <!-- Company -->
              <table role="presentation" style="width:100%;margin-bottom:18px;">
                <tr>
                  <td style="width:44px;vertical-align:top;">
                    <div style="width:38px;height:38px;background:#E8731A;border-radius:8px;text-align:center;line-height:38px;color:#fff;font-size:16px;">C</div>
                  </td>
                  <td style="vertical-align:top;padding-left:14px;">
                    <p style="margin:0 0 3px;color:#888;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Company</p>
                    <p style="margin:0;color:#111;font-size:15px;font-weight:500;">' . $company . '</p>
                  </td>
                </tr>
              </table>' : '') . '

              <!-- Email -->
              <table role="presentation" style="width:100%;margin-bottom:18px;">
                <tr>
                  <td style="width:44px;vertical-align:top;">
                    <div style="width:38px;height:38px;background:#E8731A;border-radius:8px;text-align:center;line-height:38px;color:#fff;font-size:16px;">@</div>
                  </td>
                  <td style="vertical-align:top;padding-left:14px;">
                    <p style="margin:0 0 3px;color:#888;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Email</p>
                    <a href="mailto:' . $email . '" style="color:#E8731A;font-size:15px;font-weight:500;text-decoration:none;">' . $email . '</a>
                  </td>
                </tr>
              </table>

              ' . ($phone ? '
              <!-- Phone -->
              <table role="presentation" style="width:100%;margin-bottom:18px;">
                <tr>
                  <td style="width:44px;vertical-align:top;">
                    <div style="width:38px;height:38px;background:#E8731A;border-radius:8px;text-align:center;line-height:38px;color:#fff;font-size:16px;">T</div>
                  </td>
                  <td style="vertical-align:top;padding-left:14px;">
                    <p style="margin:0 0 3px;color:#888;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Phone</p>
                    <p style="margin:0;color:#111;font-size:15px;font-weight:500;">' . $phone . '</p>
                  </td>
                </tr>
              </table>' : '') . '

              <!-- Service -->
              <table role="presentation" style="width:100%;">
                <tr>
                  <td style="width:44px;vertical-align:top;">
                    <div style="width:38px;height:38px;background:#E8731A;border-radius:8px;text-align:center;line-height:38px;color:#fff;font-size:16px;">S</div>
                  </td>
                  <td style="vertical-align:top;padding-left:14px;">
                    <p style="margin:0 0 3px;color:#888;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Service of Interest</p>
                    <p style="margin:0;color:#111;font-size:15px;font-weight:500;">' . $service_label . '</p>
                  </td>
                </tr>
              </table>

            </td></tr>
          </table>
        </td>
      </tr>

      <!-- MESSAGE -->
      <tr>
        <td style="background:#fff;padding:24px 32px 0;">
          <p style="margin:0 0 10px;color:#888;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Message</p>
          <div style="background:#f9fafb;border:1px solid #e5e7eb;border-left:4px solid #E8731A;border-radius:0 8px 8px 0;padding:20px;">
            <p style="margin:0;color:#374151;font-size:14px;line-height:1.75;white-space:pre-wrap;">' . nl2br($message) . '</p>
          </div>
        </td>
      </tr>

      <!-- REPLY BUTTON -->
      <tr>
        <td style="background:#fff;padding:28px 32px 32px;text-align:center;">
          <a href="mailto:' . $email . '?subject=Re: Your enquiry to Ikusasa Technologies"
             style="display:inline-block;background:#E8731A;color:#fff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;font-weight:700;">
            Reply to ' . $name . '
          </a>
        </td>
      </tr>

      <!-- FOOTER -->
      <tr>
        <td style="background:#111;padding:28px 32px;text-align:center;border-radius:0 0 12px 12px;">
          <p style="margin:0 0 8px;color:#888;font-size:12px;">This email was sent from the contact form at ikusasatech.co.za</p>
          <p style="margin:0 0 12px;color:#555;font-size:12px;">Ikusasa Technologies | KwaZulu-Natal, South Africa</p>
          <a href="' . $site_url . '" style="color:#E8731A;text-decoration:none;font-size:13px;">www.ikusasatech.co.za</a>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>';

// ============================================
// SEND EMAIL
// ============================================
$headers = implode("\r\n", [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . $from_name . ' <' . $from_email . '>',
    'Reply-To: ' . $name . ' <' . $email . '>',
    'X-Mailer: PHP/' . phpversion(),
    'X-Priority: 1',
    'Importance: High',
]);

$sent = mail($recipient_email, $subject, $html, $headers);

if ($sent) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send. Please call us directly on 083 293 2025.']);
}
?>
