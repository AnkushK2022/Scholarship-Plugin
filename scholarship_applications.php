<?php
ob_start(); // Start output buffering
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$table_applications = $wpdb->prefix . 'scholarship_applications';
$table_scholarships = $wpdb->prefix . 'scholarships';

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $wpdb->delete($table_applications, ['id' => $id]);
    wp_redirect(admin_url('admin.php?page=scholarships&tab=applications'));
    exit;
}

$applications = $wpdb->get_results(
    "SELECT sa.*, s.scholarship_title FROM $table_applications sa
    JOIN $table_scholarships s ON sa.scholarship_id = s.id"
);

echo '<div class="wrap"><h2>Applications</h2>';
if ($applications) {
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>
        <th>Applied For</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Birthday</th>
        <th>Document</th>
        <th>Date Applied</th>
        <th>Actions</th>
    </tr></thead>';
    foreach ($applications as $app) {
        echo "<tr>
            <td>{$app->scholarship_title}</td>
            <td>{$app->first_name} {$app->last_name}</td>
            <td>{$app->email}</td>
            <td>{$app->phone}</td>
            <td>{$app->birthday}</td>
            <td><a href='{$app->file}' target='_blank'>View Document</a></td>
            <td>{$app->date_applied}</td>
            <td>
                <a href='?page=scholarships&tab=applications&action=delete&id={$app->id}' class='button button-danger' onclick='return confirm('Are you sure?');'>Delete</a>
            </td>
        </tr>";
    }
    echo '</table>';
} else {
    echo '<p>No applications found.</p>';
}
echo '</div>';