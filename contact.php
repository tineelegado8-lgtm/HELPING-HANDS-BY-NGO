<?php
require_once __DIR__ . '/includes/functions.php';
verify_csrf();
page_title('Contact Us');

ensure_contact_messages_schema();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    execute(
        'INSERT INTO contact_messages (full_name, email, subject, message) VALUES (?, ?, ?, ?)',
        [
            $_POST['full_name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['subject'] ?? '',
            $_POST['message'] ?? ''
        ]
    );
    flash('success', 'Your message has been sent.');
}

require __DIR__ . '/includes/header.php';
?>

<style>
    .contact-hero {
        background: linear-gradient(135deg, #f8fafc, #eef2ff);
        padding: 60px 0;
    }

    .contact-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    }

    .info-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }

    .eyebrow {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
    }

    h1 {
        font-weight: 700;
        color: #111827;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px;
    }

    .btn-primary {
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
    }

    .contact-title {
        font-size: 28px;
        margin-bottom: 10px;
    }
</style>

<section class="contact-hero">
    <div class="container">
        <div class="row g-5 align-items-center">

            <!-- LEFT INFO -->
            <div class="col-lg-5">
                <p class="eyebrow">Contact Us</p>
                <h1 class="contact-title">Let's work together for the community.</h1>
                <p class="text-muted mb-4">
                    We’d love to hear from you. Reach out anytime and our team will respond as soon as possible.
                </p>

                <div class="info-box mb-3">
                    <p><strong>📞 Contact Number:</strong><br> +639266875610</p>
                    <p><strong>📧 Email:</strong><br> helpinghandsngo@gmail.com</p>
                    <p><strong>🕒 Office Hours:</strong><br> Monday to Friday, 9:00 AM - 5:00 PM</p>
                </div>
            </div>

            <!-- RIGHT FORM -->
            <div class="col-lg-7">
                <?php if ($msg = flash('success')): ?>
                    <div class="alert alert-success shadow-sm">
                        <?= e($msg) ?>
                    </div>
                <?php endif; ?>

                <form class="contact-card p-4 p-md-5" method="post">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                    <h4 class="mb-4 fw-bold">Send us a message</h4>

                    <label class="form-label">Full Name</label>
                    <input class="form-control mb-3" name="full_name" required>

                    <label class="form-label">Email</label>
                    <input class="form-control mb-3" type="email" name="email" required>

                    <label class="form-label">Subject</label>
                    <input class="form-control mb-3" name="subject" required>

                    <label class="form-label">Message</label>
                    <textarea class="form-control mb-4" name="message" rows="6" required></textarea>

                    <button class="btn btn-primary w-100 btn-lg">
                        Send Message →
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>