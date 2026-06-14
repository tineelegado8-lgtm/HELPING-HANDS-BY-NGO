<?php
// Show errors during debugging (remove or disable on production)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/includes/functions.php';
page_title('Home');
ensure_documentation_schema();
$s = stats();
$featured = array_slice(programs(), 0, 4);
$recent = table_exists('donations') ? rows('SELECT full_name, amount, donation_message, created_at FROM donations ORDER BY id DESC LIMIT 5') : [];
$upcoming_events = table_exists('events') ? rows('SELECT * FROM events WHERE event_date >= CURRENT_DATE() ORDER BY event_date, event_time LIMIT 3') : [];
$docs = table_exists('documentation') ? rows('SELECT * FROM documentation ORDER BY created_at DESC LIMIT 12') : [];
$albums = table_exists('documentation') ? documentation_albums($docs) : [];
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 fade-up">
                <p class="eyebrow"><?= e(setting_value('hero_pretext', 'Together we create positive change')) ?></p>
                <h1 class="display-4 fw-bold mb-3"><?= e(setting_value('hero_title', APP_NAME)) ?></h1>
                <p class="lead mb-4"><?= e(setting_value('hero_subtitle', APP_SLOGAN)) ?></p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-warning btn-lg" href="<?= url('donation.php') ?>"><i class="bi bi-heart-fill me-2"></i>Donate Now</a>
                    <a class="btn btn-outline-light btn-lg" href="<?= url('volunteer.php') ?>"><i class="bi bi-people-fill me-2"></i>Volunteer</a>
                </div>
            </div>
            <div class="col-lg-6 fade-up">
                <img class="hero-image" src="<?= url(setting_value('hero_image', 'assets/images/helping-hands-logo.jpg')) ?>" alt="<?= e(setting_value('hero_title', APP_NAME)) ?>">
            </div>
        </div>
    </div>
</section>

<section class="section-pad soft-band">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end gap-3 mb-4 flex-wrap">
            <div>
                <p class="eyebrow">Upcoming Events</p>
                <h2 class="mb-0">See what’s coming next</h2>
            </div>
            <a class="btn btn-primary" href="<?= url('events.php') ?>">View All Events</a>
        </div>
        <div class="row g-4">
            <?php if (!$upcoming_events): ?>
                <div class="col-12"><div class="alert alert-info">No upcoming events are scheduled right now. Please check back soon.</div></div>
            <?php endif; ?>
            <?php foreach ($upcoming_events as $event): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?= url($event['image']) ?>" class="card-img-top" alt="<?= e($event['title']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5><?= e($event['title']) ?></h5>
                            <p class="small text-secondary mb-2"><?= date('F j, Y', strtotime($event['event_date'])) ?><?= $event['event_time'] ? ' @ ' . date('g:i A', strtotime($event['event_time'])) : '' ?></p>
                            <p class="card-text mb-2"><?= e(strlen($event['description']) > 120 ? substr($event['description'], 0, 120) . '...' : $event['description']) ?></p>
                            <p class="small mb-0"><strong>Location:</strong> <?= e($event['location']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-pad soft-band">
    <div class="container">
        <div class="row g-4">
            <!-- Stats Section -->
            <div class="col-lg-7">
                <p class="eyebrow">Transparency</p>
                <h2 class="mb-4">Public accountability snapshot</h2>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="stat-card-enhanced">
                            <div class="stat-icon">💰</div>
                            <div class="stat-content">
                                <small class="stat-label">Total Donations Received</small>
                                <h4 class="stat-value"><?= money($s['donations']) ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card-enhanced">
                            <div class="stat-icon">👥</div>
                            <div class="stat-content">
                                <small class="stat-label">Number of Donors</small>
                                <h4 class="stat-value"><?= $s['donors'] ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card-enhanced">
                            <div class="stat-icon">🤝</div>
                            <div class="stat-content">
                                <small class="stat-label">Active Volunteers</small>
                                <h4 class="stat-value"><?= $s['volunteers'] ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card-enhanced">
                            <div class="stat-icon">❤️</div>
                            <div class="stat-content">
                                <small class="stat-label">Beneficiaries Helped</small>
                                <h4 class="stat-value"><?= $s['beneficiaries'] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Announcements Section -->
            <div class="col-lg-5">
                <p class="eyebrow">Latest Announcements</p>
                <div class="card announcement-card">
                    <div class="card-body">
                        <div class="announcement-header mb-4">
                            <h5 class="mb-1">📊 Monthly Donation Summary</h5>
                            <p class="announcement-amount"><?= money((float) scalar("SELECT COALESCE(SUM(amount),0) FROM donations WHERE MONTH(created_at)=MONTH(CURRENT_DATE()) AND YEAR(created_at)=YEAR(CURRENT_DATE())", [], 0)) ?></p>
                        </div>
                        
                        <div class="announcement-item">
                            <h6 class="announcement-title">✨ Success Stories</h6>
                            <p class="text-secondary mb-0">Community tutoring, meal distribution, and relief operations continue through donor and volunteer support. Your generosity makes a real difference in our community.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
