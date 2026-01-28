<?php
$sidebar = '<div class="brand">MiniGo Admin</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin"><i class="bi bi-speedometer2"></i> Overview</a>'
    . '<a class="nav-link" href="#merchants"><i class="bi bi-people"></i> Merchants</a>'
    . '<a class="nav-link" href="#templates"><i class="bi bi-grid"></i> Templates</a>'
    . '<a class="nav-link" href="#pages"><i class="bi bi-window"></i> Pages</a>'
    . '<a class="nav-link" href="#orders"><i class="bi bi-inbox"></i> Orders</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">Sign out</button>'
    . '</form>'
    . '</nav>';
$title = 'Super Admin Command Center';
$subtitle = 'Control merchants, templates, pages, and orders.';
ob_start();
?>
<section class="dashboard-grid">
    <div class="card app-card" id="merchants">
        <div class="card-header">
            <h3>Merchants</h3>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/merchants" class="row g-3 mb-4">
                <?= csrf_field() ?>
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control" placeholder="Merchant name" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="subdomain" class="form-control" placeholder="Subdomain" required>
                </div>
                <div class="col-md-3">
                    <input type="email" name="email" class="form-control" placeholder="Owner email" required>
                </div>
                <div class="col-md-2">
                    <input type="password" name="password" class="form-control" placeholder="Temp password" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-accent w-100" type="submit">Create</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subdomain</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($merchants as $merchant) : ?>
                            <tr>
                                <td><?= htmlspecialchars($merchant['name']) ?></td>
                                <td><?= htmlspecialchars($merchant['subdomain']) ?></td>
                                <td><?= $merchant['is_active'] ? 'Active' : 'Disabled' ?></td>
                                <td>
                                    <form method="post" action="/admin/merchants/toggle">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="merchant_id" value="<?= (int) $merchant['id'] ?>">
                                        <button class="btn btn-sm btn-outline-light" type="submit">Toggle</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card app-card" id="templates">
        <div class="card-header">
            <h3>Templates</h3>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/templates" class="row g-3 mb-4">
                <?= csrf_field() ?>
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Template name" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="description" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-accent w-100" type="submit">Create</button>
                </div>
            </form>
            <div class="row g-3">
                <?php foreach ($templates as $template) : ?>
                    <div class="col-md-4">
                        <div class="template-card">
                            <h5><?= htmlspecialchars($template['name']) ?></h5>
                            <p><?= htmlspecialchars($template['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card app-card" id="pages">
        <div class="card-header">
            <h3>Landing Pages</h3>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/pages" class="row g-3 mb-4">
                <?= csrf_field() ?>
                <div class="col-md-2">
                    <select name="merchant_id" class="form-select" required>
                        <option value="">Merchant</option>
                        <?php foreach ($merchants as $merchant) : ?>
                            <option value="<?= (int) $merchant['id'] ?>"><?= htmlspecialchars($merchant['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="template_id" class="form-select" required>
                        <option value="">Template</option>
                        <?php foreach ($templates as $template) : ?>
                            <option value="<?= (int) $template['id'] ?>"><?= htmlspecialchars($template['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="title" class="form-control" placeholder="Title" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="slug" class="form-control" placeholder="Slug" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="price" class="form-control" placeholder="Price">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-accent w-100" type="submit">Create</button>
                </div>
                <div class="col-12">
                    <input type="text" name="description" class="form-control" placeholder="Short description">
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Merchant</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $page) : ?>
                            <tr>
                                <td><?= htmlspecialchars($page['title']) ?></td>
                                <td><?= htmlspecialchars($page['merchant_name']) ?></td>
                                <td><?= htmlspecialchars($page['slug']) ?></td>
                                <td><?= $page['is_active'] ? 'Active' : 'Disabled' ?></td>
                                <td>
                                    <form method="post" action="/admin/pages/toggle">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                                        <button class="btn btn-sm btn-outline-light" type="submit">Toggle</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card app-card" id="orders">
        <div class="card-header">
            <h3>Orders</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Merchant</th>
                            <th>Page</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : ?>
                            <tr>
                                <td>#<?= (int) $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['merchant_name']) ?></td>
                                <td><?= htmlspecialchars($order['page_title']) ?></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td><?= htmlspecialchars($order['phone']) ?></td>
                                <td><?= htmlspecialchars($order['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
