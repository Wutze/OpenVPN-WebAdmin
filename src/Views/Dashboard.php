<div class="content-wrapper p-3">
    <section class="content">
        <div class="container-fluid">
            <h1><?= Lang::get('_DASHBOARD') ?></h1>
            <p><?= Lang::get('_WELCOME_MESSAGE') ?>, <?= $_SESSION['uname'] ?? 'Gast' ?>!</p>
        </div>
    </section>
</div>
