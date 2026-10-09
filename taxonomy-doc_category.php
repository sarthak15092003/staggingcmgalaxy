<?php
/**
 * Taxonomy Template for Docs Categories (doc_category)
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$current_term = get_queried_object();
$current_term_id = $current_term && isset($current_term->term_id) ? $current_term->term_id : 0;
$term_name = $current_term && isset($current_term->name) ? $current_term->name : 'Documentation';
$term_desc = $current_term && isset($current_term->description) ? $current_term->description : '';

$custom_icons = array(
    'User Management'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/usermanagement.png',
    'User Manegement'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/usermanagement.png',
    'Account Management'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/account-management-1.png',
    'Account Mangement'         => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/account-management-1.png',
    'Master Dashboard'          => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/master-dashboard.png',
    'Main Dashboard'            => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png',
    'Dashboard'                 => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png',
    'Funnel Attribution'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/funnel.png',
    'Integrations'              => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/integrations.png',
    'Google Dashboard'          => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/google.png',
    'Meta Dashboard'            => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/meta.png',
    'DV360 Dashboard'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/DV360.png',
    'Amazon Dashboard'          => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/amzone.png',
    'Recommendation'            => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/recommendation.png',
    'Pinterest Dashboard'       => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/pinterest.png',
    'Milestone'                 => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/milestonte.png',
    'Notification Center'       => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/notification.png',
    'Ticket/ Support'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/support.png',
    'Tickets / Supports'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/support.png',
    'Reporting Hub'             => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/report.png',
    'Reporting HUb'             => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/report.png',
    'Lex'                       => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/recommendation.png',
    'User Journey'              => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/user-jounery.png',
    'Onboarding'                => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/onboarding.png',
    'Getting started'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/onboarding.png',
    'Getting Started'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/onboarding.png',
    'Linkedin Dashboard'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/linkedin.png',
    'LinkedIn Dashboard'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/linkedin.png',
    'Teads Dashboard'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/teads.png',
    'UTM Parameters Guidelines' => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/UTM-.png'
);

$cat_icon = isset($custom_icons[$term_name]) ? $custom_icons[$term_name] : 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png';
$docs_home_url = get_post_type_archive_link('docs') ? get_post_type_archive_link('docs') : home_url('/docs/');
?>

<div class="docs-main-wrapper" style="padding-top: 32px; padding-bottom: 64px;">
    <div class="docs-container">
        
        <div class="docs-layout-row">
            
            <!-- Left Sidebar Navigation -->
            <div class="docs-col-sidebar category-left-sidebar-col">
                <?php get_template_part('template-parts/docs/sidebar-modern'); ?>
            </div>

            <!-- Main Content Column -->
            <div class="docs-col-content">
                
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="custom-breadcrumb">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <li class="breadcrumb-separator">/</li>
                        <li><a href="<?php echo esc_url($docs_home_url); ?>">Docs</a></li>
                        <li class="breadcrumb-separator">/</li>
                        <li class="active-crumb" aria-current="page"><?php echo esc_html($term_name); ?></li>
                    </ol>
                </nav>

                <!-- Category Header Card -->
                <div style="display: flex; align-items: center; gap: 16px; margin: 20px 0 28px; padding: 20px 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div style="width: 44px; height: 44px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px;">
                        <img src="<?php echo esc_url($cat_icon); ?>" alt="" style="width: 100%; height: 100%; object-fit: contain;" />
                    </div>
                    <div>
                        <h1 style="font-size: 26px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; line-height: 1.2;"><?php echo esc_html($term_name); ?></h1>
                        <?php if ($term_desc) : ?>
                            <p style="margin: 0; color: #64748b; font-size: 14.5px;"><?php echo esc_html($term_desc); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Articles & Subcategories List -->
                <?php get_template_part('template-parts/docs/content-category-detail'); ?>

            </div>

        </div>

    </div>
</div>

<?php
get_footer();
