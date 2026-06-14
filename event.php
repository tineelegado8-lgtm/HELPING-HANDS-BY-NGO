<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$ev = $id ? one('SELECT * FROM events WHERE id = ?', [$id]) : null;
if (!$ev) {
    echo '<div class="container mt-5 pt-5"><div class="alert alert-warning">Event not found.</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
page_title($ev['title']);
?>
<section class="section-pad soft-band">
    <div class="container">
        <div class="event-detail-hero" style="background-image: url('<?= $ev['image'] ? url($ev['image']) : url('assets/images/helping-hands-logo.jpg') ?>');">
            <div class="event-detail-hero-content">
                <span class="badge bg-white text-dark bg-opacity-90 mb-3"><?= e($ev['status']) ?></span>
                <h1 class="display-5 fw-bold text-white mb-3"><?= e($ev['title']) ?></h1>
                <p class="lead text-white-75 mb-0">
                    <?= date('F j, Y', strtotime($ev['event_date'])) ?><?= $ev['event_time'] ? ' • ' . date('g:i A', strtotime($ev['event_time'])) : '' ?>
                </p>
                <p class="text-white-75 mb-4"><?= e($ev['location']) ?></p>
                <?php if (!empty($ev['organizer'])): ?>
                    <p class="text-white-75 mb-0"><strong>Organizer:</strong> <?= e($ev['organizer']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="event-detail-card shadow-lg rounded-4 mt-n7 p-4">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <h2 class="mb-3">About this event</h2>
                    <div class="text-secondary fs-6 mb-4" style="line-height:1.8;"><?= nl2br(e($ev['description'])) ?></div>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="<?= url('volunteer.php') ?>" class="btn btn-primary btn-lg flex-fill">Join as a Volunteer</a>
                        <a href="<?= url('donation.php') ?>" class="btn btn-outline-primary btn-lg flex-fill">Support the Event</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="event-detail-sidebar p-4 rounded-4 h-100 bg-white border">
                        <h5 class="mb-4">Event details</h5>
                        <div class="mb-3">
                            <span class="d-block text-muted mb-1">Date</span>
                            <strong><?= date('F j, Y', strtotime($ev['event_date'])) ?></strong>
                        </div>
                        <div class="mb-3">
                            <span class="d-block text-muted mb-1">Time</span>
                            <strong><?= $ev['event_time'] ? date('g:i A', strtotime($ev['event_time'])) : 'To be announced' ?></strong>
                        </div>
                        <div class="mb-3">
                            <span class="d-block text-muted mb-1">Location</span>
                            <strong><?= e($ev['location']) ?></strong>
                        </div>
                        <div>
                            <span class="d-block text-muted mb-1">Organizer</span>
                            <strong><?= e($ev['organizer']) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php';
