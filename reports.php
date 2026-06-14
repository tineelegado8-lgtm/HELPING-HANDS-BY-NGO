<?php
require_once __DIR__ . '/includes/functions.php';

page_title('Impact Reports');

$reports = table_exists('reports')
    ? rows('SELECT * FROM reports WHERE published = 1 ORDER BY created_at DESC')
    : [];

require __DIR__ . '/includes/header.php';
?>

<style>
.report-card{
    border:1px solid rgba(23, 105, 170, .12);
    border-radius:28px;
    overflow:hidden;
    transition:transform .25s ease,box-shadow .25s ease;
    box-shadow:0 24px 70px rgba(23, 105, 170, .08);
    height:100%;
}

.report-card:hover{
    transform:translateY(-6px);
    box-shadow:0 32px 90px rgba(23, 105, 170, .14);
}

.report-card .card-body{
    padding:2rem;
}

.report-icon{
    width:84px;
    height:84px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 1.25rem;
    border-radius:20px;
    background:rgba(220, 53, 69, .12);
    color:#dc3545;
    font-size:2rem;
}

.report-date{
    font-size:.9rem;
    color:#6c757d;
}

.report-type{
    color:var(--primary-color);
    font-weight:700;
    letter-spacing:.04em;
}

.empty-state{
    text-align:center;
    padding:4rem 2rem;
    background:rgba(23, 105, 170, .05);
    border-radius:28px;
    box-shadow:inset 0 0 0 1px rgba(23, 105, 170, .08);
}

.empty-state i{
    font-size:5rem;
    color:rgba(23, 105, 170, .35);
}

.section-title{
    font-weight:700;
    margin-bottom:.65rem;
}

.section-subtitle{
    color:#6c757d;
    max-width:700px;
    margin:auto;
}
</style>

<section class="section-pad">
    <div class="container">

        <div class="text-center mb-5">
            <p class="eyebrow text-primary fw-bold">Transparency & Accountability</p>

            <h1 class="section-title">
                Impact Reports
            </h1>

            <p class="section-subtitle">
                View and download our latest financial, donation,
                program, annual, and impact reports.
            </p>
        </div>

        <?php if (!$reports): ?>

            <div class="empty-state">
                <i class="bi bi-file-earmark-pdf"></i>

                <h3 class="mt-3">
                    No Reports Available Yet
                </h3>

                <p class="text-muted">
                    Published reports will automatically appear here.
                </p>
            </div>

        <?php else: ?>

            <div class="row g-4">

                <?php foreach ($reports as $report): ?>

                    <?php
                    $filePath = trim($report['file_path'] ?? '');
                    $fileExists = !empty($filePath) && file_exists(__DIR__ . '/' . $filePath);
                    ?>

                    <div class="col-md-6 col-lg-4">

                        <div class="card report-card">

                            <div class="card-body text-center p-4">

                                <div class="mb-3">
                                    <i class="bi bi-file-earmark-pdf-fill report-icon"></i>
                                </div>

                                <h4 class="fw-bold">
                                    <?= e($report['title']) ?>
                                </h4>

                                <p class="report-type mb-2">
                                    <?= e($report['report_type']) ?>
                                </p>

                                <p class="report-date">
                                    <i class="bi bi-calendar-event"></i>
                                    <?= !empty($report['created_at']) ? date('F d, Y', strtotime($report['created_at'])) : 'N/A' ?>
                                </p>

                                <hr>

                                <?php if ($fileExists): ?>

                                    <a
                                        href="<?= url($filePath) ?>"
                                        class="btn btn-primary w-100"
                                        download
                                    >
                                        <i class="bi bi-download me-2"></i>
                                        Download PDF
                                    </a>

                                <?php else: ?>

                                    <button
                                        class="btn btn-secondary w-100"
                                        disabled
                                    >
                                        PDF Not Available
                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>