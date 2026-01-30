<?php
$sidebar = null;
$title = 'Initial Setup';
$subtitle = 'Create the first Super Admin account.';
ob_start();
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card app-card">
                <div class="card-body">
                    <form method="post" action="/setup" class="vstack gap-3">
                        <?= csrf_field() ?>
                        <div>
                            <label class="form-label">Full name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div>
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-accent" type="submit">Create Super Admin</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-secondary mt-3">This setup can be completed only once.</p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
