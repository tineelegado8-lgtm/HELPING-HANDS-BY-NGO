<?php require_once __DIR__ . '/includes/functions.php'; page_title('Programs'); $programs = programs(); require __DIR__ . '/includes/header.php'; ?>
<section class="section-pad">
    <div class="container">
        <p class="eyebrow">Programs</p>
        <h1 class="mb-4">Support a focused campaign</h1>
        <div class="row g-4">
<?php foreach ($programs as $program): $raised = (float)$program['raised_amount']; $goal = (float)$program['goal_amount']; $remaining = max(0, $goal - $raised); $pct = min(100, ($raised / max(1, $goal)) * 100); $imgPath = trim((string)($program['image_path'] ?? '')); ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="image-placeholder">
                            <?php if ($imgPath): ?>
                                <img src="<?= url($imgPath) ?>" alt="<?= e($program['name']) ?> program image" style="width: 100%; height: 220px; object-fit: cover; border-radius: 6px;">
                            <?php else: ?>
                                <?= e($program['image_label'] ?? '[PROGRAM IMAGE HERE]') ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h4><?= e($program['name']) ?></h4>
                            <p class="text-secondary"><?= e($program['description']) ?></p>
                            <div class="progress mb-3"><div class="progress-bar" style="width: <?= $pct ?>%"></div></div>
                            <p class="mb-1"><strong>Goal:</strong> <?= money($goal) ?></p>
                            <p class="mb-1"><strong>Amount Raised:</strong> <?= money($raised) ?></p>
                            <p class="mb-3"><strong>Remaining Balance:</strong> <?= money($remaining) ?></p>
                            <a class="btn btn-success mt-auto" href="<?= url('donation.php?program=' . (int)$program['id']) ?>">Donate Button</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

