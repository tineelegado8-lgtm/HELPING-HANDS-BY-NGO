<?php
require_once __DIR__ . '/includes/functions.php';

function ensure_events_schema(): void
{
    if (table_exists('events')) return;
    $sql = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        event_date DATE NOT NULL,
        event_time TIME DEFAULT NULL,
        location VARCHAR(255) DEFAULT NULL,
        organizer VARCHAR(255) DEFAULT NULL,
        status VARCHAR(32) DEFAULT 'Upcoming',
        image VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    execute($sql);
}

ensure_events_schema();
page_title('Events');
require_once __DIR__ . '/includes/header.php';
$events = rows('SELECT * FROM events ORDER BY event_date, event_time');
?>
<section class="section-pad soft-band">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <p class="eyebrow">Events & Activities</p>
                <h1 class="display-6 fw-bold">Join our community in upcoming events.</h1>
                <p class="text-secondary">Explore scheduled workshops, outreach programs, fundraisers, and volunteer opportunities designed to make a visible impact.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= url('donation.php') ?>" class="btn btn-outline-primary me-2">Support an Event</a>
                <a href="<?= url('volunteer.php') ?>" class="btn btn-primary">Volunteer Now</a>
            </div>
        </div>
        <div class="row g-4">
            <?php if (!$events): ?>
                <div class="col-12"><div class="alert alert-info">No events found. Please check back soon.</div></div>
            <?php endif; ?>
            <?php foreach ($events as $ev): ?>
                <?php $status = strtolower(trim((string)$ev['status'])); ?>
                <div class="col-sm-6 col-lg-4">
                    <div class="card event-card h-100 shadow-sm border-0 overflow-hidden">
                        <div class="event-card-image<?php if (empty($ev['image'])): ?> event-card-image--placeholder<?php endif; ?>" style="<?php if (!empty($ev['image'])): ?>background-image: url('<?= url($ev['image']) ?>');<?php endif; ?>">
                            <span class="badge event-status bg-<?= $status === 'upcoming' ? 'success' : ($status === 'cancelled' ? 'danger' : 'secondary') ?>"><?= e($ev['status']) ?></span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3 gap-3">
                                <div>
                                    <h5 class="event-title mb-2"><?= e($ev['title']) ?></h5>
                                    <div class="text-muted small event-meta">
                                        <?= date('F j, Y', strtotime($ev['event_date'])) ?><?= $ev['event_time'] ? ' • ' . date('g:i A', strtotime($ev['event_time'])) : '' ?>
                                    </div>
                                </div>
                                <div class="event-date-badge text-center">
                                    <strong><?= date('d', strtotime($ev['event_date'])) ?></strong>
                                    <span><?= strtoupper(date('M', strtotime($ev['event_date']))) ?></span>
                                </div>
                            </div>
                            <p class="card-text text-secondary mb-3"><?= e(strlen($ev['description']) > 150 ? substr($ev['description'], 0, 150) . '...' : $ev['description']) ?></p>
                            <ul class="event-info-list list-unstyled mb-0">
                                <?php if (!empty($ev['location'])): ?><li><strong>Location:</strong> <?= e($ev['location']) ?></li><?php endif; ?>
                                <?php if (!empty($ev['organizer'])): ?><li><strong>Organizer:</strong> <?= e($ev['organizer']) ?></li><?php endif; ?>
                            </ul>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="<?= url('event.php?id=' . $ev['id']) ?>" class="btn btn-outline-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php';
