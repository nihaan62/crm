<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Solutions Enquiry Form | Websites, Apps & Software</title>
    <meta name="description" content="Get a quote for website development, mobile apps, custom software, ERP and more. Fill in your IT project requirements.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a0533 0%, #4a148c 30%, #6a1b9a 60%, #ab47bc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }
        .card-wrap {
            width: 100%;
            max-width: 560px;
        }
        .brand-top {
            text-align: center;
            margin-bottom: 24px;
        }
        .brand-top .icon-circle {
            width: 68px; height: 68px;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.35);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #fff;
            margin-bottom: 14px;
            backdrop-filter: blur(10px);
        }
        .brand-top h1 {
            color: #fff;
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .brand-top p {
            color: rgba(255,255,255,0.78);
            font-size: 14px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 36px 36px 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.28);
            animation: slideUp 0.4s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-success-box {
            background: linear-gradient(135deg, #6a1b9a, #311b92);
            color: #fff;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }
        .alert-success-box .check-icon {
            font-size: 48px;
            margin-bottom: 12px;
            display: block;
        }
        .alert-error-box {
            background: #fff3f3;
            border: 1px solid #f44336;
            border-radius: 8px;
            color: #c62828;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .service-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 24px;
        }
        .service-btn {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 14px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12.5px;
            font-weight: 600;
            color: #555;
            background: #fafafa;
            user-select: none;
        }
        .service-btn .icon { font-size: 22px; display: block; margin-bottom: 5px; }
        .service-btn:hover { border-color: #6a1b9a; background: #f3e5f5; color: #6a1b9a; }
        .service-btn.selected { border-color: #6a1b9a; background: linear-gradient(135deg, #f3e5f5, #e1bee7); color: #4a148c; }
        .form-section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6a1b9a;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f3e5f5;
        }
        .form-group { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }
        label span.req { color: #f44336; margin-left: 3px; }
        input[type="text"], input[type="email"], input[type="number"], input[type="tel"], select, textarea {
            width: 100%;
            border: 1.5px solid #ddd;
            border-radius: 9px;
            padding: 11px 14px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #333;
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #6a1b9a;
            box-shadow: 0 0 0 3px rgba(106,27,154,0.12);
            background: #fff;
        }
        .row-two { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        textarea { resize: vertical; min-height: 80px; }
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #6a1b9a, #311b92);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            margin-top: 6px;
        }
        .submit-btn:hover { opacity: 0.92; transform: translateY(-1px); }
        .submit-btn:active { transform: translateY(0); }
        .privacy-note {
            text-align: center;
            color: #999;
            font-size: 11.5px;
            margin-top: 16px;
        }
        @media (max-width: 480px) {
            .card { padding: 26px 20px; }
            .service-grid { grid-template-columns: 1fr 1fr; }
            .row-two { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="card-wrap">
    <div class="brand-top">
        <div class="icon-circle">💻</div>
        <h1>IT Solutions Enquiry</h1>
        <p>Tell us about your IT needs and we will craft the perfect solution for your business.</p>
    </div>

    <div class="card">
        <?php if ($success): ?>
            <div class="alert-success-box">
                <span class="check-icon">✅</span>
                <?= e($success); ?>
                <div style="margin-top:16px; font-size:14px; font-weight:400; opacity:.88;">Our IT experts will get in touch with you within 24 hours to discuss your requirements.</div>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert-error-box">⚠️ <?= e($error); ?></div>
            <?php endif; ?>

            <!-- Service Selection -->
            <div class="form-section-title">1. Service Required</div>
            <div class="service-grid" id="serviceGrid">
                <div class="service-btn" data-value="Website Development" onclick="selectService(this)">
                    <span class="icon">🌐</span> Website Development
                </div>
                <div class="service-btn" data-value="Mobile App Development" onclick="selectService(this)">
                    <span class="icon">📱</span> Mobile App
                </div>
                <div class="service-btn" data-value="Custom Software" onclick="selectService(this)">
                    <span class="icon">⚙️</span> Custom Software
                </div>
                <div class="service-btn" data-value="ERP / CRM Solution" onclick="selectService(this)">
                    <span class="icon">📊</span> ERP / CRM
                </div>
                <div class="service-btn" data-value="Digital Marketing" onclick="selectService(this)">
                    <span class="icon">📣</span> Digital Marketing
                </div>
                <div class="service-btn" data-value="IT Consulting" onclick="selectService(this)">
                    <span class="icon">🧠</span> IT Consulting
                </div>
                <div class="service-btn" data-value="Cloud & Hosting" onclick="selectService(this)">
                    <span class="icon">☁️</span> Cloud & Hosting
                </div>
                <div class="service-btn" data-value="Other" onclick="selectService(this)">
                    <span class="icon">💡</span> Other
                </div>
            </div>

            <!-- Contact Info -->
            <div class="form-section-title">2. Your Contact Details</div>
            <form action="" method="POST" id="itForm">
                <input type="hidden" name="description" id="selected_service" value="">

                <div class="row-two">
                    <div class="form-group">
                        <label>Full Name <span class="req">*</span></label>
                        <input type="text" name="name" id="fullName" placeholder="e.g. Suresh Patel" required autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label>Company / Business Name</label>
                        <input type="text" name="company" placeholder="e.g. Tech Solutions Ltd" autocomplete="organization">
                    </div>
                </div>

                <div class="row-two">
                    <div class="form-group">
                        <label>Phone Number <span class="req">*</span></label>
                        <input type="tel" name="phonenumber" id="phoneNum" placeholder="e.g. 9876543210" required autocomplete="tel" maxlength="15">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="e.g. suresh@business.com" autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label>Project Budget (₹)</label>
                    <input type="number" name="lead_value" placeholder="e.g. 50000" min="0" step="1000">
                </div>

                <button type="submit" class="submit-btn" onclick="return validateForm()">
                    🚀 Get a Free Quote
                </button>
            </form>
            <div class="privacy-note">🔒 Your information is 100% secure and will never be shared with third parties.</div>
        <?php endif; ?>
    </div>
</div>

<script>
function selectService(el) {
    document.querySelectorAll('.service-btn').forEach(function(b) { b.classList.remove('selected'); });
    el.classList.add('selected');
    document.getElementById('selected_service').value = el.dataset.value;
}

function validateForm() {
    var name  = document.getElementById('fullName').value.trim();
    var phone = document.getElementById('phoneNum').value.trim();
    if (!name) { alert('Please enter your full name.'); return false; }
    if (!phone || phone.length < 10) { alert('Please enter a valid phone number.'); return false; }
    return true;
}
</script>
</body>
</html>
