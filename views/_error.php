<?php
/** @var int|null $statusCode */
/** @var string|null $message */
/** @var \Throwable|null $exception */
$code    = $statusCode ?? ($exception ? $exception->getCode() : 500);
$summary = $message    ?? 'Đã xảy ra lỗi không mong muốn.';
?>
<div class="container text-center" style="padding: 60px 0">
    <h1><?= e((string) $code) ?></h1>
    <h3><?= e($summary) ?></h3>
    <?php if ($exception !== null) : ?>
    <pre style="text-align:left; background:#f8f9fa; padding:20px; margin-top:20px; overflow:auto">
<?= e($exception->getMessage()) ?>

<?= e($exception->getTraceAsString()) ?>
    </pre>
    <?php endif; ?>
    <a href="/" class="btn btn-primary" style="margin-top:20px">Về trang chủ</a>
</div>
