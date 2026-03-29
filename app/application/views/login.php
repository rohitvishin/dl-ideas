<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-start: #f3f7f4;
            --bg-end: #e8eef5;
            --surface: #ffffff;
            --surface-border: rgba(13, 42, 61, 0.12);
            --text-main: #0f2d3a;
            --text-muted: #5f7480;
            --primary: #0b7a75;
            --primary-hover: #085f5c;
            --danger-bg: #fef3f2;
            --danger-text: #9f2d2d;
            --success-bg: #edfdf3;
            --success-text: #1b6b3f;
            --shadow: 0 24px 48px rgba(12, 35, 52, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--text-main);
            background: radial-gradient(circle at top left, rgba(11, 122, 117, 0.15), transparent 45%),
                        radial-gradient(circle at 90% 10%, rgba(40, 120, 180, 0.15), transparent 40%),
                        linear-gradient(135deg, var(--bg-start), var(--bg-end));
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .auth-shell {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            background: var(--surface);
            border: 1px solid var(--surface-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            animation: panelIn 480ms ease;
        }

        .auth-hero {
            background: linear-gradient(155deg, #0f3e58, #0b7a75 55%, #2c8f5f);
            color: #ecf7f6;
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
        }

        .auth-hero:before,
        .auth-hero:after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.25;
            pointer-events: none;
        }

        .auth-hero:before {
            width: 260px;
            height: 260px;
            background: rgba(255, 255, 255, 0.32);
            top: -95px;
            right: -70px;
        }

        .auth-hero:after {
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.2);
            bottom: -60px;
            left: -70px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.17);
            margin-bottom: 18px;
        }

        .auth-hero h1 {
            margin: 0 0 14px;
            font-size: 34px;
            line-height: 1.15;
            max-width: 420px;
        }

        .auth-hero p {
            margin: 0;
            font-size: 15px;
            line-height: 1.65;
            color: rgba(245, 252, 252, 0.9);
            max-width: 430px;
        }

        .auth-form-wrap {
            padding: 44px 42px;
        }

        .auth-form-wrap h2 {
            margin: 0;
            font-size: 26px;
            letter-spacing: -0.01em;
        }

        .subtitle {
            margin: 8px 0 24px;
            color: var(--text-muted);
            font-size: 15px;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.45;
        }

        .alert-danger {
            background: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid rgba(198, 71, 71, 0.22);
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid rgba(38, 143, 90, 0.26);
        }

        .field {
            margin-bottom: 16px;
            animation: fieldIn 300ms ease both;
        }

        .field:nth-child(1) { animation-delay: 40ms; }
        .field:nth-child(2) { animation-delay: 90ms; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #254350;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1px solid rgba(24, 62, 82, 0.26);
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 15px;
            color: var(--text-main);
            outline: 0;
            transition: border-color 180ms ease, box-shadow 180ms ease;
            background: #fbfdff;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: rgba(11, 122, 117, 0.76);
            box-shadow: 0 0 0 4px rgba(11, 122, 117, 0.14);
        }

        .submit-btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 13px 16px;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(145deg, var(--primary), #0c6b9d);
            cursor: pointer;
            transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 18px rgba(7, 75, 112, 0.2);
            background: linear-gradient(145deg, var(--primary-hover), #0b5d88);
        }

        .auth-footer {
            margin-top: 14px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        @keyframes panelIn {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fieldIn {
            from {
                opacity: 0;
                transform: translateX(8px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 900px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .auth-hero {
                padding: 34px 28px;
            }

            .auth-form-wrap {
                padding: 30px 24px;
            }

            .auth-hero h1 {
                font-size: 30px;
            }
        }

        @media (max-width: 640px) {
            body {
                display: block;
                min-height: 100dvh;
                padding: 12px;
            }

            .auth-shell {
                border-radius: 16px;
                min-height: auto;
            }

            .auth-hero {
                padding: 26px 20px;
            }

            .brand-badge {
                margin-bottom: 14px;
            }

            .auth-hero h1 {
                font-size: 24px;
                max-width: none;
            }

            .auth-hero p {
                font-size: 14px;
                max-width: none;
            }

            .auth-form-wrap {
                padding: 24px 18px 20px;
            }

            .auth-form-wrap h2 {
                font-size: 22px;
            }

            .subtitle {
                margin-bottom: 18px;
            }

            input[type="email"],
            input[type="password"],
            .submit-btn {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <main class="auth-shell" role="main">
        <section class="auth-hero" aria-label="Platform login intro">
            <span class="brand-badge">Platform Login</span>
            <h1>Secure access for administrators and users.</h1>
            <p>Sign in with your credentials. Admin accounts open the dashboard, while user accounts go to the project listing.</p>
        </section>

        <section class="auth-form-wrap" aria-label="Login form">
            <h2>Welcome back</h2>
            <p class="subtitle">Use your registered email and password to continue.</p>

            <?php if ($this->session->flashdata('auth_error')): ?>
                <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('auth_error')); ?></div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('auth_success')): ?>
                <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('auth_success')); ?></div>
            <?php endif; ?>

            <?php if (validation_errors()): ?>
                <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
            <?php endif; ?>

            <?php echo form_open('authenticate', array('autocomplete' => 'off')); ?>
                <div class="field">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo set_value('email'); ?>"
                        placeholder="name@company.com"
                        required
                    >
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <button type="submit" class="submit-btn">Sign In</button>
            <?php echo form_close(); ?>

            <p class="auth-footer">Protected area. Authorized accounts only.</p>
        </section>
    </main>
</body>
</html>
