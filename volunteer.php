<?php
require_once __DIR__ . '/includes/functions.php';
ensure_volunteer_columns();
verify_csrf();
page_title('Volunteer Sign Up');
$error = null;
$applicationStatus = null;
$rejectionReason = null;
$checkEmail = trim((string)($_GET['status_email'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photoPath = upload_file('photo', 'volunteers', ['jpg', 'jpeg', 'png'], 200 * 1024 * 1024, true);
    if (!$photoPath) {
        $error = 'Please upload a valid JPG or PNG photo under 200MB.';
    } else {
        $columns = ['full_name', 'email', 'contact_number', 'address', 'skills', 'availability', 'emergency_contact', 'reason', 'status'];
        $values = [
            $_POST['full_name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['contact_number'] ?? '',
            $_POST['address'] ?? '',
            $_POST['skills'] ?? '',
            $_POST['availability'] ?? '',
            $_POST['emergency_contact'] ?? '',
            $_POST['reason'] ?? '',
            'Pending',
        ];
        if (table_has_column('volunteers', 'photo_path')) {
            $columns[] = 'photo_path';
            $values[] = $photoPath;
        }
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $sql = 'INSERT INTO volunteers (' . implode(',', $columns) . ') VALUES (' . $placeholders . ')';
        execute($sql, $values);
        flash('success', 'Your volunteer application was submitted and is now pending review.');
        header('Location: ' . url('volunteer.php?status_email=' . urlencode($_POST['email'] ?? '')));
        exit;
    }
}
if ($checkEmail !== '') {
    $application = one('SELECT status, rejection_reason FROM volunteers WHERE email = ? ORDER BY id DESC LIMIT 1', [$checkEmail]);
    if ($application) {
        $applicationStatus = $application['status'];
        $rejectionReason = $application['rejection_reason'] ?? null;
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="section-pad soft-band">
    <div class="container">
        <div class="row g-5">
          <div class="row justify-content-center">

    <div class="col-lg-8 text-center mb-5">
        <p class="eyebrow text-success fw-bold">
            Volunteer Sign Up
        </p>

        <h1 class="display-4 fw-bold">
            Join Our Volunteer Team
        </h1>

        <p class="lead text-muted mx-auto" style="max-width:700px;">
            Complete the form below to become a volunteer and help us make a positive impact in the community.
        </p>
    </div>

    <div class="col-lg-8">
</div>
                <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form class="form-card" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Full Name</label><input class="form-control" name="full_name" required></div>
                        <div class="col-md-6"><label class="form-label">Email Address</label><input class="form-control" type="email" name="email" required></div>
                        <div class="col-md-6"><label class="form-label">Contact Number</label><input class="form-control" name="contact_number" required></div>
                        <div class="col-md-6"><label class="form-label">Availability</label><input class="form-control" name="availability" required></div>
                        <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address" required></div>
                        <div class="col-md-6"><label class="form-label">Skills</label><textarea class="form-control" name="skills" rows="3"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Emergency Contact</label><textarea class="form-control" name="emergency_contact" rows="3"></textarea></div>
                        <div class="col-12"><label class="form-label">Reason for Volunteering</label><textarea class="form-control" name="reason" rows="4"></textarea></div>
                        <div class="col-12">
                            <label class="form-label">Verification Photo (JPG, PNG, max 200MB)</label>
                            <div class="input-group">
                                <input class="form-control" type="file" id="photoInput" name="photo" accept="image/jpeg,image/png" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('photoInput').value=''; document.getElementById('photoPreview').innerHTML=''; document.getElementById('photoPreview').style.display='none';">Clear</button>
                            </div>
                            <small class="text-secondary d-block mt-2">Accepted: JPG, JPEG, PNG | Maximum size: 200MB</small>
                        </div>
                        <div class="col-12">
                            <div id="photoPreview" class="photo-preview-box">
                                <img id="previewImg" src="" alt="Photo preview">
                                <p class="mt-3 mb-0"><small id="fileName" class="text-secondary"></small></p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Submit Application</button>
                        <button type="reset" class="btn btn-outline-secondary btn-lg">Clear Form</button>
                    </div>
                </form>
                <script>
                    document.getElementById('photoInput').addEventListener('change', function(e) {
                        const file = this.files[0];
                        if (!file) return;
                        if (!['image/jpeg', 'image/png'].includes(file.type) || file.size > 200 * 1024 * 1024) {
                            alert('Please upload a JPG or PNG image under 200MB.');
                            this.value = '';
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            document.getElementById('previewImg').src = event.target.result;
                            document.getElementById('fileName').textContent = 'File: ' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                            document.getElementById('photoPreview').style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    });
                </script>
                <div class="card status-card mt-4">
                    <div class="card-body">
                        <h3 class="mb-3">Check Your Application Status</h3>
                        <form method="get" class="row g-3">
                        <div class="col-md-8"><input class="form-control" type="email" name="status_email" placeholder="Enter your application email" value="<?= e($checkEmail) ?>" required></div>
                        <div class="col-md-4"><button class="btn btn-outline-primary w-100">Check Status</button></div>
                    </form>
                    <?php if ($applicationStatus !== null): ?>
                        <div class="alert alert-info mt-3">
                            <p class="mb-1"><strong>Status:</strong> <?= e($applicationStatus) ?></p>
                            <?php if ($rejectionReason): ?><p class="mb-0"><strong>Reason:</strong> <?= e($rejectionReason) ?></p><?php endif; ?>
                        </div>
                    <?php elseif ($checkEmail !== ''): ?>
                        <div class="alert alert-warning mt-3">No volunteer application was found for that email.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

