<?php
/**
 * CMGalaxy Docs Upgrade / Paywall Modal Component
 */

$default_upgrade  = 'https://cmgalaxy.com/book-a-demo';
$upgrade_url = isset($args['upgrade_url']) ? $args['upgrade_url'] : $default_upgrade;
$signin_url  = isset($args['signin_url']) ? $args['signin_url'] : home_url('/signin/');
?>

<div class="cmg-paywall-container" style="margin: 40px 0; padding: 32px; border: 1px solid #bfdbfe; border-radius: 12px; background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%); text-align: center;">
    <div style="max-width: 540px; margin: 0 auto;">
        <div style="width: 48px; height: 48px; margin: 0 auto 16px; background: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #2563eb;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>
        <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Unlock Full Documentation</h3>
        <p style="font-size: 15px; color: #64748b; margin-bottom: 24px; line-height: 1.6;">
            Sign in to your CMGalaxy account or schedule a walkthrough to access this guide, actionable playbooks, and in-depth troubleshooting docs.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo esc_url($signin_url); ?>" style="padding: 10px 24px; background: #3b82f6; color: #ffffff; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14.5px;">Sign In</a>
            <a href="<?php echo esc_url($upgrade_url); ?>" target="_blank" rel="noopener" style="padding: 10px 24px; background: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14.5px;">Book a Demo</a>
        </div>
    </div>
</div>
