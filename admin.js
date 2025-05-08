// Admin JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any admin-specific functionality
    console.log('Admin scripts loaded');
    
    // Table row highlighting
    const tableRows = document.querySelectorAll('.data-table tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            this.classList.toggle('highlight');
        });
    });

    // Add this to your script section
document.getElementById('sermonForm').addEventListener('submit', function(e) {
    const audioFile = document.getElementById('audio_file').files[0];
    const videoFile = document.getElementById('video_file').files[0];
    const docFile = document.getElementById('document_file').files[0];
    
    // Audio file validation (100MB)
    if (audioFile && audioFile.size > 100 * 1024 * 1024) {
        alert('Audio file is too large. Maximum size is 100MB.');
        e.preventDefault();
        return;
    }
    
    // Video file validation (500MB)
    if (videoFile && videoFile.size > 500 * 1024 * 1024) {
        alert('Video file is too large. Maximum size is 500MB.');
        e.preventDefault();
        return;
    }
    
    // Document validation (20MB)
    if (docFile && docFile.size > 20 * 1024 * 1024) {
        alert('Document file is too large. Maximum size is 20MB.');
        e.preventDefault();
        return;
    }
});
    
    // Form validation can be added here
});