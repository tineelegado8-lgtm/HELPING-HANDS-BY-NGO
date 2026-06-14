<?php
require_once __DIR__ . '/includes/functions.php';

ensure_documentation_schema();

page_title('Documentation Gallery');

$docs = rows(
    "SELECT * FROM documentation ORDER BY created_at DESC"
);
$albums = documentation_albums($docs);

require_once __DIR__ . '/includes/header.php';
?>

<style>
.gallery-header{
    text-align:center;
    margin-bottom:50px;
}

.gallery-header h1{
    font-weight:700;
    margin-bottom:10px;
}

.gallery-header p{
    color:#6c757d;
}

.doc-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    transition:all .3s ease;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    height:100%;
}

.doc-card:hover{
    transform:translateY(-8px);
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}

.doc-media{
    width:100%;
    height:260px;
    object-fit:cover;
}

.doc-card-clickable {
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.doc-card-clickable .doc-card {
    cursor: pointer;
}

.related-item {
    display: inline-flex;
    width: 72px;
    height: 72px;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    margin: 0 0.5rem 0.5rem 0;
}

.related-item.active {
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13,110,253,.12);
}

.related-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.doc-modal-main {
    position: relative;
}

.doc-modal-nav-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,.12);
    background: rgba(255,255,255,.95);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,.08);
}

.doc-modal-nav-button:hover {
    background: #fff;
}

.doc-modal-nav-prev {
    left: 12px;
}

.doc-modal-nav-next {
    right: 12px;
}

#docModalRelated {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

/* Full album grid removed per request */

.badge-category{
    background:#0d6efd;
    color:#fff;
    padding:6px 12px;
    border-radius:20px;
    font-size:.75rem;
}

.empty-gallery{
    text-align:center;
    padding:100px 20px;
}

.empty-gallery i{
    font-size:5rem;
    color:#ced4da;
}
</style>

<section class="section-pad">
<div class="container">

    <div class="gallery-header">
        <p class="eyebrow">Community Activities</p>

        <h1>Documentation Gallery</h1>

        <p>
            Browse photos and videos from our outreach programs,
            volunteer activities, donation drives, and community events.
        </p>
    </div>

    <?php if(empty($albums)): ?>

        <div class="empty-gallery">
            <i class="bi bi-images"></i>

            <h3 class="mt-3">
                No Documentation Albums Available
            </h3>

            <p class="text-muted">
                Uploaded photos and videos will appear here.
            </p>
        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php foreach($albums as $index => $album): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="doc-card-clickable" id="album-<?= e($index) ?>" data-album-index="<?= e($index) ?>" data-album-key="<?= e($album['key']) ?>">
                        <div class="card doc-card">

                            <?php $cover = $album['cover']; ?>
                            <?php if (is_video_media($cover)): ?>

                                <video class="doc-media" muted playsinline preload="metadata">
                                    <source src="<?= url($cover) ?>" type="<?= e(media_mime_type($cover)) ?>">
                                </video>

                            <?php elseif (is_image_media($cover)): ?>

                                <img
                                    src="<?= url($cover) ?>"
                                    class="doc-media"
                                    alt="<?= e($album['title']) ?>"
                                >

                            <?php else: ?>

                                <div class="doc-media bg-light d-flex align-items-center justify-content-center">
                                    <span class="text-muted">Unsupported media</span>
                                </div>

                            <?php endif; ?>

                            <div class="card-body p-4">

                                <?php if(!empty($album['category'])): ?>
                                    <span class="badge-category">
                                        <?= e($album['category']) ?>
                                    </span>
                                <?php endif; ?>

                                <h5 class="mt-3 fw-bold">
                                    <?= e($album['title']) ?>
                                </h5>

                                <?php if(!empty($album['description'])): ?>
                                    <p class="text-muted">
                                        <?= e(strlen($album['description']) > 100 ? substr($album['description'], 0, 100) . '...' : $album['description']) ?>
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="small text-secondary">
                                        <i class="bi bi-calendar-event"></i>
                                        <?= date('F d, Y', strtotime($album['updated_at'])) ?>
                                    </div>
                                    <span class="badge bg-secondary"><?= e($album['count']) ?> items</span>
                                </div>

                                <?php if ($album['count'] > 1): ?>
                                    <div class="d-grid">
                                        <a href="#album_key=<?= rawurlencode($album['key']) ?>" class="btn btn-outline-primary btn-sm view-album-button" data-album-index="<?= e($index) ?>" data-album-key="<?= e($album['key']) ?>">View Album</a>
                                    </div>
                                <?php endif; ?>

                            </div>

                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>
</section>

<?php if (!empty($albums)): ?>
<?php
    $albums_for_js = [];
    foreach ($albums as $a) {
        $copy = $a;
        $copy['cover'] = isset($a['cover']) ? url($a['cover']) : '';
        $copy['items'] = [];
        foreach ($a['items'] as $it) {
            $itCopy = $it;
            $itCopy['media_path'] = isset($it['media_path']) ? url($it['media_path']) : '';
            $copy['items'][] = $itCopy;
        }
        $albums_for_js[] = $copy;
    }
?>
<script>
window.documentationAlbums = <?= json_encode($albums_for_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<?php endif; ?>

<div class="modal fade" id="documentationModal" tabindex="-1" aria-labelledby="documentationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentationModalLabel">Documentation Album</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="doc-modal-main mb-3">
                            <div id="docModalMedia"></div>
                            <button id="docModalPrev" type="button" class="doc-modal-nav-button doc-modal-nav-prev" aria-label="Previous media">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button id="docModalNext" type="button" class="doc-modal-nav-button doc-modal-nav-next" aria-label="Next media">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <h4 id="docModalTitle"></h4>
                        <p id="docModalDescription" class="text-secondary"></p>
                        <div class="mb-3">
                            <span class="badge-category" id="docModalCategory"></span>
                        </div>
                        <div class="small text-secondary mb-4" id="docModalDate"></div>
                        <h6>Album Items</h6>
                        <div id="docModalRelated"></div>
                        <!-- Full album removed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>