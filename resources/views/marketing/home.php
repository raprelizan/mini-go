<?php
$sidebar = null;
$title = 'MiniGo Multi-Tenant Commerce Platform';
$subtitle = 'Launch high-converting landing pages for every merchant without custom development.';
ob_start();
?>
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge text-bg-primary mb-3">Cash On Delivery Ready</span>
                <h2 class="display-5 fw-bold">Build. Launch. Convert. Manage everything from one platform.</h2>
                <p class="lead">MiniGo hosts multiple merchant landing pages, each with its own subdomain, templates, and instant order notifications on WhatsApp or Telegram.</p>
                <div class="d-flex gap-3">
                    <a class="btn btn-accent" href="/login">Admin Login</a>
                    <a class="btn btn-outline-light" href="#features">Explore Features</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-card">
                    <h3 class="h5">Platform Highlights</h3>
                    <ul class="list-unstyled mt-4">
                        <li><i class="bi bi-globe"></i> Multi-tenant subdomains for merchants</li>
                        <li><i class="bi bi-layout-text-window"></i> Template-controlled landing pages</li>
                        <li><i class="bi bi-whatsapp"></i> WhatsApp + Telegram order delivery</li>
                        <li><i class="bi bi-speedometer2"></i> Fast, responsive, conversion-first UI</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="features" class="feature-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-diagram-3"></i>
                    <h4>Controlled Templates</h4>
                    <p>Create multiple layouts, lock what matters, and give merchants only what they should edit.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-chat-square-heart"></i>
                    <h4>Instant Order Routing</h4>
                    <p>Orders are saved in the database and sent to merchants with clean, professional messages.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-shield-check"></i>
                    <h4>Role-Based Access</h4>
                    <p>Super Admin controls the ecosystem while merchants see only their own pages and orders.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
