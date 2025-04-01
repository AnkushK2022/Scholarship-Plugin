<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$scholarships_table = $wpdb->prefix . 'scholarships';

// Handle tab switching
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all_scholarships';

// Handle delete scholarship
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['scholarship_id'])) {
    $scholarship_id = intval($_GET['scholarship_id']);
    $wpdb->delete($scholarships_table, ['id' => $scholarship_id]);
    echo '<div class="updated"><p>Scholarship deleted successfully.</p></div>';
}


?>

<h2><?php esc_html_e('Scholarship Management', 'scholarships'); ?></h2>

<h2 class="nav-tab-wrapper">
    <a href="?page=scholarships&tab=all_scholarships" class="nav-tab <?php echo $current_tab == 'all_scholarships' ? 'nav-tab-active' : ''; ?>">All Scholarships</a>
    <a href="?page=scholarships&tab=add_scholarship" class="nav-tab <?php echo $current_tab == 'add_scholarship' ? 'nav-tab-active' : ''; ?>">Add Scholarship</a>
    <a href="?page=scholarships&tab=open_scholarships" class="nav-tab <?php echo $current_tab == 'open_scholarships' ? 'nav-tab-active' : ''; ?>">Open Scholarships</a>
    <a href="?page=scholarships&tab=closed_scholarships" class="nav-tab <?php echo $current_tab == 'closed_scholarships' ? 'nav-tab-active' : ''; ?>">Closed Scholarships</a>
</h2>

<div class="tab-content">
    <?php
    switch ($current_tab) {
        case 'add_scholarship':
            scholarships_add_scholarship_form($wpdb, $scholarships_table);
            break;
        case 'open_scholarships':
            scholarships_list_scholarships($wpdb, $scholarships_table, 1);
            break;
        case 'closed_scholarships':
            scholarships_list_scholarships($wpdb, $scholarships_table, 0);
            break;
        case 'all_scholarships':
        default:
            scholarships_list_scholarships($wpdb, $scholarships_table);
            break;
    }
    ?>
</div>

<?php
// Function to display the "Add Scholarship" form
// Function to display the "Add Scholarship" form
function scholarships_add_scholarship_form($wpdb, $scholarships_table) {
    if (isset($_POST['submit_scholarship'])) {
        $featured_image_url = sanitize_text_field($_POST['featured_image']); // Get the image URL from hidden input

        $data = [
            'scholarship_title' => sanitize_text_field($_POST['scholarship_title']),
            'location' => sanitize_text_field($_POST['location']),
            'tagline' => sanitize_text_field($_POST['tagline']),
            'scholarship_brief' => wp_kses_post($_POST['scholarship_brief']),
            'eligibility' => wp_kses_post($_POST['eligibility']),
            'requirement' => wp_kses_post($_POST['requirement']),
            'application_deadline' => sanitize_text_field($_POST['application_deadline']),
            'featured_image' => $featured_image_url, // Save the image URL
            'status' => 1
        ];

        $wpdb->insert($scholarships_table, $data);
        echo '<div class="updated"><p>Scholarship added successfully.</p></div>';
    }
?>
<form method="POST">
    <table class="form-table">
        <tr>
            <th><label for="scholarship_title">Scholarship Title</label></th>
            <td><input type="text" id="scholarship_title" name="scholarship_title" required></td>
        </tr>
        <tr>
            <th><label for="location">Location</label></th>
            <td><input type="text" id="location" name="location" required></td>
        </tr>
        <tr>
            <th><label for="tagline">Tagline</label></th>
            <td><input type="text" id="tagline" name="tagline"></td>
        </tr>
        <tr>
            <th><label for="application_deadline">Application Deadline</label></th>
            <td><input type="date" id="application_deadline" name="application_deadline" required></td>
        </tr>
        <tr>
            <th><label for="featured_image">Featured Image</label></th>
            <td>
                <input type="hidden" id="featured_image" name="featured_image">
                <img id="featured_image_preview" src="" style="max-width: 200px; display: none;">
                <button type="button" class="button" id="upload_image_button">Select Image</button>
            </td>
        </tr>
        <tr>
            <th><label for="scholarship_brief">Scholarship Brief</label></th>
            <td><?php wp_editor('', 'scholarship_brief', ['textarea_name' => 'scholarship_brief', 'textarea_rows' => 6]); ?></td>
        </tr>
        <tr>
            <th><label for="eligibility">Eligibility Criteria</label></th>
            <td><?php wp_editor('', 'eligibility', ['textarea_name' => 'eligibility', 'textarea_rows' => 6]); ?></td>
        </tr>
        <tr>
            <th><label for="requirement">Requirements</label></th>
            <td><?php wp_editor('', 'requirement', ['textarea_name' => 'requirement', 'textarea_rows' => 6]); ?></td>
        </tr>
    </table>
    <p><input type="submit" name="submit_scholarship" value="Add Scholarship" class="button button-primary"></p>
</form>

<script>
jQuery(document).ready(function($){
    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        var mediaUploader;
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: 'Select Featured Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#featured_image').val(attachment.url);
            $('#featured_image_preview').attr('src', attachment.url).show();
        });

        mediaUploader.open();
    });
});
</script>
<?php
}

// Function to list scholarships
function scholarships_list_scholarships($wpdb, $scholarships_table, $status = null) {
   
?>

<?php
    $query = "SELECT * FROM $scholarships_table WHERE 1=1";
    
    if ($status !== null) {
        $query .= $wpdb->prepare(" AND status = %d", $status);
    }

    $scholarships = $wpdb->get_results($query);

    if ($scholarships) {
?>
    <table class="widefat">
        <thead>
            <tr>
                <th>Scholarship Title</th>
                <th>Location</th>
                <th>Application Deadline</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scholarships as $scholarship) : 
                $deadline = date('Y-m-d', strtotime($scholarship->application_deadline));
            ?>
            <tr>
                <td><?php echo esc_html($scholarship->scholarship_title); ?></td>
                <td><?php echo esc_html($scholarship->location); ?></td>
                <td><?php echo esc_html($deadline); ?></td>
                <td><?php echo $scholarship->status ? 'Open' : 'Closed'; ?></td>
                <td>
                    <a href="?page=scholarships&tab=view_scholarship&scholarship_id=<?php echo $scholarship->id; ?>">View</a> |
                    <a href="?page=scholarships&tab=edit_scholarship&scholarship_id=<?php echo $scholarship->id; ?>">Edit</a> |
                    <a href="?page=scholarships&tab=all_scholarships&action=delete&scholarship_id=<?php echo $scholarship->id; ?>" 
                       onclick="return confirm('Are you sure you want to delete this scholarship?');">
                       Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php
    } else {
        echo '<p>No scholarships found.</p>';
    }
}