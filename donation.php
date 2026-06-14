<?php
require_once __DIR__ . '/includes/functions.php';
verify_csrf();
page_title('Donation');

function donation_column_exists(string $column): bool
{
    return (bool) scalar("SHOW COLUMNS FROM donations LIKE ?", [$column], false);
}

function ensure_donation_schema(): void
{
    if (!table_exists('donations')) {
        return;
    }
    $columns = [
        'transaction_reference' => "ALTER TABLE donations ADD transaction_reference VARCHAR(120) NULL AFTER payment_method",
        'receipt_path' => "ALTER TABLE donations ADD receipt_path VARCHAR(255) NULL AFTER transaction_reference",
        'tracking_code' => "ALTER TABLE donations ADD tracking_code VARCHAR(40) NULL UNIQUE AFTER receipt_path",
        'rejection_reason' => "ALTER TABLE donations ADD rejection_reason TEXT NULL AFTER status",
        'email_verified_at' => "ALTER TABLE donations ADD email_verified_at TIMESTAMP NULL AFTER rejection_reason",
        'verified_at' => "ALTER TABLE donations ADD verified_at TIMESTAMP NULL AFTER email_verified_at",
    ];
    foreach ($columns as $column => $sql) {
        if (!donation_column_exists($column)) {
            execute($sql);
        }
    }
}

function next_tracking_code(): string
{
    $year = date('Y');
    $lastId = (int) scalar('SELECT COALESCE(MAX(id), 0) FROM donations', [], 0);
    return sprintf('NGO-%s-%04d', $year, $lastId + 1);
}

function donation_old(): array
{
    return [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'contact_number' => trim($_POST['contact_number'] ?? ''),
        'amount' => trim($_POST['amount'] ?? ''),
        'payment_method' => trim($_POST['payment_method'] ?? 'GCash'),
        'transaction_reference' => trim($_POST['transaction_reference'] ?? ''),
        'donation_message' => trim($_POST['donation_message'] ?? ''),
    ];
}

ensure_donation_schema();

$errors = [];
$success = null;
$trackingCode = null;
$old = donation_old();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'submit_donation';

    if (!empty($_POST['website'])) {
        $errors[] = 'Submission blocked by anti-spam protection.';
    }

    if ($action === 'submit_donation' && !$errors) {
        if ($old['full_name'] === '') {
            $errors[] = 'Full name is required.';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if ($old['contact_number'] === '') {
            $errors[] = 'Contact number is required.';
        }
        if ((float) $old['amount'] <= 0) {
            $errors[] = 'Donation amount must be greater than zero.';
        }
        if (!in_array($old['payment_method'], ['GCash', 'MariBank', 'Bank Transfer', 'Other'], true)) {
            $errors[] = 'Choose a valid payment method.';
        }
        if ($old['transaction_reference'] === '') {
            $errors[] = 'Transaction reference number is required.';
        }
        if (empty($_FILES['receipt']['name']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload one receipt image.';
        }

        $receiptPath = null;
        if (!$errors) {
            $maxSize = 5 * 1024 * 1024;
            $receipt = $_FILES['receipt'];
            $extension = strtolower(pathinfo($receipt['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'jfif'];
            $imageInfo = @getimagesize($receipt['tmp_name']);

            if (!$imageInfo || !in_array($extension, $allowed, true)) {
                $errors[] = 'Receipt must be a valid image file (JPG, JPEG, PNG, GIF, WEBP, BMP, JFIF).';
            } elseif ($receipt['size'] > $maxSize) {
                $errors[] = 'Receipt image must be 5MB or smaller.';
            } else {
                $folder = __DIR__ . '/uploads/donations';
                if (!is_dir($folder)) {
                    mkdir($folder, 0775, true);
                }
                $fileName = uniqid('receipt_', true) . '.' . $extension;
                $target = $folder . '/' . $fileName;
                if (move_uploaded_file($receipt['tmp_name'], $target)) {
                    $receiptPath = 'uploads/donations/' . $fileName;
                } else {
                    $errors[] = 'Receipt upload failed. Please try again.';
                }
            }
        }

        if (!$errors && $receiptPath) {
            $programId = (int) ($_POST['program_id'] ?? 0);
            $trackingCode = next_tracking_code();
            $saved = execute('INSERT INTO donations (full_name, email, contact_number, amount, donation_message, program_id, payment_method, transaction_reference, receipt_path, tracking_code, status, email_verified_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())', [
                $old['full_name'],
                $old['email'],
                $old['contact_number'],
                (float) $old['amount'],
                $old['donation_message'],
                $programId ?: null,
                $old['payment_method'],
                $old['transaction_reference'],
                $receiptPath,
                $trackingCode,
                'Pending Verification',
            ]);
            if ($saved) {
                $programName = get_program_name_by_id($programId);
                $receiptData = [
                    'full_name' => $old['full_name'],
                    'email' => $old['email'],
                    'program_name' => $programName,
                    'amount' => (float) $old['amount'],
                    'payment_method' => $old['payment_method'],
                    'transaction_reference' => $old['transaction_reference'],
                    'tracking_code' => $trackingCode,
                    'donation_date' => date('F j, Y g:i A'),
                ];
                $receiptImage = create_donation_receipt_image($receiptData);
                if ($receiptImage) {
                    $receiptSent = send_email_with_attachment(
                        $old['email'],
                        'Your Helping Hands NGO Donation Receipt',
                        "Dear {$old['full_name']},\n\nThank you for your donation to {$programName}. Your donation is recorded and a receipt image is attached to this email.\n\nDonation reference: {$old['transaction_reference']}\nAmount: PHP " . number_format((float) $old['amount'], 2) . "\nDate: " . $receiptData['donation_date'] . "\n\nWe appreciate your support.\n\n" . APP_NAME,
                        $receiptImage,
                        'donation-receipt-' . preg_replace('/[^A-Za-z0-9_-]/', '-', $trackingCode) . '.png'
                    );

                    sendReceiptEmail(
                        $old['email'],
                        $old['full_name'],
                        $programName,
                        $old['payment_method'],
                        $old['transaction_reference'],
                        'PHP ' . number_format((float)$old['amount'], 2)
                    );

                    @unlink($receiptImage);

                    if (!$receiptSent) {
                        $errors[] = 'Donation was saved, but the receipt email could not be sent. Please contact support.';
                    }
                } else {
                    $errors[] = 'Donation was saved, but the receipt image could not be generated.';
                }

                if (!$errors) {
                    $success = 'Congratulations! Your donation is valid and submitted for verification. Your tracking code is ' . $trackingCode . '.';
                    $old = [
                        'full_name' => '',
                        'email' => '',
                        'contact_number' => '',
                        'amount' => '',
                        'payment_method' => 'GCash',
                        'transaction_reference' => '',
                        'donation_message' => '',
                    ];
                }
            } else {
                $errors[] = 'Donation could not be saved. Please check the database connection.';
            }
        }
    }
}

$programs = programs();
$selected = (int) ($_GET['program'] ?? 0);
require __DIR__ . '/includes/header.php';
?>
<section class="donation-hero">
    <div class="container text-center">
        <p class="eyebrow">Secure Donation</p>
        <h1>Support Our Mission</h1>
        <p class="lead mx-auto">Your donation helps fund education, feeding programs, disaster response, medical assistance, and community projects. Every contribution is reviewed before official receipts are issued.</p>
    </div>
</section>

<section class="section-pad donation-page">
    <div class="container">
        <?php if ($success): ?><div class="alert alert-success donation-alert mx-auto"><?= e($success) ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-danger donation-alert mx-auto"><strong>Please review:</strong><ul class="mb-0 mt-2"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

        <form class="donation-card" method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="text" name="website" class="anti-spam-field" tabindex="-1" autocomplete="off">
            <div class="row g-0">
                <div class="col-lg-7 donation-panel">
                    <div class="donation-panel-header">
                        <span class="step-badge">1</span>
                        <div>
                            <p class="eyebrow mb-1">Donation Information</p>
                            <h2>Tell us about your contribution</h2>
                        </div>
                    </div>
                    <div class="secure-note"><i class="bi bi-shield-lock"></i><span>Donations are recorded as Pending Verification until the admin confirms the payment was received.</span></div>
                    <div class="inline-process">
                        <p class="eyebrow mb-2">What happens next</p>
                        <div><span>1</span><p>Save or scan the QR, then pay using your selected method.</p></div>
                        <div><span>2</span><p>Upload a clear receipt image with the reference number.</p></div>
                        <div><span>3</span><p>Admin verifies the payment before issuing receipts or thank-you messages.</p></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Full Name</label><input class="form-control" name="full_name" value="<?= e($old['full_name']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Email Address</label><input class="form-control" type="email" name="email" value="<?= e($old['email']) ?>" required></div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input class="form-control" name="contact_number" value="<?= e($old['contact_number']) ?>" required>
                        </div>
                        <div class="col-md-6"><label class="form-label">Donation Amount</label><input class="form-control" type="text" inputmode="decimal" name="amount" value="<?= e($old['amount']) ?>" placeholder="Enter amount" required></div>
                        <div class="col-md-6"><label class="form-label">Payment Method</label><select class="form-select" name="payment_method" required><?php foreach (['GCash', 'MariBank', 'Bank Transfer', 'Other'] as $method): ?><option <?= $old['payment_method'] === $method ? 'selected' : '' ?>><?= e($method) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-6"><label class="form-label">Select Program</label><select class="form-select" name="program_id"><?php foreach ($programs as $program): ?><option value="<?= (int)$program['id'] ?>" <?= $selected === (int)$program['id'] ? 'selected' : '' ?>><?= e($program['name']) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-6"><label class="form-label">Transaction Reference Number</label><input class="form-control" name="transaction_reference" value="<?= e($old['transaction_reference']) ?>" required></div>
                        <div class="col-12"><label class="form-label">Donation Message <span class="text-secondary">(Optional)</span></label><textarea class="form-control" name="donation_message" rows="4"><?= e($old['donation_message']) ?></textarea></div>
                    </div>
                </div>
                <div class="col-lg-5 donation-panel donation-upload-panel">
                    <div class="donation-panel-header">
                        <span class="step-badge">2</span>
                        <div>
                            <p class="eyebrow mb-1">Proof of Donation</p>
                            <h2>Upload receipt image</h2>
                        </div>
                    </div>
                    <label class="receipt-drop" for="receiptUpload">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <strong>Choose one receipt image</strong>
                        <span>Any image file is okay (JPG, JPEG, PNG, GIF, WEBP, BMP). Maximum 5MB.</span>
                        <input id="receiptUpload" type="file" name="receipt" accept="image/*" required>
                    </label>
                    <div class="receipt-preview" id="receiptPreview">
                        <span>Image preview will appear here.</span>
                    </div>
                    <div class="verification-list">
                        <h3>Receipt should clearly show</h3>
                        <p>Donor name, amount, payment method, reference number, and transaction details.</p>
                    </div>
                    <div class="privacy-inline">
                        <i class="bi bi-lock"></i>
                        <p>Only verified donors receive receipts, certificates, and appreciation messages. Uploaded receipts are for admin review only.</p>
                    </div>
                    <div class="payment-qr-panel">
                        <h3>Pay with QR</h3>
                        <p>Save the QR image, open your payment app, then upload your transaction receipt here.</p>
                        <div class="payment-qr-grid">
                            <div class="payment-qr-card">
                                <img src="<?= url(setting_value('gcash_qr', 'assets/images/gcash-qr.jfif')) ?>" alt="GCash QR code for Helping Hands NGO donations">
                                <strong>GCash</strong>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-sm btn-outline-primary qr-view-button" type="button" data-qr-src="<?= url(setting_value('gcash_qr', 'assets/images/gcash-qr.jfif')) ?>" data-qr-title="GCash QR Code"><i class="bi bi-zoom-in me-1"></i>View Full Size</button>
                                    <a class="btn btn-sm btn-primary" href="<?= url(setting_value('gcash_qr', 'assets/images/gcash-qr.jfif')) ?>" download="helping-hands-gcash-qr"><i class="bi bi-download me-1"></i>Save QR</a>
                                </div>
                            </div>
                            <div class="payment-qr-card">
                                <img src="<?= url(setting_value('maribank_qr', 'assets/images/maribank-qr.jfif')) ?>" alt="MariBank QR code for Helping Hands NGO donations">
                                <strong>MariBank</strong>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-sm btn-outline-primary qr-view-button" type="button" data-qr-src="<?= url(setting_value('maribank_qr', 'assets/images/maribank-qr.jfif')) ?>" data-qr-title="MariBank QR Code"><i class="bi bi-zoom-in me-1"></i>View Full Size</button>
                                    <a class="btn btn-sm btn-primary" href="<?= url(setting_value('maribank_qr', 'assets/images/maribank-qr.jfif')) ?>" download="helping-hands-maribank-qr"><i class="bi bi-download me-1"></i>Save QR</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-warning btn-lg w-100 mt-3" name="action" value="submit_donation" type="submit"><i class="bi bi-send-check me-2"></i>Submit for Verification</button>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="qr-lightbox" id="qrLightbox" aria-hidden="true">
    <div class="qr-lightbox-dialog" role="dialog" aria-modal="true" aria-labelledby="qrLightboxTitle">
        <button class="qr-lightbox-close" id="qrLightboxClose" type="button" aria-label="Close QR preview"><i class="bi bi-x-lg"></i></button>
        <h2 id="qrLightboxTitle">QR Code</h2>
        <img id="qrLightboxImage" src="" alt="">
        <p>Save this QR image or scan it with your payment app.</p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
